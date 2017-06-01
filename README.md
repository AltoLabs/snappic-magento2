# AltoLabs_Snappic

Integrate your Magento 2 store with Snappic.io.

## Requirements

* Magento 2.1+
* PHP 5.6+
* [Composer](http://getcomposer.org)

## Installation

### Configuring the module

Once you have installed the module with Composer, you will need to allow Magento
to discover it and be re-compiled:

    php bin/magento setup:di:compile
    php bin/magento cache:flush && php bin/magento cache:clean
    php bin/magento setup:upgrade

Compilation can take some time.

## Automated tests

This module contains some automated tests - you can run them as such (from the
Magento 2 webroot):

### Unit tests

      vendor/bin/phpunit \
        -c dev/tests/unit/phpunit.xml.dist
        vendor/altolabs/snappic-magento2/Test/Unit/

### Integration tests

TBC.

## License

This module code is proprietary and licensed under the
