# AltoLabs_Snappic

Integrate your Magento 2 store with Snappic.io.

## Requirements

* Magento 2.1+
* PHP 5.6+
* [Composer](http://getcomposer.org)

## Installation

From your Magento 2 root, simply use composer like so:

      composer require altolabs/snappic-magento2

If you want to run the `dev` edge version (unstable, at your own risk), you can
instead run:

      composer require altolabs/snappic-magento2 ^1.0@dev

Once installed, you will need to allow Magento to discover the module and be
re-compiled:

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

This module code is proprietary and copyright AltoLabs 2017.
