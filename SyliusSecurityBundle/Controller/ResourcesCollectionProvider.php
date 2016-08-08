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

use Hateoas\Configuration\Route;
use Hateoas\Representation\CollectionRepresentation;
use Hateoas\Representation\Factory\PagerfantaFactory;
use Hateoas\Representation\PaginatedRepresentation;
use Kaliber5\LoggerBundle\LoggingTrait\LoggingTrait;
use Kaliber5\SyliusSecurityBundle\Manipulator\ResourceProviderManipulatorInterface;
use Pagerfanta\Pagerfanta;
use Sylius\Bundle\ResourceBundle\Controller\RequestConfiguration;
use Sylius\Bundle\ResourceBundle\Controller\ResourcesCollectionProviderInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Bundle\ResourceBundle\Controller\ResourcesCollectionProvider as BaseResourcesCollectionProvider;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * Class ResourcesCollectionProvider
 *
 * This class extends the SyliusResourcesCollectionProvider by security checks
 *
 * @package Kaliber5\SyliusSecurityBundle\Controller
 */
class ResourcesCollectionProvider extends BaseResourcesCollectionProvider implements ResourcesCollectionProviderInterface
{
    use LoggingTrait;
    use PermissionTrait;

    /**
     * @var ResourceProviderManipulatorInterface
     */
    protected $manipulator;

    /**
     * @var PagerfantaFactory
     */
    protected $pagerfantaRepresentationFactory;

    /**
     * @var AuthorizationCheckerInterface
     */
    protected $authorizationChecker;

    /**
     * @param PagerfantaFactory                    $pagerfantaRepresentationFactory
     * @param ResourceProviderManipulatorInterface $manipulator
     * @param AuthorizationCheckerInterface        $authorizationChecker
     */
    public function __construct(PagerfantaFactory $pagerfantaRepresentationFactory, ResourceProviderManipulatorInterface $manipulator, AuthorizationCheckerInterface $authorizationChecker)
    {
        parent::__construct($pagerfantaRepresentationFactory);
        $this->pagerfantaRepresentationFactory = $pagerfantaRepresentationFactory;
        $this->manipulator = $manipulator;
        $this->authorizationChecker = $authorizationChecker;
    }

    /**
     * {@inheritdoc}
     */
    public function get(RequestConfiguration $requestConfiguration, RepositoryInterface $repository)
    {
        $this->logDebug('Call Manipulators for alias: '.$requestConfiguration->getMetadata()->getAlias());
        $this->manipulator->process($requestConfiguration, $repository);
        $permission = $this->getPermission($requestConfiguration->getRequest());

        if (empty($requestConfiguration->getCriteria())) {
            $resources = parent::get($requestConfiguration, $repository);
        } else {
            $resources = $this->getResources($requestConfiguration, $repository);

            if ($resources instanceof Pagerfanta) {
                $resources->getCurrentPageResults();
                $request = $requestConfiguration->getRequest();
                $resources->setMaxPerPage($requestConfiguration->getPaginationMaxPerPage());
                $resources->setCurrentPage($request->query->get('page', 1));

                if (!$requestConfiguration->isHtmlRequest()) {
                    $route = new Route($request->attributes->get('_route'), array_merge($request->attributes->get('_route_params'), $request->query->all()));
                    $resources = $this->pagerfantaRepresentationFactory->createRepresentation($resources, $route);
                }
            }
        }
        $this->checkResources($resources, $permission);
        return $resources;
    }

    /**
     * checks the permission on single resources
     *
     * @param $resources
     * @param $permission
     */
    protected function checkResources($resources, $permission)
    {
        if (is_object($resources)) {
            $this->logDebug('Resource instance of '.get_class($resources));
        }
        if ($resources instanceof PaginatedRepresentation) {
            $resources = $resources->getInline();
        }
        if ($resources instanceof CollectionRepresentation) {
            $resources = $resources->getResources();
        }
        if ($resources instanceof Pagerfanta) {
            $resources = $resources->getCurrentPageResults();
        }

        $this->logDebug('Check Resources for permission: '.$permission);
        if (is_object($resources)) {
            $this->logDebug('Resource instance of '.get_class($resources));
        }
        foreach ($resources as $resource) {
            if ($this->authorizationChecker->isGranted($permission, $resource) === false) {
                throw new AccessDeniedHttpException();
            }
        }
    }

    /**
     * @param RequestConfiguration $requestConfiguration
     * @param RepositoryInterface $repository
     * @return array|mixed
     */
    protected function getResources(RequestConfiguration $requestConfiguration, RepositoryInterface $repository)
    {
        if (null !== $repositoryMethod = $requestConfiguration->getRepositoryMethod()) {
            $callable = [$repository, $repositoryMethod];

            $resources = call_user_func_array($callable, $requestConfiguration->getRepositoryArguments());

            return $resources;
        }

        if (!$requestConfiguration->isPaginated()) {
            return $repository->findBy($requestConfiguration->getCriteria(), $requestConfiguration->getSorting(), $requestConfiguration->getLimit());
        }

        return $repository->createPaginator($requestConfiguration->getCriteria(), $requestConfiguration->getSorting());
    }
}
