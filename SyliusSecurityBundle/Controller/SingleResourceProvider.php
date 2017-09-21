<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Kaliber5\SyliusSecurityBundle\Controller;

use Sylius\Component\Resource\Model\ResourceInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Kaliber5\LoggerBundle\LoggingTrait\LoggingTrait;
use Kaliber5\SyliusSecurityBundle\Manipulator\ResourceProviderManipulatorInterface;
use Sylius\Bundle\ResourceBundle\Controller\SingleResourceProvider as SyliusSingleResourceProvider;
use Sylius\Bundle\ResourceBundle\Controller\RequestConfiguration;
use Sylius\Bundle\ResourceBundle\Controller\SingleResourceProviderInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * Class SingleResourceProvider
 *
 * This class extends the SyliusSingleResourceProvider by security checks
 * @package Kaliber5\SyliusSecurityBundle\Controller
 */
class SingleResourceProvider implements SingleResourceProviderInterface
{
    use PermissionTrait;
    use LoggingTrait;

    /**
     * @var ResourceProviderManipulatorInterface
     */
    protected $manipulator;

    /**
     * @var AuthorizationCheckerInterface
     */
    protected $authorizationChecker;

    /**
     * @param ResourceProviderManipulatorInterface $manipulator
     * @param AuthorizationCheckerInterface        $authorizationChecker
     */
    public function __construct(ResourceProviderManipulatorInterface $manipulator, AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->manipulator = $manipulator;
        $this->authorizationChecker = $authorizationChecker;
    }

    /**
     * {@inheritdoc}
     */
    public function get(RequestConfiguration $requestConfiguration, RepositoryInterface $repository): ?ResourceInterface
    {
        $resource = null;
        $this->logDebug('Call Manipulators for alias: '.$requestConfiguration->getMetadata()->getAlias());
        $this->manipulator->process($requestConfiguration, $repository);
        if (empty($requestConfiguration->getCriteria())) {
            $resource = $this->syliusGet($requestConfiguration, $repository);
        } else {
            $criteria = $requestConfiguration->getCriteria();

            $request = $requestConfiguration->getRequest();
            if ($request->attributes->has('slug')) {
                $criteria['slug'] = $request->attributes->get('slug');
            } elseif ($request->attributes->has('id')) {
                $criteria['id'] = $request->attributes->get('id');
            }
            $resource = $repository->findOneBy($criteria);
        }

        if ($resource !== null) {
            $this->logDebug('Is Resource granted: '.get_class($resource).' -> '.$this->getPermission($requestConfiguration->getRequest()));
            if (!$this->authorizationChecker->isGranted($this->getPermission($requestConfiguration->getRequest()), $resource)) {
                throw new AccessDeniedHttpException();
            }
        }
        return $resource;
    }

    /**
     * Cause The ResourceProvider is final now, we cant inherit from and use it directly
     *
     * @param RequestConfiguration $requestConfiguration
     * @param RepositoryInterface  $repository
     *
     * @return null|object|\Sylius\Component\Resource\Model\ResourceInterface
     */
    protected function syliusGet(RequestConfiguration $requestConfiguration, RepositoryInterface $repository)
    {
        $resourceProvider = new SyliusSingleResourceProvider();
        return $resourceProvider->get($requestConfiguration, $repository);
    }
}
