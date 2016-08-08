<?php
/**
 * Created by PhpStorm.
 * User: andreasschacht
 * Date: 19.05.16
 * Time: 17:15
 */

namespace Kaliber5\SyliusSecurityBundle\Manipulator;

use Kaliber5\LoggerBundle\LoggingTrait\LoggingTrait;
use Sylius\Bundle\ResourceBundle\Controller\RequestConfiguration;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * Class PropertyManipulator
 * @package Kaliber5\SyliusSecurityBundle\Manipulator
 */
class PropertyManipulator implements ResourceProviderManipulatorInterface
{

    use LoggingTrait;


    /**
     * @var array
     */
    protected $properties;


    /**
     * PropertyManipulator constructor.
     * @param array $properties
     */
    public function __construct(array $properties)
    {
        $this->properties = $properties;
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
        $criteria = $requestConfiguration->getParameters()->get('criteria', array());
        $this->logDebug('Criteria Before: '.print_r($criteria, true));
        $criteria = array_merge($criteria, $this->properties);
        $this->logDebug('Criteria After: '.print_r($criteria, true));
        $requestConfiguration->getParameters()->set('criteria', $criteria);

    }
}
