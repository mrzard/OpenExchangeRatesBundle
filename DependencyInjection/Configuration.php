<?php
/**
 * OpenExchangeRates Bundle for Symfony2
 *
 * @author Gonzalo Míguez (mrzard@gmail.com)
 * @since 2014
 */

namespace Mrzard\OpenExchangeRatesBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Class Configuration
 * This is the class that validates and merges configuration from your app/config files
 *
 * @package Mrzard\OpenExchangeRatesBundle\DependencyInjection
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('open_exchange_rates');
        $rootNode
            ->children()
                ->scalarNode('api_id')
                    ->cannotBeEmpty()
                    ->isRequired()
                ->end()
                ->arrayNode('api_configuration')
                    ->children()
                        ->scalarNode('https')
                            ->defaultValue(false)
                        ->end()
                        ->scalarNode('base_currency')
                            ->defaultValue('USD')
                        ->end()
                    ->end()
            ->end();

        return $treeBuilder;
    }
}