OpenExchangeRates for Symfony2
==============================

[![SensioLabsInsight](https://insight.sensiolabs.com/projects/ce4cd1cd-f4e5-42ed-8881-7a7d436f4f41/mini.png)](https://insight.sensiolabs.com/projects/ce4cd1cd-f4e5-42ed-8881-7a7d436f4f41)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/mrzard/OpenExchangeRatesBundle/badges/quality-score.png?s=4d7b367f4b6e520f529836f96f6f8ff6fef7ceee)](https://scrutinizer-ci.com/g/mrzard/OpenExchangeRatesBundle/)
[![Build Status](https://travis-ci.org/mrzard/OpenExchangeRatesBundle.svg?branch=master)](https://travis-ci.org/mrzard/OpenExchangeRatesBundle)

## Installation

``` bash
$ php composer.phar require mrzard/open-exchange-rates-bundle dev-master
```

And register the bundle in your AppKernel.php file

``` php
return array(
   // ...
   new Mrzard\OpenExchangeRatesBundle\OpenExchangeRatesBundleBundle(),
   // ...
);
```


## Configuration

You will have to define your api id in the parameters.yml file of you
environment.

``` yml
open_exchange_rates_service:
    api_id: YOUR_API_ID
    api_configuration:
        https: true|false #defaults to false
        base_currency: XXX #defaults to USD
```

If you're using a free version, you won't need to change the `https` or
`base_currency` as they only work fror Enterprise/Unlimited accounts

## Usage

You can access the service by getting it from the container under
open_exchange_rates_service

Keep in mind that some options will only work properly with an
Enterprise/Unlimited Plan

## Free features

### Get latest exchange rates

``` php
/**
 * Get the latest exchange rates
 *
 * @param array  $symbols Currency codes to get the rates for. Default all
 * @param string $base    Base currency, default NULL (gets it from config)
 *
 * @return array
 */
public function getLatest($symbols = array, $base = null)
{
}
```

Only use the `$symbols` and `$base` parameters if you have an Enterprise or
Unlimited plan.

Output:

```
array (size=5)
  'disclaimer' => string 'Exchange rates...'
  'license' => string 'Data sourced from...'
  'timestamp' => int 1395396061
  'base' => string 'USD' (length=3)
  'rates' =>
    array (size=166)
      'AED' => float 3.672721
      'AFN' => float 56.747225
      'ALL' => float 101.7573
      'AMD' => float 417.366998
      ...
    )
)
```

### Get available currencies

``` php
/**
 * Gets a list of all available currencies
 *
 * @return array with keys = ISO codes, content = Currency Name
 */
public function getCurrencies()
{
}
```

Output:

```
array (size=5)
  'AED' => 'United Arab Emirates Dirham'
  'AFN' => 'Afghan Afghani'
  'ALL' => 'Albanian Lek'
  'AMD' => 'Armenian Dram'
  'ANG' => 'Netherlands Antillean Guilder'
  ...
)
```


### Get historical data for a date

``` php
/**
 * Get historical data
 *
 * @param \DateTime $date
 */
public function getHistorical(\DateTime $date)
{
}
```

Output:

```
array (size=5)
  'disclaimer' => string 'Exchange rates...'
  'license' => string 'Data sourced from...'
  'timestamp' => int 1388617200
  'base' => string 'USD' (length=3)
  'rates' =>
    array (size=166)
      'AED' => float 3.672524
      'AFN' => float 56.0846
      'ALL' => float 102.06575
      'AMD' => float 408.448002
      'ANG' => float 1.78902
      'AOA' => float 97.598401
      'ARS' => float 6.51658
      'AUD' => float 1.124795
      'AWG' => float 1.789775
      'AZN' => float 0.7841
      'BAM' => float 1.421715
      'BBD' => int 2
      ...
    )
)
```

## Developer / Unlimited features

### Get the latest exchange rates, limiting the return array

``` php
$openExchangeRatesService->getLatest(['EUR', 'USD', 'COP']);
```

Output:

```
array (size=5)
  'disclaimer' => string 'Exchange rates ...'
  'license' => string 'Data sourced...'
  'timestamp' => int 1395396061
  'base' => string 'USD' (length=3)
  'rates' =>
    array (size=3)
      'EUR' => ...,
      'USD' => ...,
      'COP' => ...
    )
)
```

You can also change the base currency used to get the latest exchange rates with
the second parameter

### Directly convert a quantity between currencies

``` php
$openExchangeRatesService->convert(10, 'USD', 'EUR');
```
