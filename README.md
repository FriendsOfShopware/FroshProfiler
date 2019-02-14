# Profiling

TEST

[![Travis CI](https://api.travis-ci.org/FriendsOfShopware/FroshProfiler.svg?branch=master)](https://travis-ci.org/FriendsOfShopware/FroshProfiler)

Profiling for Shopware

| Version 	| Requirements               	| Availability                            	|
|---------	|----------------------------	|-----------------------------------------	|
| 1.3     	| Min. Shopware 5.2, PHP 5.6 	| Github & Community Store                	|
| 1.4     	| Min. Shopware 5.5, PHP 7.1 	| Github (with 5.6 Release also in Store) 	|

**Please create Pull Requests to the lowest version**

# Installation

## Zip Installation package for the Shopware Plugin Manager

* Download the [latest plugin version](https://github.com/FriendsOfShopware/FroshProfiler/releases/latest/) (e.g. `FroshProfiler-1.3.6.zip`)
* Upload and install plugin using Plugin Manager

## Git Version
* Checkout Plugin in `/custom/plugins/FroshProfiler`
* Change to Directory and run `composer install` to install the dependencies
* Install the Plugin with the Plugin Manager

## Install with composer
* Change to your root Installation of shopware
* Run command `composer require frosh/shopware-profiler` and install and active plugin with Plugin Manager 

## Features

### Var Dump Server

Starting with 1.3.0 profiler does also support [var dump server](https://symfony.com/blog/new-in-symfony-4-1-vardumper-server) from Symfony 4.1.

#### How to use it?

* Enable var dump server in plugin config
* Open a terminal and run ```./bin/console server:dump --format=html > dump.html```
* Use method ```dump()``` in your code and open dump.html in your Browser.

![VarDumpServerHtml](https://i.imgur.com/qrTtG1Z.png)

### Adding additional stop watch events

````php
$this->get('frosh_profiler.stop_watch')->start('Watch Name');

// Your code

$this->get('frosh_profiler.stop_watch')->stop('Watch Name');
````

Go to Performance tab, and your custom events are shown there

### JavaScript: StateManager and Event Logging

Will log ...

... PubSub events: 

* subscribe (*event name*)
* unsubscribe (*event name*)
* publish (*event name, arguments*)

... Plugin registration:

* addPlugin (*plugin name, element, viewports*)
* removePlugin (*plugin name, element*)
* updatePlugin (*plugin name, element*)
* destroyPlugin (*plugin name, element*)

... Breakpoint change:

* switchPlugins (*previous/current state*)

... Plugin initialization:

* initPlugin (*plugin name, selector, event handlers*)

#### Filter

You can filter output by ...

* type (available: *subscribe, unsubscribe, publish, addPlugin, removePlugin, updatePlugin, destroyPlugin, switchPlugins, initPlugin*)
* event name
* plugin name

To set filter use the javascript console and the following functions, corresponding to each filter:

```javascript
StateDebug.setFilterType('publish');
StateDebug.setFilterEvent(['onTrackItems', 'onSetSizes']);
StateDebug.setFilterPlugin('swAjaxVariant');
```

You can pass a single filter criteria as string or an array of multiple filter criterias.

Type criterias need to be exact and valid (see above). Event and plugin name criterias can
include part of the name.

To reset filter criterias call the functions without any arguments.


### Backend Profiling

Backend Profiling can be enabled in plugin configuration. The Profile link can be retrieved from the Request Response Header "X-Profiler-URL"

# Images
![Browser Toolbar](http://i.imgur.com/1F5d8jj.jpg)

![Performance Profiling](http://i.imgur.com/3eUWwQ3.png)
