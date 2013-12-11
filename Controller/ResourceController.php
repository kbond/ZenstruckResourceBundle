<?php

namespace Zenstruck\ResourceBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Zenstruck\ResourceBundle\Config\Resource;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class ResourceController extends AbstractResourceController
{
    public function listAction(Request $request)
    {
        if ($grid = $this->getGrid($request)) {
            return $this->renderResponse(Resource::ACTION_LIST, array(
                    'grid' => $grid
                ));
        }

        return $this->renderResponse(Resource::ACTION_LIST, array(
                $this->resource->getPluralName(true) => $this->getCollection()
            ));
    }

    public function showAction($id, Request $request)
    {
        return $this->renderResponse(Resource::ACTION_SHOW, array(
                $this->resource->getEntityName(true) => $this->findEntity($id)
            ));
    }

    public function newAction(Request $request)
    {
        return $this->processNew($request);
    }

    public function postAction(Request $request)
    {
        return $this->processNew($request);
    }

    public function editAction($id, Request $request)
    {
        return $this->processEdit($id, $request);
    }

    public function putAction($id, Request $request)
    {
        return $this->processEdit($id, $request);
    }

    public function deleteAction($id, Request $request)
    {
        return $this->processDelete($id, $request);
    }
}
