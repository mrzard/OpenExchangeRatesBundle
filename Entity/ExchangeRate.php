<?php

namespace Mrzard\OpenExchangeRatesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Currency
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Mrzard\OpenExchangeRatesBundle\Entity\ExchangeRateRepository")
 */
class ExchangeRate
{

    /**
     * @var string
     * @ORM\Id
     * @ORM\Column(name="currency", type="string", length=3)
     */
    private $currency;

    /**
     * @var string
     *
     * @ORM\Column(name="rates", type="string", length=3000)
     */
    private $rates;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_at", type="datetime")
     */
    private $updatedAt;

    public function getCurrency()
    {
        return $this->currency;
    }

    public function setCurrency($currency)
    {
        $this->currency = $currency;
        return $this;
    }

    /**
     * Set rates
     *
     * @param string $rates
     * @return Currency
     */
    public function setRates($rates)
    {
        $this->rates = $rates;

        return $this;
    }

    /**
     * Get rates
     *
     * @return string
     */
    public function getRates()
    {
        return $this->rates;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     * @return Currency
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

}