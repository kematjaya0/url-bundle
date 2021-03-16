<?php

/**
 * This file is part of the url-bundle.
 */

namespace Kematjaya\URLBundle\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Security\Core\Role\RoleHierarchyInterface;
use Kematjaya\URLBundle\Factory\RoutingFactoryInterface;
use Kematjaya\URLBundle\Source\RoutingSourceInterface;
use Kematjaya\UserBundle\Entity\KmjUserInterface;

/**
 * @package App\Console
 * @license https://opensource.org/licenses/MIT MIT
 * @author  Nur Hidayatullah <kematjaya0@gmail.com>
 */
class RoutingCommand extends Command
{
    protected static $defaultName = 'url:configure';
    
    /**
     * 
     * @var RoutingFactoryInterface
     */
    private $routingFactory;
    
    /**
     * 
     * @var RoleHierarchyInterface
     */
    private $roleHierarchy;
    
    /**
     * 
     * @var RoutingSourceInterface
     */
    private $routingSource;
    
    public function __construct(RoutingSourceInterface $routingSource, RoutingFactoryInterface $routingFactory, RoleHierarchyInterface $roleHierarchy, mixed $name = null) 
    {
        $this->routingFactory = $routingFactory;
        $this->roleHierarchy = $roleHierarchy;
        $this->routingSource = $routingSource;
        
        parent::__construct($name);
    }
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->routingFactory->setBasePath('/backend/');
        $roles = $this->roleHierarchy->getReachableRoleNames([KmjUserInterface::ROLE_SUPER_USER]);
        $resultSets = [];
        foreach ($this->routingFactory->build() as $name => $credential) {
            $resultSets[$name] = $roles;
        }
        
        $this->routingSource->dump($resultSets);
        $output->writeln(sprintf("write %s rows success", count($resultSets)));
        
        return Command::SUCCESS;
    }
}