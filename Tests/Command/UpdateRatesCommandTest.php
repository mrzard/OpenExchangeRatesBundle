<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of updateRatesCommandTest
 *
 * @author stefan
 */
use Mrzard\OpenExchangeRatesBundle\Command\UpdateRatesCommand;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

require_once(__DIR__ . "/../../../../../../../app/AppKernel.php");

class updateRatesCommandTest extends \PHPUnit_Framework_TestCase
{

    public function testUpdateRates()
    {
        $kernel = new \AppKernel("dev", true);
        $kernel->boot();
        $application = new \Symfony\Bundle\FrameworkBundle\Console\Application($kernel);
        $application->setAutoExit(false);

        $application->add(new UpdateRatesCommand());

        $command = $application->find('open-exchange-rates:updateRates');
        $commandTester = new CommandTester($command);
        $commandTester->execute(
                array(
                    'base' => 'USD'
                )
        );

        $this->assertRegExp('/Finished updating/', $commandTester->getDisplay());
    }

}