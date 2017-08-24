[![Travis](https://img.shields.io/travis/folhasp/composer-plugin-qa.svg?style=flat-square)](https://travis-ci.org/folhasp/composer-plugin-qa)
[![Travis](https://img.shields.io/badge/HHVM-tested-orange.svg?style=flat-square&maxAge=3600)](https://travis-ci.org/folhasp/composer-plugin-qa)
[![Minimum PHP Version](https://img.shields.io/badge/php-%3E%3D%205.3-8892BF.svg?style=flat-square&maxAge=3600)](https://php.net/)
[![Packagist](https://img.shields.io/packagist/v/folhasp/composer-plugin-qa.svg?style=flat-square)](https://packagist.org/packages/folhasp/composer-plugin-qa)
[![GitHub license](https://img.shields.io/badge/license-MIT-blue.svg?style=flat-square&maxAge=3600)](https://raw.githubusercontent.com/folhasp/composer-plugin-qa/master/LICENSE)

# Composer Plugin for QA

Comprehensive [plugin for composer](https://getcomposer.org/doc/articles/plugins.md#creating-a-plugin)
 to execute [PHP QA Tools](http://phpqatools.org) in a uniform and simple way.
 
## Features

- Automatic check if exists src,app,test and use by default
- Check if binary is in ```vendor/bin``` or globally installed
- Show ```--version``` for all started commands, only few tools show this information
- Total Execution time
- Command executed by the wrapper

## Install

Add to composer.json:

```json
"require-dev": {
    "folhasp/composer-plugin-qa": "~1.0",
    "folhasp/composer-meta-qa": "~1.0"
}
```

Or install globally:

```bash
composer global require folhasp/composer-plugin-qa
```

List the options

```bash
$ composer list
```

![](https://pbs.twimg.com/media/CtOb2zfXYAAQ21O.jpg)

Almost commands have the short version, example,```qa:sec``` is short version for ```qa:security-checker```.

## Sample

Run Code Sniffer to all source code (```composer qa:cs``` is a short version):

![](https://pbs.twimg.com/media/CtOelj1WYAAHqrS.jpg)

If you change some peace of code e need run for this change:

![](https://pbs.twimg.com/media/CtOeVnyWYAAfQMx.jpg:large)

Is possibile to point for diretory or file:

```bash
$ composer qa:cs app/ACME
$ composer qa:cs app/ACME/Bomb.php
```

To see options for any QA command:

```bash
$ composer qa:cpd --help
```

## List of PHP Quality Assurance Tools

- [PHPUnit](https://github.com/sebastianbergmann/phpunit): Testing Framework
- [PHPCOV](https://github.com/sebastianbergmann/phpcov): CLI frontend for the [PHP_CodeCoverage](https://github.com/sebastianbergmann/php-code-coverage)
- [Paratest](https://github.com/brianium/paratest): Parallel testing for PHPUnit
- [DbUnit](https://github.com/sebastianbergmann/dbunit): Puts your database into a known state between test runs
- [PHPLOC](https://github.com/sebastianbergmann/phploc): A tool for quickly measuring the size of a PHP project
- [PHPCPD](https://github.com/sebastianbergmann/phpcpd): Copy/Paste Detector
- [PHP_Depend](https://github.com/pdepend/pdepend): Quality of your design in the terms of extensibility, reusability and maintainability
- [PHPMD](https://github.com/phpmd/phpmd): User friendly frontend application for the raw metrics stream measured by PHP Depend
- [PhpMetrics](https://github.com/phpmetrics/PhpMetrics): Static analysis tool, gives metrics about PHP project and classes
- [PHP_CodeSniffer](https://github.com/squizlabs/PHP_CodeSniffer): Detects violations of a defined set of coding standards

Plus: 

- [PHP-CS-Fixer](https://github.com/FriendsOfPHP/PHP-CS-Fixer): A tool to automatically fix coding standards issues
- [Security-Checker](https://github.com/sensiolabs/security-checker): Checks if your application uses dependencies with known security vulnerabilities

Suggest install:

- [Prestissimo](https://github.com/hirak/prestissimo): Composer parallel install plugin
