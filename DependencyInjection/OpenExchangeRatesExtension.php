<?php
/**
 * OpenExchangeRates Bundle for Symfony2
 *
 * @author Gonzalo Míguez (mrzard@gmail.com)
 * @since 2014
 */

namespace Mrzard\OpenExchangeRatesBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * Class OpenExchangeRatesExtension
 *
 * @package Mrzard\OpenExchangeRatesBundle\DependencyInjection
 */
class OpenExchangeRatesExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader(
            $container,
            new FileLocator(__DIR__.'/../Resources/config')
        );

        $container->setParameter('open_exchange_rates_service.api_id', $config['api_id']);
        $container->setParameter(
            'open_exchange_rates_service.api_configuration',
            $config['api_configuration']
        );
        $loader->load('parameters.yml');
        $loader->load('services.yml');
    }
}
