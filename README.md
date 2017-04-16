# AltoLabs_Snappic

Integrate your Magento 2 store with Snappic.io.

## Requirements

* Magento 2.1+
* PHP 5.6+
* [Composer](http://getcomposer.org)

## Installation

Magento 2 module installation is generally done using Composer. To install this module you will first need to make
your Composer configuration aware of the private Git repo that holds this module.

Open the `composer.json` file in your Magento 2 webroot and add the "VCS" repository below alongside the Magento composer
repository.

**File:** MAGENTO_ROOT/composer.json
```
"repositories": [
    {
        "type": "composer",
        "url": "https://repo.magento.com/"
    },
    {
        "type": "vcs",
        "url": "git@github.com:AltoLabs/snappic-magento-2-extension.git"
    }
],
```

You can now install the module:

```
composer require altolabs/snappic-magento-2 1.0.x-dev
```

This will install the latest unstable version of the code. If you want to install a stable version, use a stable semver
constraint - for example:

```
composer require altolabs/snappic-magento-2 ^1.0
```

You will also need to ensure that the `"minimum-stability"` in your Magento 2 project is permissive enough to let you
install unstable packages such as this, and ensure that `"prefer-stable": true` is also set to prevent you from
installing unstable versions of all of your packages.

### Configuring the module

Once you have installed the module with Composer, you will need to allow Magento to discover it and be re-compiled:

```
php bin/magento setup:di:compile
php bin/magento cache:flush && php bin/magento cache:clean
```

Compilation can take some time.

It's also a good idea to enable developer mode when running Magento locally:

```
php bin/magento deploy:mode:set developer
```

## Automated tests

This module contains some automated tests - you can run them as such (from the Magento 2 webroot):

### Unit tests

```
vendor/bin/phpunit -c dev/tests/unit/phpunit.xml.dist vendor/altolabs/snappic-magento-2/Test/Unit/
```

### Integration tests

TBC.

## License

This module code is proprietary. TBC.
