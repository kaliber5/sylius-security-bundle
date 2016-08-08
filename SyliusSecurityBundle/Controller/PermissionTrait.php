<?php
/**
 * Created by PhpStorm.
 * User: andreasschacht
 * Date: 23.05.16
 * Time: 10:47
 */

namespace Kaliber5\SyliusSecurityBundle\Controller;

use Sylius\Component\Resource\ResourceActions;
use Symfony\Component\HttpFoundation\Request;

/**
 * trait PermissionTrait
 *
 * this trait provides the Resrouce Action from the request
 *
 * @package Kaliber5\SyliusSecurityBundle\Controller
 */
trait PermissionTrait
{

    /**
     * Returns the ResourceActions from RequestMethod
     * @param Request $request
     * @return string
     */
    protected function getPermission(Request $request)
    {
        switch ($request->getMethod()) {
            case Request::METHOD_GET:
                if ($request->attributes->has('id')) {
                    return ResourceActions::SHOW;
                } else {
                    return ResourceActions::INDEX;
                }
                break;
            case Request::METHOD_DELETE:
                return ResourceActions::DELETE;
                break;
            case Request::METHOD_POST:
                return ResourceActions::CREATE;
                break;
            case Request::METHOD_PATCH:
            case Request::METHOD_PUT:
                return ResourceActions::UPDATE;
            default:
                return null;
        }
    }
}
