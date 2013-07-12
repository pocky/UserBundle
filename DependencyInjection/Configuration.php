<?php

/*
 * This file is part of the Blackengine package.
 *
 * (c) Alexandre Balmes <albalmes@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Black\Bundle\UserBundle\DependencyInjection;

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
        $rootNode = $treeBuilder->root('black_user');

        $supportedDrivers = array('mongodb', 'mysql');

        $rootNode
            ->children()
                ->scalarNode('db_driver')
                    ->isRequired()
                    ->cannotBeOverwritten()
                    ->cannotBeEmpty()
                    ->validate()
                        ->ifNotInArray($supportedDrivers)
                        ->thenInvalid(
                            'The database driver %s is not supported, please use one of them'
                            . json_encode($supportedDrivers)
                        )
                    ->end()
                ->end()

                ->scalarNode('user_class')->isRequired()->cannotBeEmpty()->end()

            ->end();

        $this->addUserSection($rootNode);
        $this->addServiceSection($rootNode);

        return $treeBuilder;
    }

    private function addUserSection(ArrayNodeDefinition $node)
    {
        $node
            ->children()
                ->arrayNode('user')
                    ->addDefaultsIfNotSet()
                    ->canBeUnset()
                    ->children()
                        ->arrayNode('form')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('name')->defaultValue('black_user')
                                ->end()
                                ->scalarNode('type')->defaultValue('Black\\Bundle\\UserBundle\\Form\\Type\\UserType')
                                ->end()
                                ->scalarNode('handler')->defaultValue(
                                    'Black\\Bundle\\UserBundle\\Form\\Handler\\UserFormHandler'
                                )
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                 ->end()
            ->end()
            ->children()
                ->arrayNode('register')
                    ->addDefaultsIfNotSet()
                    ->canBeUnset()
                    ->children()
                        ->arrayNode('form')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('name')->defaultValue('black_register')
                                ->end()
                                ->scalarNode('type')->defaultValue(
                                    'Black\\Bundle\\UserBundle\\Form\\Type\\RegisterType'
                                )
                                ->end()
                                ->scalarNode('handler')->defaultValue(
                                    'Black\\Bundle\\UserBundle\\Form\\Handler\\RegisterFormHandler'
                                )
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                 ->end()
            ->end()
            ->children()
                ->arrayNode('front_user')
                    ->addDefaultsIfNotSet()
                    ->canBeUnset()
                    ->children()
                        ->arrayNode('form')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('name')->defaultValue('black_front_user')
                                ->end()
                                ->scalarNode('type')->defaultValue(
                                    'Black\\Bundle\\UserBundle\\Form\\Type\\FrontUserFormType'
                                )
                                ->end()
                                ->scalarNode('handler')->defaultValue(
                                    'Black\\Bundle\\UserBundle\\Form\\Handler\\FrontUserHandler'
                                )
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();
    }

    private function addServiceSection(ArrayNodeDefinition $node)
    {
        $node
            ->addDefaultsIfNotSet()
                ->children()
                    ->arrayNode('service')
                        ->addDefaultsIfNotSet()
                        ->children()
                            ->scalarNode('mailer')->defaultValue('black_user.mailer.default')->end()
                        ->end()
                    ->end()
                ->end()
            ->end();
    }
}
