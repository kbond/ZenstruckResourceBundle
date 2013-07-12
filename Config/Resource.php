<?php

namespace Zenstruck\ResourceBundle\Config;

use Doctrine\Common\Inflector\Inflector;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class Resource
{
    // For use when default_route controller option is null
    const DEFAULT_ROUTE = 'homepage';

    const ACTION_LIST   = 'list';
    const ACTION_SHOW   = 'show';
    const ACTION_NEW    = 'new';
    const ACTION_POST   = 'post';
    const ACTION_EDIT   = 'edit';
    const ACTION_PUT    = 'put';
    const ACTION_DELETE = 'delete';

    protected $entity;
    protected $formClass;
    protected $entityName;
    protected $pluralName;
    protected $serviceId;
    protected $defaultRoute;

    /** @var Routing */
    protected $routing;

    public function __construct(array $config)
    {
        $this->entity = $config['entity'];
        $this->formClass = $config['form_class'];
        $this->serviceId = $config['service_id'];
        $this->defaultRoute = $config['default_route'];
        $this->routing = new Routing($config['routing']);

        // get class name without namespace
        preg_match('#([\w]+)$#', $this->entity, $matches);
        $this->entityName = $matches[1];

        $this->pluralName = Inflector::pluralize($this->entityName);
    }

    public function getEntity()
    {
        return $this->entity;
    }

    public function getEntityName($lowerCase = false)
    {
        if ($lowerCase) {
            return strtolower($this->entityName);
        }

        return $this->entityName;
    }

    public function getFormClass()
    {
        return $this->formClass;
    }

    public function getPluralName($lowerCase = false)
    {
        if ($lowerCase) {
            return strtolower($this->pluralName);
        }

        return $this->pluralName;
    }

    public function getServiceId()
    {
        return $this->serviceId;
    }

    public function getDefaultRoute()
    {
        if ($this->defaultRoute) {
            return $this->defaultRoute;
        }

        // use list route if it is enabled
        if (!in_array(static::ACTION_LIST, $this->getRouting()->getDisabledActions())) {
            return $this->defaultRoute = $this->getRouteName(static::ACTION_LIST);
        }

        return $this->defaultRoute = static::DEFAULT_ROUTE;
    }

    public function getRouteName($action)
    {
        return sprintf('%s_%s', $action, $action == static::ACTION_LIST ? $this->getPluralName(true) : $this->getEntityName(true));
    }

    /**
     * @return \Zenstruck\ResourceBundle\Config\Routing
     */
    public function getRouting()
    {
        return $this->routing;
    }
}