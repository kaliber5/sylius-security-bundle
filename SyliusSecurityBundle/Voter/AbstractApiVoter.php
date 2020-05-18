<?php
namespace Kaliber5\SyliusSecurityBundle\Voter;

use Kaliber5\LoggerBundle\LoggingTrait\LoggingTrait;
use Sylius\Component\Resource\ResourceActions;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @TODO this class was extending the AbstractVoter, removed in sf3.2, and should maybe refactored
 *
 * Class AbstractApiVoter
 *
 * This is an abstract class that supports all
 * crud-actions/attributes on a resource
 *
 * @package Kaliber5\SyliusSecurityBundle\Voter
 */
abstract class AbstractApiVoter extends Voter implements VoterInterface
{
    use LoggingTrait;

    /**
     * returns the crud actions on a resource
     *
     * @return array
     */
    protected function getSupportedAttributes()
    {
        return [
            ResourceActions::CREATE,
            ResourceActions::DELETE,
            ResourceActions::INDEX,
            ResourceActions::SHOW,
            ResourceActions::UPDATE,
        ];
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

    /**
     * This is from the sf2.8 AbstractVoter, to aware compatibility. removed in sf3.2, maybe this should be refactored
     * 
     * Return an array of supported classes. This will be called by supportsClass.
     *
     * @return array an array of supported classes, i.e. array('Acme\DemoBundle\Model\Product')
     */
    abstract protected function getSupportedClasses();
}
