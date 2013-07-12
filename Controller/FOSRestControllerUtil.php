<?php

namespace Zenstruck\ResourceBundle\Controller;

use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class FOSRestControllerUtil extends ControllerUtil
{
    public function render($view, array $parameters = array(), Response $response = null)
    {
        $templateVar = null;

        // if only one parameter is set, just use it for the data
        if (count($parameters) === 1) {
            $templateVar = key($parameters);
            $parameters = $parameters[$templateVar];
        }

        return $this->createResponse($parameters, $view, $templateVar);
    }

    /**
     * @param $data
     * @param string|null $template
     * @param string|null $tempateVar
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function createResponse($data, $template = null, $tempateVar = null)
    {
        $view = $this->view($data);

        if ($template) {
            $view->setTemplate($template);
        }

        if ($tempateVar) {
            $view->setTemplateVar($tempateVar);
        }

        return $this->handleView($view);
    }

    /**
     * @param null $data
     * @param null $statusCode
     * @param array $headers
     *
     * @return View
     */
    public function view($data = null, $statusCode = null, array $headers = array())
    {
        return View::create($data, $statusCode, $headers);
    }

    /**
     * @param View $view
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handleView(View $view)
    {
        return $this->get('fos_rest.view_handler')->handle($view);
    }
}