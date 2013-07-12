<?php

namespace Zenstruck\ResourceBundle\Config;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class Routing
{
    protected $prefix;
    protected $disabledActions;
    protected $formats;
    protected $defaultFormat;
    protected $extraRoutes = array();

    public function __construct(array $config)
    {
        $this->prefix = $config['prefix'];
        $this->disabledActions = $config['disabled_actions'];
        $this->formats = $config['formats'];
        $this->defaultFormat = $config['default_format'];
        $this->extraRoutes = $config['extra_routes'];
    }

    public function getPrefix()
    {
        return $this->prefix;
    }

    public function getDisabledActions()
    {
        return $this->disabledActions;
    }

    public function getDefaultFormat()
    {
        return $this->defaultFormat;
    }

    public function getFormats()
    {
        return $this->formats;
    }

    /**
     * @return array
     */
    public function getExtraRoutes()
    {
        return $this->extraRoutes;
    }
}