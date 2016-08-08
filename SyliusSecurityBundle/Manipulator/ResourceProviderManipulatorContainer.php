<?php
/**
 * Created by PhpStorm.
 * User: andreasschacht
 * Date: 19.05.16
 * Time: 15:17
 */

namespace Kaliber5\SyliusSecurityBundle\Manipulator;

use Kaliber5\LoggerBundle\LoggingTrait\LoggingTrait;
use Sylius\Bundle\ResourceBundle\Controller\RequestConfiguration;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 *
 * Class ResourceProviderManipulatorContainer
 *
 * This class contains a collection of the manipulators for different namespaces. The namespace is the alias of the resource.
 *
 * @package Kaliber5\SyliusSecurityBundle\Manipulator
 */
class ResourceProviderManipulatorContainer implements ResourceProviderManipulatorInterface
{

    use LoggingTrait;

    /**
     * An array of manipulators
     *
     * @var array
     */
    protected $manipulators = array();

    /**
     * Adds a manipulator for a namespace. The namespace must match the alias of the resource to process the configuration
     *
     * @param ResourceProviderManipulatorInterface $manipulator
     * @param string                               $namespace
     */
    public function addManipulator(ResourceProviderManipulatorInterface $manipulator, $namespace)
    {
        $this->logDebug('adding Manipulator for: '.$namespace);
        if (!is_string($namespace)) {
            throw new \InvalidArgumentException('The namespace must be a string');
        }
        if (!array_key_exists($namespace, $this->manipulators)) {
            $this->manipulators[$namespace] = array();
        }
        $this->manipulators[$namespace][] = $manipulator;
    }



    /**
     * Processes the Manipulation
     *
     * @param RequestConfiguration $requestConfiguration
     * @param RepositoryInterface  $repository
     *
     */
    public function process(RequestConfiguration $requestConfiguration, RepositoryInterface $repository)
    {
        if (!array_key_exists($requestConfiguration->getMetadata()->getAlias(), $this->manipulators)) {
            return;
        }
        foreach ($this->manipulators[$requestConfiguration->getMetadata()->getAlias()] as $manipulator) {
            $manipulator->process($requestConfiguration, $repository);
        }
    }
}
