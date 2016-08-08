<?php
/**
 * Created by PhpStorm.
 * User: andreasschacht
 * Date: 19.05.16
 * Time: 15:10
 */

namespace Kaliber5\SyliusSecurityBundle\Manipulator;

use Sylius\Bundle\ResourceBundle\Controller\RequestConfiguration;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * Instances of this interface should manipulate the requestConfiguration (e.g. for security reasons)
 *
 * Interface ResourceProviderManipulatorInterface
 * @package Kaliber5\SyliusSecurityBundle\Manipulator
 */
interface ResourceProviderManipulatorInterface
{
    /**
     * Processes the Manipulation
     *
     * @param RequestConfiguration $requestConfiguration
     * @param RepositoryInterface  $repository
     *
     */
    public function process(RequestConfiguration $requestConfiguration, RepositoryInterface $repository);
}
