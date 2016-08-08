<?php
/**
 * Created by PhpStorm.
 * User: andreasschacht
 * Date: 20.05.16
 * Time: 16:09
 */

namespace Kaliber5\SyliusSecurityBundle\Voter;

use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class ActionGrantedVoter
 * @package Kaliber5\SyliusSecurityBundle\Voter
 */
class ActionGrantedVoter extends AbstractRequestConfigurationVoter
{

    /**
     * Perform a single access check operation on a given attribute, object and (optionally) user
     * It is safe to assume that $attribute and $object's class pass supportsAttribute/supportsClass
     * $user can be one of the following:
     *   a UserInterface object (fully authenticated user)
     *   a string               (anonymously authenticated user).
     *
     * @param string $attribute
     * @param object $object
     * @param UserInterface|string $user
     *
     * @return bool
     */
    protected function isGranted($attribute, $object, $user = null)
    {
        return true;
    }
}
