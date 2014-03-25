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
    protected $mockedService;

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

        $this->mockedService = $this
            ->getMockBuilder(
                'Mrzard\OpenExchangeRatesBundle\Service\OpenExchangeRatesService'
            )
            ->setConstructorArgs([$appId, $this->getServiceConfig(), $fakeClient])
            ->setMethods(null)
            ->getMock();

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
        $service = $this->mockedService;
        $this->assertTrue($service->getLatest([], null)['ok'], 'getLatest failed');
        $this->assertTrue($service->getLatest(['EUR'], null)['ok'], 'getLatest failed');
        $this->assertTrue($service->getLatest(['EUR'], 'USD')['ok'], 'getLatest failed');
        $this->assertTrue($service->getLatest([], 'USD')['ok'], 'getLatest failed');
        $this->assertTrue($service->getCurrencies()['ok'], 'getCurrencies failed');
        $this->assertTrue(
            $service->convertCurrency(10, 'EUR', 'USD')['ok'],
            'convertCurrency failed'
        );
        $this->assertTrue(
            $service->getHistorical(new \DateTime('2014-01-01'))['ok'],
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


        $this->mockedService = $this
            ->getMockBuilder(
                'Mrzard\OpenExchangeRatesBundle\Service\OpenExchangeRatesService'
            )
            ->setConstructorArgs([$appId, $this->getServiceConfig(), $fakeClient])
            ->setMethods(null)
            ->getMock();

        $this->assertArrayHasKey(
            'error',
            $this->mockedService->getCurrencies(),
            'Error was not properly checked'
        );
    }

    /**
     * Test general config
     */
    public function testConfig()
    {
        $this->assertEquals(
            $this->mockedService->getAppId(),
            static::$kernel->getContainer()->getParameter('open_exchange_rates_service.api_id'),
            'App id does not match correctly'
        );

        $config = $this->getServiceConfig();

        $this->assertEquals(
            $config['https'],
            $this->mockedService->useHttps(),
            'https config mismatch'
        );

        $this->assertEquals(
            $config['base_currency'],
            $this->mockedService->getBaseCurrency(),
            'base_currency config mismatch'
        );

        $this->mockedService->setHttps(true);
        $this->assertEquals(
            true,
            $this->mockedService->useHttps(),
            'https setter failed'
        );
        $this->assertEquals(
            'https://openexchangerates.org/api',
            $this->mockedService->getEndPoint(),
            'Endpoint does not look right'
        );

        $this->mockedService->setHttps(false);
        $this->assertEquals(
            'http://openexchangerates.org/api',
            $this->mockedService->getEndPoint(),
            'Endpoint does not look right'
        );

        $this->mockedService->setBaseCurrency('EUR');
        $this->assertEquals(
            'EUR',
            $this->mockedService->getBaseCurrency(),
            'base currency setter failed'
        );
    }
}