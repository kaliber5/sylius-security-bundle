<?php
namespace Kaliber5\SyliusSecurityBundle\Voter;

use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

/**
 * Class GrantedApiVoter
 *
 * this class granted true to all crud actions
 * on the supported classes
 *
 * @package AppBundle\Voter
 */
class GrantedApiVoter extends AbstractApiVoter implements VoterInterface
{

    /**
     * @var array
     */
    private $supportedClasses;

    /**
     * GrantedApiVoter constructor.
     *
     * @param array $supportedClasses
     */
    public function __construct(array $supportedClasses)
    {
        $this->supportedClasses = $supportedClasses;
    }

    /**
     * Return an array of supported classes. This will be called by supportsClass.
     *
     * @return array an array of supported classes, i.e. array('Acme\DemoBundle\Model\Product')
     */
    protected function getSupportedClasses()
    {
        return $this->supportedClasses;
    }

    /**
     * @param string $attribute
     * @param object $object
     * @param null $user
     * @return bool
     */
    protected function isGranted($attribute, $object, $user = null)
    {
        return true;
    }
}
