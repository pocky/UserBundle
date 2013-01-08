<?php

/*
 * This file is part of the Blackengine package.
 *
 * (c) Alexandre Balmes <albalmes@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Blackroom\Bundle\UserBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('blackroom_user');

        $supportedDrivers = array('mongodb');

        $rootNode
            ->children()
                ->scalarNode('db_driver')
                    ->isRequired()
                    ->cannotBeOverwritten()
                    ->cannotBeEmpty()
                    ->validate()
                        ->ifNotInArray($supportedDrivers)
                        ->thenInvalid('The database driver %s is not supported, please use one of them' . json_encode($supportedDrivers))
                    ->end()
                ->end()

                ->scalarNode('user_class')->isRequired()->cannotBeEmpty()->end()

            ->end()


        ;

        $this->addUserSection($rootNode);

        return $treeBuilder;
    }

    private function addUserSection(ArrayNodeDefinition $node)
    {
        $node
            ->children()
                ->arrayNode('form')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('user')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('name')->defaultValue('blackroom_user')->end()
                                ->scalarNode('type')->defaultValue('Blackroom\\Bundle\\UserBundle\\Form\\Type\\UserType')->end()
                                ->scalarNode('handler')->defaultValue('blackroom_user.form.handler')->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()

            ->end()
        ;
    }
}
