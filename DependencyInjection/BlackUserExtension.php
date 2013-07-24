<?php

/*
 * This file is part of the Black package.
 *
 * (c) Alexandre Balmes <albalmes@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Black\Bundle\UserBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

/**
 * Class BlackUserExtension
 *
 * @package Black\Bundle\UserBundle\DependencyInjection
 * @author  Alexandre Balmes <albalmes@gmail.com>
 * @license http://opensource.org/licenses/mit-license.php MIT
 */
class BlackUserExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $processor      = new Processor();
        $configuration  = new Configuration();
        $config         = $processor->processConfiguration($configuration, $configs);

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));

        if (!isset($config['db_driver'])) {
            throw new \InvalidArgumentException('You must provide the black_user.db_driver configuration');
        }

        try {
            $loader->load(sprintf('%s.xml', $config['db_driver']));
        } catch (\InvalidArgumentException $e) {
            throw new \InvalidArgumentException(
                sprintf('The db_driver "%s" is not supported by user', $config['db_driver'])
            );
        }

        $this->remapParametersNamespaces(
            $config,
            $container,
            array(
                ''  => array(
                    'db_driver'     => 'black_user.db_driver',
                    'user_class'    => 'black_user.model.user.class',
                    'user_manager'  => 'black_user.user.manager',
                )
            )
        );

        if (!empty($config['user'])) {
            $this->loadUser($config['user'], $container, $loader);
        }

        if (!empty($config['register'])) {
            $this->loadRegister($config['register'], $container, $loader);
        }

        if (!empty($config['front_user'])) {
            $this->loadFrontUser($config['front_user'], $container, $loader);
        }

        foreach (array('mailer', 'unlock') as $basename) {
            $loader->load(sprintf('%s.xml', $basename));
        }

        $container->setAlias('black_user.mailer', $config['service']['mailer']);
    }

    /**
     * @param array            $config
     * @param ContainerBuilder $container
     * @param XmlFileLoader    $loader
     */
    private function loadUser(array $config, ContainerBuilder $container, XmlFileLoader $loader)
    {
        $loader->load('user.xml');

        $this->remapParametersNamespaces(
            $config,
            $container,
            array(
                'form' => 'black_user.user.form.%s',
            )
        );
    }

    /**
     * @param array            $config
     * @param ContainerBuilder $container
     * @param XmlFileLoader    $loader
     */
    private function loadRegister(array $config, ContainerBuilder $container, XmlFileLoader $loader)
    {
        $loader->load('register.xml');

        $this->remapParametersNamespaces(
            $config,
            $container,
            array(
                'form' => 'black_user.register.form.%s',
            )
        );
    }

    /**
     * @param array            $config
     * @param ContainerBuilder $container
     * @param XmlFileLoader    $loader
     */
    private function loadFrontUser(array $config, ContainerBuilder $container, XmlFileLoader $loader)
    {
        $loader->load('front_user.xml');

        $this->remapParametersNamespaces(
            $config,
            $container,
            array(
                'form' => 'black_user.front_user.form.%s',
            )
        );
    }

    /**
     * @param array            $config
     * @param ContainerBuilder $container
     * @param array            $map
     */
    protected function remapParameters(array $config, ContainerBuilder $container, array $map)
    {
        foreach ($map as $name => $paramName) {
            if (array_key_exists($name, $config)) {
                $container->setParameter($paramName, $config[$name]);
            }
        }
    }

    /**
     * @param array            $config
     * @param ContainerBuilder $container
     * @param array            $namespaces
     */
    protected function remapParametersNamespaces(array $config, ContainerBuilder $container, array $namespaces)
    {
        foreach ($namespaces as $ns => $map) {

            if ($ns) {
                if (!array_key_exists($ns, $config)) {
                    continue;
                }
                $namespaceConfig = $config[$ns];
            } else {
                $namespaceConfig = $config;
            }
            if (is_array($map)) {
                $this->remapParameters($namespaceConfig, $container, $map);
            } else {
                foreach ($namespaceConfig as $name => $value) {
                    $container->setParameter(sprintf($map, $name), $value);
                }
            }
        }
    }

    /**
     * @return string
     */
    public function getAlias()
    {
        return 'black_user';
    }
}
