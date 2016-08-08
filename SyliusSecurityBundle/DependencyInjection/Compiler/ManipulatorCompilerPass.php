<?php
/**
 * Created by PhpStorm.
 * User: andreasschacht
 * Date: 19.05.16
 * Time: 18:20
 */

namespace Kaliber5\SyliusSecurityBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class ManipulatorCompilerPass
 *
 * This class adds the tagged manipulator to the container class
 *
 * @package Kaliber5\SyliusSecurityBundle\DependencyInjection\Compiler
 */
class ManipulatorCompilerPass implements CompilerPassInterface
{

    /**
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->has('kaliber5.sylius_security.manipulator')) {
            return;
        }

        $taggedServices = $container->findTaggedServiceIds(
            'k5.security.manipulator'
        );

        foreach ($taggedServices as $id => $tags) {
            foreach ($tags as $attributes) {
                $container->getDefinition('kaliber5.sylius_security.manipulator')
                    ->addMethodCall(
                        'addManipulator',
                        array(
                            new Reference($id),
                            $attributes['resource']
                        )
                    );
            }
        }

    }
}
