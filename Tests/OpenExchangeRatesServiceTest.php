<?php

namespace Mrzard\OpenExchangeRatesBundle\Tests;

use Guzzle\Http\Client;
use Guzzle\Http\Message\Response;
use Mrzard\OpenExchangeRatesBundle\Service\OpenExchangeRatesService;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\FrameworkBundle\Console\Application;

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

        $container = static::$kernel->getContainer();
        $appId = $container->getParameter('open_exchange_rates_service.api_id');
        $fakeRequest = $this
            ->getMockBuilder('Guzzle\Http\Message\Request')
            ->setConstructorArgs([
                'GET',
                'localhost',
                []
            ])
            ->setMethods(['send', 'getResponse'])
            ->getMock();

        $fakeRequest->expects($this->any())->method('send')->willReturn(true);

        //all request will return a fake response
        $fakeRequest
            ->expects($this->any())
            ->method('getResponse')
            ->willReturn(new Response(200, null, json_encode(['ok' => true])));

        //create our fake client
        $fakeClient = $this
            ->getMockBuilder('Guzzle\Http\Client')
            ->setMethods(['createRequest'])
            ->getMock();

        //our client will always return a our request
        $fakeClient
            ->expects($this->any())
            ->method('createRequest')
            ->withAnyParameters()
            ->will($this->returnValue($fakeRequest));

        $this->service = new OpenExchangeRatesService(
            $appId,
            $this->getServiceConfig(),
            $fakeClient
        );
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

    /**
     * Test that the functions can run
     */
    public function testService()
    {
        $this->assertTrue($this->service->getLatest([], null)['ok'], 'getLatest failed');
        $this->assertTrue($this->service->getLatest(['EUR'], null)['ok'], 'getLatest failed');
        $this->assertTrue($this->service->getLatest(['EUR'], 'USD')['ok'], 'getLatest failed');
        $this->assertTrue($this->service->getLatest([], 'USD')['ok'], 'getLatest failed');
        $this->assertTrue($this->service->getCurrencies()['ok'], 'getCurrencies failed');
        $this->assertTrue(
            $this->service->convertCurrency(10, 'EUR', 'USD')['ok'],
            'convertCurrency failed'
        );
        $this->assertTrue(
            $this->service->getHistorical(new \DateTime('2014-01-01'))['ok'],
            'getHistorical failed'
        );
    }

    /**
     * Test that the class can be directly instantiated
     */
    public function testDirectInstantiation()
    {
        $config = $this->getServiceConfig();
        $service = new OpenExchangeRatesService(
            'f4k31d',
            $config,
            new Client()
        );
        $this->assertTrue($service instanceof OpenExchangeRatesService, 'Creation failed');
    }

    /**
     * Test what happens when an error is thrown
     */
    public function testError()
    {
        $container = static::$kernel->getContainer();
        $appId = $container->getParameter('open_exchange_rates_service.api_id');
        $fakeRequest = $this
            ->getMockBuilder('Guzzle\Http\Message\Request')
            ->setConstructorArgs([
                'GET',
                'localhost',
                []
            ])
            ->setMethods(['send', 'getResponse'])
            ->getMock();

        //make send throw an exception
        $fakeRequest->expects($this->any())->method('send')->willThrowException(
            new \Exception('testException')
        );

        //all request will return a fake response
        $fakeRequest
            ->expects($this->any())
            ->method('getResponse')
            ->willReturn(new Response(200, null, json_encode(['ok' => true])));

        //create our fake client
        $fakeClient = $this
            ->getMockBuilder('Guzzle\Http\Client')
            ->setMethods(['createRequest'])
            ->getMock();

        //our client will always return a our request
        $fakeClient
            ->expects($this->any())
            ->method('createRequest')
            ->withAnyParameters()
            ->will($this->returnValue($fakeRequest));

        $service = new OpenExchangeRatesService(
            $appId,
            $this->getServiceConfig(),
            $fakeClient
        );

        $this->assertArrayHasKey(
            'error',
            $service->getCurrencies(),
            'Error was not properly checked'
        );
    }

    /**
     * Test general config
     */
    public function testConfig()
    {
        $this->assertEquals(
            $this->service->getAppId(),
            static::$kernel->getContainer()->getParameter('open_exchange_rates_service.api_id'),
            'App id does not match correctly'
        );

        $config = $this->getServiceConfig();

        $this->assertEquals(
            $config['https'],
            $this->service->useHttps(),
            'https config mismatch'
        );

        $this->assertEquals(
            $config['base_currency'],
            $this->service->getBaseCurrency(),
            'base_currency config mismatch'
        );

        $this->service->setHttps(true);
        $this->assertEquals(
            true,
            $this->service->useHttps(),
            'https setter failed'
        );
        $this->assertEquals(
            'https://openexchangerates.org/api',
            $this->service->getEndPoint(),
            'Endpoint does not look right'
        );

        $this->service->setHttps(false);
        $this->assertEquals(
            'http://openexchangerates.org/api',
            $this->service->getEndPoint(),
            'Endpoint does not look right'
        );

        $this->service->setBaseCurrency('EUR');
        $this->assertEquals(
            'EUR',
            $this->service->getBaseCurrency(),
            'base currency setter failed'
        );
    }
}