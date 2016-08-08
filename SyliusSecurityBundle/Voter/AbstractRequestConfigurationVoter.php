<?php
/**
 * Created by PhpStorm.
 * User: andreasschacht
 * Date: 20.05.16
 * Time: 15:49
 */

namespace Kaliber5\SyliusSecurityBundle\Voter;

use Kaliber5\LoggerBundle\LoggingTrait\LoggingTrait;
use Sylius\Bundle\ResourceBundle\Controller\RequestConfiguration;
use Symfony\Component\Security\Core\Authorization\Voter\AbstractVoter;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

/**
 * Class AbstractRequestConfigurationVoter
 * @package Kaliber5\SyliusSecurityBundle\Voter
 */
abstract class AbstractRequestConfigurationVoter extends AbstractVoter implements VoterInterface
{

    use LoggingTrait;

    protected $resource;

    protected $actions = array();

    public function __construct($resource, array $actions = array('index', 'show', 'create', 'update', 'delete'))
    {
        $this->resource = $resource;
        $this->actions = $actions;
    }

    /**
     * Return an array of supported classes. This will be called by supportsClass.
     *
     * @return array an array of supported classes, i.e. array('Acme\DemoBundle\Model\Product')
     */
    protected function getSupportedClasses()
    {
        return array(RequestConfiguration::class);
    }

    /**
     * Return an array of supported attributes. This will be called by supportsAttribute.
     *
     * @return array an array of supported attributes, i.e. array('CREATE', 'READ')
     */
    protected function getSupportedAttributes()
    {
        return array_map(function ($action) {
            return $this->resource.'.'.$action;
        }, $this->actions);
    }

    /**
     * @param $attribute
     * @return string the action e.g. create or update
     * @throws \InvalidArgumentException if the attribute contains no action
     */
    protected function getActionFromAttribute($attribute)
    {
        if (($pos = strrpos($attribute, '.')) !== false) {
            $action = substr($attribute, ($pos+1));
            $this->logDebug('Got action: '.$action.' from '.$attribute);
            return $action;
        }
        throw new \InvalidArgumentException('Attribute: '.$attribute.' contains no action');
    }
}
