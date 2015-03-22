FlameCore FilesSynchronizer
===========================

[![Build Status](https://img.shields.io/travis/FlameCore/FilesSynchronizer.svg)](https://travis-ci.org/FlameCore/FilesSynchronizer)
[![Code Climate](http://img.shields.io/codeclimate/github/FlameCore/FilesSynchronizer.svg)](https://codeclimate.com/github/FlameCore/FilesSynchronizer)
[![License](http://img.shields.io/packagist/l/flamecore/synchronizer-files.svg)](https://packagist.org/packages/flamecore/synchronizer-files)

This library makes it easy to synchronize local and remote filesystems.

FilesSynchronizer was developed as backend for the deployment and testing tool [Seabreeze](https://github.com/FlameCore/Seabreeze).
It is using our self-developed [Synchronizer](https://github.com/FlameCore/Synchronizer) library as foundation.


Getting Started
---------------

Create your `Source` and `Target` objects:

```php
$source = new LocalFilesSource(['dir' => $sourcePath]);
$target = new LocalFilesTarget(['dir' => $targetPath]);
```

Create the `FilesSynchronizer` and assign the `Source` and the `Target`: 

```php
$synchronizer = new FilesSynchronizer($source, $target);
$synchronizer->observe($observer); // optionally set an EventObserver object
```

Now start syncing your files:

```php
$synchronizer->synchronize();
$synchronizer->synchronize(false); // Do not preserve obsolete files
```


Installation
------------

### Install via Composer

Create a file called `composer.json` in your project directory and put the following into it:

```
{
    "require": {
        "flamecore/synchronizer-files": "dev-master"
    }
}
```

[Install Composer](https://getcomposer.org/doc/00-intro.md#installation-nix) if you don't already have it present on your system:

    curl -sS https://getcomposer.org/installer | php

Use Composer to [download the vendor libraries](https://getcomposer.org/doc/00-intro.md#using-composer) and generate the vendor/autoload.php file:

    php composer.phar install

Include the vendor autoloader and use the classes:

```php
namespace Acme\MyApplication;

use FlameCore\Synchronizer\Files\FilesSynchronizer;
use FlameCore\Synchronizer\Files\Source\LocalFilesSource;
use FlameCore\Synchronizer\Files\Target\LocalFilesTarget;

require 'vendor/autoload.php';
```


Requirements
------------

* You must have at least PHP version 5.4 installed on your system.


Contributors
------------

If you want to contribute, please see the [CONTRIBUTING](CONTRIBUTING.md) file first.

Thanks to the contributors:

* Christian Neff (secondtruth)
