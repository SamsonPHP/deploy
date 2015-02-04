# SamsonPHP deployment system 

[![Latest Stable Version](https://poser.pugx.org/samsonphp/deploy/v/stable.svg)](https://packagist.org/packages/samsonphp/deploy) 
[![Build Status](https://travis-ci.org/SamsonPHP/deploy.svg)](https://travis-ci.org/SamsonPHP/deploy)
[![Code Coverage](https://scrutinizer-ci.com/g/samsonphp/deploy/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/samsonphp/deploy/?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/samsonphp/deploy/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/samsonphp/deploy/?branch=master) 
[![Total Downloads](https://poser.pugx.org/samsonphp/deploy/downloads.svg)](https://packagist.org/packages/samsonphp/deploy)
[![Stories in Ready](https://badge.waffle.io/samsonphp/deploy.png?label=ready&title=Ready)](https://waffle.io/samsonphp/deploy)

SamsonPHP service for automatic project deployment to specific configured environment

##Configuration  

This is done using [SamsonPHP configuration system](https://github.com/samsonphp/config)

All available configuration fields are:
```php
class DeployConfig extends \samson\core\Config 
{
    /** @var array Collection of path names to be ignored from deployment */
    public $ignorePath = array('cms');

    /** Path to site document root on local server */
    public $sourceroot = '';

    /** FTP host */
    public $host 	= '';

    /** Path to site document root on remote server */
    public $wwwroot	= '';

    /** FTP username */
    public $username= '';

    /** FTP password */
    public $password= '';
}
