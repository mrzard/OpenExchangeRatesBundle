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


## Configuration
You will have to define your api id in the parameters.yml file of you environment.

``` yml
open_exchange_rates_service: YOUR_API_ID
```

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
Sample returns:
```
    $openExchangeRatesService->getLatest();
    /*output:
        array (size=5)
          'disclaimer' => string 'Exchange rates are provided for informational purposes only, and do not constitute financial advice of any kind. Although every attempt is made to ensure quality, NO guarantees are given whatsoever of accuracy, validity, availability, or fitness for any purpose - please use at your own risk. All usage is subject to your acceptance of the Terms and Conditions of Service, available at: https://openexchangerates.org/terms/' (length=423)
          'license' => string 'Data sourced from various providers with public-facing APIs; copyright may apply; resale is prohibited; no warranties given of any kind. Bitcoin data provided by http://coindesk.com. All usage is subject to your acceptance of the License Agreement available at: https://openexchangerates.org/license/' (length=300)
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
```
array(
  'AED' => 'United Arab Emirates Dirham'
  'AFN' => 'Afghan Afghani'
  'ALL' => 'Albanian Lek'
  'AMD' => 'Armenian Dram'
  'ANG' => 'Netherlands Antillean Guilder'
  ...
)
```


Sample return:
``` php
    /**
     * Get historical data
     *
     * @param \DateTime $date
     */
    public function getHistorical(\DateTime $date)
```

## Developer / Unlimited features
``` php
    $openExchangeRatesService->getLatest(['EUR', 'USD', 'COP']);
        /*output:
        array (size=5)
          'disclaimer' => string 'Exchange rates are provided for informational purposes only, and do not constitute financial advice of any kind. Although every attempt is made to ensure quality, NO guarantees are given whatsoever of accuracy, validity, availability, or fitness for any purpose - please use at your own risk. All usage is subject to your acceptance of the Terms and Conditions of Service, available at: https://openexchangerates.org/terms/' (length=423)
          'license' => string 'Data sourced from various providers with public-facing APIs; copyright may apply; resale is prohibited; no warranties given of any kind. Bitcoin data provided by http://coindesk.com. All usage is subject to your acceptance of the License Agreement available at: https://openexchangerates.org/license/' (length=300)
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