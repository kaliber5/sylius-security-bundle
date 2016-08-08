<?php
namespace Kaliber5\SyliusSecurityBundle\Voter;

use Kaliber5\LoggerBundle\LoggingTrait\LoggingTrait;
use Sylius\Component\Resource\ResourceActions;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;
use Symfony\Component\Security\Core\Authorization\Voter\AbstractVoter;

/**
 * Class AbstractApiVoter
 *
 * This is an abstract class that supports all
 * crud-actions/attributes on a resource
 *
 * @package Kaliber5\SyliusSecurityBundle\Voter
 */
abstract class AbstractApiVoter extends AbstractVoter implements VoterInterface
{
    use LoggingTrait;

    /**
     * returns the crud actions on a resource
     *
     * @return array
     */
    protected function getSupportedAttributes()
    {
        return array(
            ResourceActions::CREATE,
            ResourceActions::DELETE,
            ResourceActions::INDEX,
            ResourceActions::SHOW,
            ResourceActions::UPDATE
        );
    }
}
