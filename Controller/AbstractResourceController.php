<?php

namespace Zenstruck\ResourceBundle\Controller;

use Doctrine\Common\Inflector\Inflector;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Zenstruck\DataGridBundle\Grid;
use Zenstruck\ResourceBundle\Config\Resource;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
abstract class AbstractResourceController
{
    protected $resource;
    protected $routing;
    protected $util;

    protected $entityClass;

    public function __construct(Resource $resource, ControllerUtil $util)
    {
        $this->resource = $resource;
        $this->routing = $resource->getRouting();
        $this->util = $util;

        if (class_exists($class = $this->resource->getEntity())) {
            $this->entityClass = $class;
        } else {
            $this->entityClass = $this->util->getEntityManager()->getClassMetadata($this->resource->getEntity())->getName();
        }
    }

    protected function processNew(Request $request)
    {
        $action = Resource::ACTION_NEW;
        $this->checkPermissions($action);

        $entity = $this->createEntity();
        $form = $this->createForm($action, $entity);

        if ('POST' === $request->getMethod()) {
            if ($form->submit($request)->isValid()) {
                $this->saveEntity($entity, $action);

                return $this->postRedirect($action);
            }
        }

        return $this->renderResponse($action, array('form' => $form->createView()));
    }

    protected function processEdit($id, Request $request)
    {
        $action = Resource::ACTION_EDIT;
        $this->checkPermissions($action);

        $entity = $this->findEntity($id);
        $form = $this->createForm($action, $entity);

        if ('PUT' === $request->getMethod()) {
            if ($form->submit($request)->isValid()) {
                $this->saveEntity($entity, $action);

                return $this->postRedirect($action);
            }
        }

        return $this->renderResponse($action, array(
                'form' => $form->createView(),
                $this->resource->getEntityName(true) => $entity
            ));
    }

    protected function processDelete($id, Request $request)
    {
        $action = Resource::ACTION_DELETE;
        $this->checkPermissions($action);

        $entity = $this->findEntity($id);
        $this->deleteEntity($entity);

        return $this->postRedirect($action);
    }

    /**
     * @param null|string $action
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    protected function postRedirect($action = null)
    {
        return $this->util->redirect($this->util->generateUrl($this->resource->getDefaultRoute()));
    }

    protected function checkPermissions($action)
    {
        $permissions = $this->resource->getPermissions();

        switch ($permissions) {
            case Resource::PERMISSION_NONE:
                return;

            case Resource::PERMISSION_SIMPLE:
                $role = sprintf('ROLE_%s_ADMIN', strtoupper(Inflector::tableize($this->resource->getEntityName())));
                break;

            default:
                $role = sprintf(
                    'ROLE_%s_%s',
                    strtoupper(Inflector::tableize($this->resource->getEntityName())),
                    strtoupper($action)
                );
                break;
        }

        if (!$this->util->get('security.context')->isGranted($role)) {
            throw new AccessDeniedException();
        }
    }

    /**
     * @param Request $request
     *
     * @return null|Grid
     */
    protected function getGrid(Request $request)
    {
        $gridId = str_replace('controller', 'grid', $this->resource->getServiceId());

        if ($this->util->has($gridId)) {
            $grid = $this->util->get($gridId);

            if ($grid instanceof Grid) {
                return $grid->execute();
            }
        }

        return null;
    }

    /**
     * @param object      $entity
     * @param null|string $action
     */
    protected function saveEntity($entity, $action = null)
    {
        $this->util->persist($entity);
        $this->util->addFlash($this->getFlashMessage($action));
    }

    /**
     * @param object $entity
     */
    protected function deleteEntity($entity)
    {
        $this->util->delete($entity);
        $this->util->addFlash($this->getFlashMessage(Resource::ACTION_DELETE));
    }

    /**
     * @param string $action
     * @param array  $data
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function renderResponse($action, $data = array())
    {
        return $this->util->render($this->getTemplateName($action), $data);
    }

    /**
     * @param string $action
     *
     * @return string
     */
    protected function getTemplateName($action)
    {
        return sprintf('%s:%s.html.twig', $this->resource->getEntity(), $action);
    }

    /**
     * @param string $action
     *
     * @return string
     */
    protected function getFlashMessage($action)
    {
        return sprintf('%s.flash.%s', strtolower($this->resource->getEntityName()), $action);
    }

    /**
     * @return mixed
     */
    protected function getCollection()
    {
        return $this->util->getRepository($this->resource->getEntity())->findAll();
    }

    /**
     * @param $id
     *
     * @return object
     *
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    protected function findEntity($id)
    {
        $entity = $this->util->getRepository($this->resource->getEntity())->find($id);

        if (!$entity) {
            throw $this->util->createNotFoundException(
                sprintf('Entity "%s" with the id "%s" not found.', $this->resource->getEntity(), $id)
            );
        }

        return $entity;
    }

    /**
     * @return object
     */
    protected function createEntity()
    {
        return new $this->entityClass;
    }

    /**
     * @param $action
     * @param $entity
     *
     * @return Form
     */
    protected function createForm($action, $entity)
    {
        $class = $this->resource->getFormClass();

        if (!$class) {
            // use best practice naming
            $class = str_replace('\\Entity\\', '\\Form\\', $this->entityClass).'Type';
        }

        $form = new $class;

        return $this->util->createForm($form, $entity);
    }
}