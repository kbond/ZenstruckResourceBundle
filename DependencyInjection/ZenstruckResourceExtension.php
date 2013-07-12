<?php

namespace Zenstruck\ResourceBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class ZenstruckResourceExtension extends Extension
{
    public function load(array $config, ContainerBuilder $container)
    {
        $processor = new Processor();
        $configuration = new Configuration();
        $config = $processor->processConfiguration($configuration, $config);
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));

        $loader->load('services.xml');
        $container->setParameter('zenstruck_resource.controller_utils.class', $config['controller_utils_class']);

        $utilDef = $container->getDefinition('zenstruck_resource.controller_utils');
        $routeLoaderDef = $container->getDefinition('zenstruck_resource.routing_loader');
        $resourceClass = $container->getParameter('zenstruck_resource.resource.class');

        foreach ($config['controllers'] as $name => $controller) {
            $controllerClass = $controller['controller_class'] ?: $config['default_controller_class'];
            $controllerId = $controller['controller_id'];

            if (!$controllerId) {
                // build controller id based on bundle and controller name (ie AppBundle:Post becomse app.controller.post)
                preg_match('/^([\w]+)Bundle/', $controller['entity'], $matches);
                $controllerId = sprintf('%s.controller.%s', $matches[1], $name);
            }

            $controller['service_id'] = $controllerId;
            unset($controller['controller_class']);

            $configDef = new Definition($resourceClass, array($controller));
            $configDef->setPublic(false);

            $controllerDef = new Definition($controllerClass, array($configDef, $utilDef));

            if ($controller['routing']['enabled']) {
                $routeLoaderDef->addMethodCall('addConfig', array($configDef));
            }

            $container->setDefinition($controllerId.'.config', $configDef);
            $container->setDefinition($controllerId, $controllerDef);
        }
    }
}