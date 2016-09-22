[![Travis](https://img.shields.io/travis/Webysther/composer-plugin-qa.svg?style=flat-square)](https://travis-ci.org/Webysther/composer-plugin-qa)
[![Travis](https://img.shields.io/badge/HHVM-tested-orange.svg?style=flat-square&maxAge=3600)](https://travis-ci.org/Webysther/composer-plugin-qa)
[![Minimum PHP Version](https://img.shields.io/badge/php-%3E%3D%205.3-8892BF.svg?style=flat-square&maxAge=3600)](https://php.net/)
[![Packagist](https://img.shields.io/packagist/v/Webysther/composer-plugin-qa.svg?style=flat-square)](https://packagist.org/packages/webysther/composer-plugin-qa)
[![GitHub license](https://img.shields.io/badge/license-MIT-blue.svg?style=flat-square&maxAge=3600)](https://raw.githubusercontent.com/Webysther/composer-plugin-qa/master/LICENSE)

# Composer Plugin for QA

Comprehensive [plugin for composer](https://getcomposer.org/doc/articles/plugins.md#creating-a-plugin)
 to execute [PHP QA Tools](http://phpqatools.org) in a uniform and simple way.
 
## Features

- Automatic check if exists src,app,test and use by default
- Check if binary is in ```vendor/bin``` or globally installed
- Show ```--version``` for all started commands, only few tools show this information
- Total Execution time
- Command executed by the wrapper

## Sample

```bash
$ cd /sample
$ composer qa:cpd

Running Copy/Paste Detector...
phpcpd 2.0.4 by Sebastian Bergmann.


Found 1 exact clones with 654 duplicated lines in 2 files:
 
  -	/sample/tests/Folha/Durin/Models/News/NewstextTest.php:73-132
 	/sample/tests/Folha/Durin/Adapters/Resource/News/NewstextResourceTest.php:59-118
 
0.08% duplicated lines out of 58771 total lines of code.

Time: 1.31 seconds, Memory: 46.25MB

Command executed `phpcpd app tests --ansi --fuzzy` in 1 seconds
```

## Install

Add to composer.json:

```json
"require-dev": {
    "webysther/composer-plugin-qa": "~1.0",
    "webysther/composer-meta-qa": "~1.0"
}
```

Or install globally:

```bash
composer global require webysther/composer-plugin-qa
```

List the options

```bash
$ composer list
 qa
  qa:all                    Run all tools
  qa:code-coverage          Code Coverage
  qa:cc                     Code Coverage
  qa:code-beautifier-fixer  Code Beautifier and Fixer
  qa:cbf                    Code Beautifier and Fixer
  qa:copy-paste-detector    Copy/Paste Detector
  qa:cpd                    Copy/Paste Detector
  qa:code-sniffer           Code Sniffer
  qa:cs                     Code Sniffer
  qa:php-cs-fixer           PHP Code Sniffer Fixer
  qa:csf                    PHP Code Sniffer Fixer
  qa:line-of-code           Line of Code
  qa:loc                    Line of Code
  qa:mess-detector          Mess Detector
  qa:md                     Mess Detector
  qa:php-metrics            PHP Metrics
  qa:pm                     PHP Metrics
  qa:security-checker       SecurityChecker
  qa:sc                     SecurityChecker
  qa:test                   Tests

```

All plugins (except ```qa:test```) have the short version, example,```qa:sc``` is short version for ```qa:security-checker```.

## PHP Quality Assurance Tools

This is a composer meta package for installing PHP Quality Assurance Tools with only one dependency, based on [h4cc/phpqatools](https://github.com/h4cc/phpqatools).

Included in this package (based on [phpqatools](http://phpqatools.org/)) are:

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
