<?php

namespace Zenstruck\ResourceBundle\Routing;

use Symfony\Component\Config\Loader\Loader;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use Zenstruck\ResourceBundle\Config\Resource;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class ResourceLoader extends Loader
{
    /**
     * @var \Zenstruck\ResourceBundle\Config\Resource[]
     */
    protected $resources = array();

    public function addConfig(Resource $resource)
    {
        $this->resources[] = $resource;
    }

    public function load($resource, $type = null)
    {
        $collection = new RouteCollection();

        foreach ($this->resources as $resource) {
            $routes = new RouteCollection();

            // list
            $this->addRoute($routes, $resource, Resource::ACTION_LIST);

            // show
            $this->addRoute($routes, $resource, Resource::ACTION_SHOW, '/{id}');

            // new
            $this->addRoute($routes, $resource, Resource::ACTION_NEW, '/new');

            // post
            $this->addRoute($routes, $resource, Resource::ACTION_POST, '', 'POST');

            // edit
            $this->addRoute($routes, $resource, Resource::ACTION_EDIT, '/{id}/edit');

            // put
            $this->addRoute($routes, $resource, Resource::ACTION_PUT, '/{id}', 'PUT');

            // put
            $this->addRoute($routes, $resource, Resource::ACTION_DELETE, '/{id}', 'DELETE');

            // extra routes
            foreach ($resource->getRouting()->getExtraRoutes() as $name => $extraRoute) {
                // TODO add more error checking for this logic

                $pattern = sprintf('/%s%s.{_format}', $resource->getPluralName(true), $extraRoute['pattern']);

                $defaults = array(
                    '_controller' => sprintf('%s:%sAction', $resource->getServiceId(), $name),
                    '_format' => $extraRoute['default_format']
                );

                $requirements = array(
                    '_method' => $extraRoute['methods'],
                    '_format' => $extraRoute['formats']
                );

                $routes->add(sprintf('%s_%s', $name, $resource->getEntityName(true)), new Route($pattern, $defaults, $requirements));
            }

            $routes->addPrefix($resource->getRouting()->getPrefix());
            $collection->addCollection($routes);
        }

        return $collection;
    }

    public function supports($resource, $type = null)
    {
        return $type === 'zenstruck_resource';
    }

    protected function addRoute(RouteCollection $routes, Resource $resource, $action, $pattern = '', $method = 'GET')
    {
        $routing = $resource->getRouting();

        if (in_array($action, $routing->getDisabledActions())) {
            return;
        }

        $pattern = sprintf('/%s%s.{_format}', $resource->getPluralName(true), $pattern);

        $defaults = array(
            '_controller' => sprintf('%s:%sAction', $resource->getServiceId(), $action),
            '_format' => $routing->getDefaultFormat()
        );

        $requirements = array(
            '_method' => $method,
            '_format' => $routing->getFormats()
        );

        $routes->add($resource->getRouteName($action), new Route($pattern, $defaults, $requirements));
    }
}