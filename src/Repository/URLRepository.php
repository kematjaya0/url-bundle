<?php

/**
 * This file is part of the cash-in.
 */

namespace Kematjaya\URLBundle\Repository;

use Kematjaya\URLBundle\Source\RoutingSourceInterface;

/**
 * @package Kematjaya\MenuBundle\Repository
 * @license https://opensource.org/licenses/MIT MIT
 * @author  Nur Hidayatullah <kematjaya0@gmail.com>
 */
class URLRepository implements URLRepositoryInterface
{
    /**
     *
     * @var RoutingSourceInterface
     */
    private $routingSource;


    public function __construct(RoutingSourceInterface $routingSource)
    {
        $this->routingSource = $routingSource;
    }

    public function findAll(string $role):array
    {
        $routers = $this->routingSource->getAll();
        $groups = $this->getIndexRoutes($routers);
        $values = $this->groupingRoutes($routers, $groups, $role);

        return $values;
    }

    public function save(array $routers):void
    {
        $this->routingSource->dump($routers);
    }

    /**
     * Get Index Routes
     * @param array $routers
     * @return array
     */
    protected function getIndexRoutes(array $routers):array
    {
        return array_filter($routers, function ($row) {

            return preg_match("/index\z/i", $row);
        }, ARRAY_FILTER_USE_KEY);
    }


    protected function groupingRoutes(array $routers, array $groups, string $role):array
    {
        $values = [];
        foreach ($groups as $k => $group) {
            $name = str_replace("_index", '', $k);
            $values[$name] = $this->checkRole(
                $this->filtering($k, $routers, $groups),
                $role
            );
        }
        $values = array_filter($values, function ($value) {
            return !empty($value);
        });

        $containtOther = $this->findContainingOthers($values);
        foreach ($containtOther as $k => $containt) {
            foreach ($values as $routeName => $val) {
                if ($k === $routeName) {
                    continue;
                }

                $similiar = array_intersect_key($val, $containt);
                if (empty($similiar)) {
                    continue;
                }

                // remove similiar keys
                $similiar[$routeName . '_index'] = true;
                foreach ($similiar as $name => $v) {
                    if (!isset($values[$k][$name])) {
                        continue;
                    }

                    unset($values[$k][$name]);
                }
            }
        }

        return $values;
    }

    protected function findContainingOthers(array $routers):array
    {
        return array_filter($routers, function ($value) {
            foreach ($value as $routeName => $v) {
                if (preg_match("/index\z/i", $routeName)) {

                    return true;
                }
            }

            return false;
        });
    }

    protected function filtering(string $k, array $routers, array $groups):array
    {
        $name = str_replace("_index", '', $k);
        $result = [];
        if (isset($routers[$k])) {
            $result[$k] = $routers[$k];
        }
        $filtered = array_filter($routers, function ($key) use ($k, $name, $groups) {

            if ($key === $k) {
                return false;
            }

            if (!preg_match("/^" . $name . "/i", $key)) {
                return false;
            }

            $keys = explode("_", $key);
            $baseKey = array_slice($keys, 0,count($keys)-1);
            if (!in_array("index", $keys)) {
                $otherIndex = implode("_", $baseKey) . "_index";
                if (!isset($groups[$otherIndex])) {
                    return true;
                }

                return ($name === implode("_", $baseKey));
            }

            if ($name !== implode("_", $baseKey)) {
                return false;
            }

        }, ARRAY_FILTER_USE_KEY);

        return array_merge($result, $filtered);
    }

    protected function checkRole(array $values, string $role):array
    {
        $results = [];
        foreach ($values as $n => $roles) {
            $results[$n] = in_array($role, $roles);
        }

        return $results;
    }
}
