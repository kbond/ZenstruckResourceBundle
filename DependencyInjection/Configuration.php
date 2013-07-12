<?php

namespace Zenstruck\ResourceBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('zenstruck_resource');

        $rootNode
            ->children()
                ->scalarNode('default_controller_class')->defaultValue('Zenstruck\ResourceBundle\Controller\ResourceController')->end()
                ->scalarNode('controller_utils_class')->defaultValue('Zenstruck\ResourceBundle\Controller\ControllerUtil')->end()
                ->arrayNode('controllers')
                    ->useAttributeAsKey('name')
                    ->prototype('array')
                        ->children()
                            ->scalarNode('entity')->isRequired()->info('The entity (in the short notation) to create a resource controller for.')->example('AppBundle:Product')->end()
                            ->scalarNode('controller_class')->defaultNull()->info('The optional FQN of the resource controller. The above "default_controller_class" option will be used if left blank.')->end()
                            ->scalarNode('controller_id')->defaultNull()->info('The service id for the generated controller. By default it is: "<bundle_prefix>.controller.<resource_name>".')->end()
                            ->scalarNode('form_class')->defaultNull()->info('The optional FQN of the form. It defaults to the Symfony2 standard for the resource.')->end()
                            ->scalarNode('default_route')->defaultNull()->info('The default route to use after create/edit/delete actions.  Defaults to the "list" action if enabled or "homepage" if not.')->end()
                            ->arrayNode('routing')
                                ->canBeEnabled()
                                ->children()
                                    ->variableNode('disabled_actions')->defaultValue(array())->info('An array of disabled actions. Allowed values: list, show, new, post, edit, put, delete.')->example('[show, list]')->end()
                                    ->scalarNode('prefix')->defaultValue('/')->end()
                                    ->scalarNode('default_format')->defaultValue('html')->end()
                                    ->scalarNode('formats')->defaultValue('html')->example('html|json')->end()
                                    ->arrayNode('extra_routes')->info('Additional routes for this resource.')
                                        ->example(array(
                                            'promote' => array(
                                                'pattern' => '/promote',
                                                'methods' => 'POST'
                                            ),
                                            'photos' => array(
                                                'pattern' => '/{id}/photos'
                                            )
                                        ))
                                        ->useAttributeAsKey('name')
                                        ->prototype('array')
                                            ->children()
                                                ->scalarNode('pattern')->isRequired()->end()
                                                ->scalarNode('methods')->defaultValue('GET')->end()
                                                ->scalarNode('formats')->defaultValue('html')->end()
                                                ->scalarNode('default_format')->defaultValue('html')->end()
                                            ->end()
                                        ->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}