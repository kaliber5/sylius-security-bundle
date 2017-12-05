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
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

/**
 * @TODO this class was extending the AbstractVoter, removed in sf3.2, and should maybe refactored
 *
 *
 * Class AbstractRequestConfigurationVoter
 * @package Kaliber5\SyliusSecurityBundle\Voter
 */
abstract class AbstractRequestConfigurationVoter extends Voter implements VoterInterface
{

    use LoggingTrait;

    protected $resource;

    protected $actions = array();

    /**
     * AbstractRequestConfigurationVoter constructor.
     *
     * @param object $resource
     * @param array  $actions
     */
    public function __construct($resource, array $actions = array('index', 'show', 'create', 'update', 'delete'))
    {
        $this->resource = $resource;
        $this->actions = $actions;
    }

    /**
     * This is from the sf2.8 AbstractVoter, to aware compatibility. removed in sf3.2, maybe this should be refactored
     *
     * Return an array of supported classes. This will be called by supportsClass.
     *
     * @return array an array of supported classes, i.e. array('Acme\DemoBundle\Model\Product')
     */
    protected function getSupportedClasses()
    {
        return array(RequestConfiguration::class);
    }

    /**
     * This is from the sf2.8 AbstractVoter, to aware compatibility. removed in sf3.2, maybe this should be refactored
     *
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

    /**
     * {@inheritDoc}
     */
    protected function supports($attribute, $subject)
    {
        if (is_object($subject) && $this->supportsClass(get_class($subject))) {
            return $this->supportsAttribute($attribute);
        } else {
            return false;
        }
    }

    /**
     * {@inheritDoc}
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        return $this->isGranted($attribute, $subject, $token->getUser());
    }

    /**
     * This is from the sf2.8 AbstractVoter, removed in sf3.2, maybe this should be refactored
     */
    protected function supportsAttribute($attribute)
    {
        return in_array($attribute, $this->getSupportedAttributes());
    }

    /**
     * This is from the sf2.8 AbstractVoter, removed in sf3.2, maybe this should be refactored
     */
    protected function supportsClass($class)
    {
        foreach ($this->getSupportedClasses() as $supportedClass) {
            if ($supportedClass === $class || is_subclass_of($class, $supportedClass)) {
                return true;
            }
        }

        return false;
    }

    /**
     * This is from the sf2.8 AbstractVoter, to aware compatibility. removed in sf3.2, maybe this should be refactored
     *
     * Perform a single access check operation on a given attribute, object and (optionally) user
     * It is safe to assume that $attribute and $object's class pass supportsAttribute/supportsClass
     * $user can be one of the following:
     *   a UserInterface object (fully authenticated user)
     *   a string               (anonymously authenticated user).
     *
     * @param string               $attribute
     * @param object               $object
     * @param UserInterface|string $user
     *
     * @return bool
     */
    abstract protected function isGranted($attribute, $object, $user = null);

}
