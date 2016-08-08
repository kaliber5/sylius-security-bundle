<?php

namespace Kaliber5\SyliusSecurityBundle;

use Kaliber5\SyliusSecurityBundle\DependencyInjection\Compiler\ManipulatorCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Class Kaliber5SyliusSecurityBundle
 * @package Kaliber5\SyliusSecurityBundle
 */
class Kaliber5SyliusSecurityBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $container->addCompilerPass(new ManipulatorCompilerPass());
    }
}
