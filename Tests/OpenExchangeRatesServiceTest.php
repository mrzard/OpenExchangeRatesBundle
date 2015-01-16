<?php

namespace Mrzard\OpenExchangeRatesBundle\Tests;

use Mrzard\OpenExchangeRates\Service\OpenExchangeRatesService;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\HttpKernel\KernelInterface;

class OpenExchangeRatesServiceTest extends WebTestCase
{
    /**
     * @var Application|null
     */
    protected static $application = null;

    /**
     * @var KernelInterface|null
     */
    protected static $kernel = null;

    /**
     * @var OpenExchangeRatesService
     */
    protected $service;

    /**
     * Get service configuration
     *
     * @return array
     */
    protected function getServiceConfig()
    {
        return static::$kernel->getContainer()->getParameter(
            'open_exchange_rates_service.api_configuration'
        );
    }

    /**
     * Set up test
     */
    public function setUp()
    {
        static::$kernel = static::createKernel();
        static::$kernel->boot();
        static::$application = new Application(static::$kernel);
        static::$application->setAutoExit(false);
    }

    /**
     * Checks if the app can get the service from the container
     */
    public function testServiceIsGettable()
    {
        $serviceCallableName = 'open_exchange_rates_service';
        $this->assertTrue(static::$kernel->getContainer()->has($serviceCallableName));
        static::$kernel->getContainer()->get($serviceCallableName);
        $this->assertTrue(true);
    }
}