<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of CurrencyConverter
 *
 * @author stefan
 */

namespace Mrzard\OpenExchangeRatesBundle\Services;

use Doctrine\ORM\EntityManager;
use Mrzard\OpenExchangeRatesBundle\Entity\ExchangeRate;

class CurrencyConverter
{

    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function getExchangeRate($from, $to)
    {
        /* @var $exchangeRate ExchangeRate */
        $exchangeRate = $this->em->getRepository("OpenExchangeRatesBundle:ExchangeRate")->findOneBy(array('currency' => $from));

        if (!$exchangeRate) {
            throw new \Exception(sprintf(
                    'The base currency %s is not available in the local database. Use the open_exchange_rates_service service instead.',
                    $from));
        }

        $rates = json_decode($exchangeRate->getRates(), true);

        if (!isset($rates[$to])) {
            throw new \Exception(sprintf(
                    'The currency %s to witch you want to convert is not available in the local database. Use the open_exchange_rates_service service instead.',
                    $to));
        }

        return $rates[$to];
    }

    public function convertAmount($amount, $from, $to)
    {
        return $amount * $this->getExchangeRate($from, $to);
    }

}