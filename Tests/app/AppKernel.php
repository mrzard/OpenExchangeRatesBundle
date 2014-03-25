<?php

/**
 * OpenExchangeRates Bundle for Symfony2
 * OpenExchangeRates Bundle for Symfony2
 *
 * @author Marc Morera <yuhu@mmoreram.com>
 * @since 2013
 */

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

/**
 * AppKernel for testing
 */
class AppKernel extends Kernel
{
    /**
     * Register OpenExchangeRatesBundle
     */
    public function registerBundles()
    {
        return array(
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Mrzard\OpenExchangeRatesBundle\OpenExchangeRatesBundle(),
        );
    }

    /**
     * Setup configuration file.
     */
    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(dirname(__FILE__) . '/config.yml');
    }
}
