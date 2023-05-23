<?php

namespace Kematjaya\URLBundle\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Security\Core\Role\RoleHierarchyInterface;
use Kematjaya\URLBundle\Factory\RoutingFactoryInterface;
use Kematjaya\URLBundle\Source\RoutingSourceInterface;
use Kematjaya\UserBundle\Entity\KmjUserInterface;

/**
 * @package Kematjaya\URLBundle\Console
 * @license https://opensource.org/licenses/MIT MIT
 * @author  Nur Hidayatullah <kematjaya0@gmail.com>
 */
class RoutingCommand extends Command
{
    protected static $defaultName = 'url:configure';

    private RoutingFactoryInterface $routingFactory;

    private RoleHierarchyInterface $roleHierarchy;

    private RoutingSourceInterface $routingSource;

    public function __construct(RoutingSourceInterface $routingSource, RoutingFactoryInterface $routingFactory, RoleHierarchyInterface $roleHierarchy, mixed $name = null)
    {
        $this->routingFactory = $routingFactory;
        $this->roleHierarchy = $roleHierarchy;
        $this->routingSource = $routingSource;

        parent::__construct($name);
    }


    protected function execute(InputInterface $input, OutputInterface $output):int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title("Collect route path");

        $path = $io->askQuestion(new Question("insert base url", "/"));
        $this->routingFactory->setBasePath($path);
        $roles = $this->roleHierarchy->getReachableRoleNames([KmjUserInterface::ROLE_SUPER_USER]);
        $resultSets = [];
        foreach ($this->routingFactory->build()->getKeys() as $name) {
            $resultSets[$name] = $roles;
        }

        try {
            $updated = $this->routingSource->dump($resultSets);
        } catch (\Exception $ex) {
            $io->error($ex->getMessage());

            return self::FAILURE;
        }

        $io->success(
            sprintf("success update %s route in file: %s", $updated, $this->routingSource->getPath())
        );

        return self::SUCCESS;
    }
}
