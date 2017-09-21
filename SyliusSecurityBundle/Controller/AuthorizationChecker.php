<?php

namespace Kaliber5\SyliusSecurityBundle\Controller;

use Kaliber5\LoggerBundle\LoggingTrait\LoggingTrait;
use Sylius\Bundle\ResourceBundle\Controller\AuthorizationCheckerInterface as SyliusAuthorizationCheckerInterface;
use \Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Sylius\Bundle\ResourceBundle\Controller\RequestConfiguration;

/**
 * Custom authorization checker that
 * calls the voter from framework
 */
class AuthorizationChecker implements SyliusAuthorizationCheckerInterface
{
    use LoggingTrait;

    /**
     * @var AuthorizationCheckerInterface
     */
    protected $authorizationChecker;

    /**
     * @param AuthorizationCheckerInterface $authorizationChecker
     */
    public function __construct(AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->authorizationChecker = $authorizationChecker;
    }


    /**
     * {@inheritdoc}
     */
    public function isGranted(RequestConfiguration $requestConfiguration, string $permission): bool
    {
        $this->logDebug('Check Permissions: '.$permission);
        return $this->authorizationChecker->isGranted($permission, $requestConfiguration);
    }
}
