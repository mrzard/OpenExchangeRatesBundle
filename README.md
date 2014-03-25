OpenExchangeRates service for Symfony2
=====

Installation
-----
``` yml
"require": {
    "php": ">=5.3.0",
    "symfony/symfony": ">=2.3.0",
    "mrzard/open-exchange-rates-bundle": "dev-master"
}
```

Then use composer to install the bundle:
``` bash
$ curl -sS https://getcomposer.org/installer | php
$ php composer.phar update mrzard/open-exchange-rates-bundle
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
You will have to define your api id in the parameters.yml file of you environment.

``` yml
open_exchange_rates_service:
    api_id: YOUR_API_ID
    api_configuration:
        https: true|false #defaults to false
        base_currency: XXX #defaults to USD
```

If you're using a free version, you won't need to change the `https` or `base_currency` as they
only work fror Enterprise/Unlimited accounts

Usage
-----
You can access the service by getting it from the container under open_exchange_rates_service

Keep in mind that some options will only work properly with an Enterprise/Unlimited Plan

## Free features
``` php
/**
 * Get the latest exchange rates
 *
 * @param array  $symbols array of currency codes to get the rates for. Default empty (all
 * currencies)
 * @param string $base    Base currency, default NULL (gets it from config)
 *
 * @return array
 */
public function getLatest($symbols, $base = null)
```
Only use the `$symbols` and `$base` parameters if you have an Enterprise or Unlimited plan.

Sample returns:
``` php
/*output:
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
*/
```

``` php
    /**
     * Gets a list of all available currencies
     *
     * @return array with keys = ISO codes, content = Currency Name
     */
    public function getCurrencies()
```

Sample return:
``` php
array(
  'AED' => 'United Arab Emirates Dirham'
  'AFN' => 'Afghan Afghani'
  'ALL' => 'Albanian Lek'
  'AMD' => 'Armenian Dram'
  'ANG' => 'Netherlands Antillean Guilder'
  ...
)
```


``` php
/**
 * Get historical data
 *
 * @param \DateTime $date
 */
public function getHistorical(\DateTime $date)
```

Sample return:
``` php
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
      ....
```

## Developer / Unlimited features
``` php
$openExchangeRatesService->getLatest(['EUR', 'USD', 'COP']);
/*output:
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
*/
```

## Unlimited features
``` php
    $openExchangeRatesService->convert(10, 'USD', 'EUR');
```