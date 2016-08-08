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
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class SecurityUserManipulator
 * @package Kaliber5\SyliusSecurityBundle\Manipulator
 */
class SecurityUserManipulator implements ResourceProviderManipulatorInterface
{

    use LoggingTrait;

    /**
     * @var TokenStorageInterface
     */
    protected $tokenStorage;

    /**
     * @var string
     */
    protected $userIdField;

    /**
     * @param TokenStorageInterface $tokenStorage
     * @param $userIdField
     */
    public function __construct(TokenStorageInterface $tokenStorage, $userIdField)
    {
        $this->tokenStorage = $tokenStorage;
        $this->userIdField = $userIdField;
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
        $currentUser = 0;
        if ($this->tokenStorage->getToken()->getUser() instanceof UserInterface) {
            $currentUser = $this->tokenStorage->getToken()->getUser()->getId();
        }

        $criteria = $requestConfiguration->getParameters()->get('criteria', array());
        $this->logDebug('Criteria Before: '.print_r($criteria, true));
        $criteria[$this->userIdField] = $currentUser;
        $requestConfiguration->getParameters()->set('criteria', $criteria);
        $this->logDebug('Criteria After: '.print_r($criteria, true));
    }
}
