# Profiling

[![Travis CI](https://api.travis-ci.org/FriendsOfShopware/FroshProfiler.svg?branch=master)](https://travis-ci.org/FriendsOfShopware/FroshProfiler)

Profiling for Shopware  
Required Minimum Shopware Version 5.2
Required PHP 7.0

# Installation

## Zip Installation package for the Shopware Plugin Manager

* Download the [latest plugin version](https://github.com/FriendsOfShopware/FroshProfiler/releases/latest/) (e.g. `FroshProfiler-1.2.1.zip`)
* Upload and install plugin using Plugin Manager

## Git Version
* Checkout Plugin in `/custom/plugins/FroshProfiler`
* Change to Directory and run `composer install` to install the dependencies
* Install the Plugin with the Plugin Manager

## Install with composer
* Change to your root Installation of shopware
* Run command `composer require frosh/shopware-profiler` and install and active plugin with Plugin Manager 

## Var Dump Server

Starting with 1.3.0 profiler does also support [var dump server](https://symfony.com/blog/new-in-symfony-4-1-vardumper-server) from Symfony 4.1.

### How to use it?

* Enable var dump server in plugin config
* Open a terminal and run ```./bin/console server:dump --format=html > dump.html```
* Use method ```dump()``` in your code and open dump.html in your Browser.

# Images
![Browser Toolbar](http://i.imgur.com/1F5d8jj.jpg)

![Performance Profiling](http://i.imgur.com/3eUWwQ3.png)
