<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of getLatestExchangeRates
 *
 * @author stefan
 */

namespace Mrzard\OpenExchangeRatesBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\ORM\EntityManager;

class UpdateRatesCommand extends ContainerAwareCommand
{

    protected function configure()
    {
        $this
                ->setName('open-exchange-rates:updateRates')
                ->setDescription('Get the latest exchange rates')
                ->addArgument('base', InputArgument::OPTIONAL, 'Base currency, default NULL (gets it from config)', null)
                ->addArgument('symbols', InputArgument::IS_ARRAY,
                        'Array of currency codes to get the rates for. Defaults to all currencies.')
                ->setHelp('Fetches the latest currencies from open exchange and updates the entry in the database for the base currency or creates a new entry if it doesn\'t exist.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $symbols = $input->getArgument('symbols');
        $base = $input->getArgument('base');

        $output->writeln(sprintf('Started fetching the latest rates for %s.',
                        $base ? $base : $this->getContainer()->getParameter('open_exchange_rates_service.api_configuration')['base_currency'] ));

        $latestCurrencies = $this->getContainer()->get('open_exchange_rates_service')->getLatest($symbols, $base);

        $output->writeln('Finished fetching');

        if (isset($latestCurrencies['error'])) {
            $output->writeln(array('<error>An error has occured!</error>',
                '<error>' . $latestCurrencies['error']['description'] . '</error>')
            );

            return;
        }

        $output->writeln('Started updating the database');

        /* @var $em EntityManager */
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');

        $exchangeRate = $em->getRepository('OpenExchangeRatesBundle:ExchangeRate')->findOneBy(array('currency' => $latestCurrencies['base']));

        if (!$exchangeRate) {
            $output->writeln(sprintf('Currency %s not found, so a new entry is created.', $latestCurrencies['base']));
            $exchangeRate = new \Mrzard\OpenExchangeRatesBundle\Entity\ExchangeRate();
        } else {
            $output->writeln(sprintf('Currency %s found, currently updating it.', $latestCurrencies['base']));
        }

        $exchangeRate->setCurrency($latestCurrencies['base']);
        $exchangeRate->setRates(json_encode($latestCurrencies['rates']));
        $exchangeRate->setUpdatedAt(new \DateTime());

        $em->persist($exchangeRate);
        $em->flush();

        $output->writeln('Finished updating.');
    }

}