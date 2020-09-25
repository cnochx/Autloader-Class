# Autoloader-Class
An Autoloder-Class intended to be used as a default implementation for __autoload().

Highly inspired from the PSR-4 autoloader: https://www.php-fig.org/psr/psr-4 and the sample code: https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-4-autoloader-examples.md.

The Autoloader-Class is searching inside given Strukture and give back a `require_once('base/directory/of/that/File.php')`.

## Integrate this autoloader with: 
```php
$Loading = new App\Autoloader\Autoloader;
```

## Add the Strukture of that App:
```php
$Loading->setStruktur('core');
$Loading->setStruktur('app/controllers');
```
 
## Add the Root Directory of that App
```php
$Loading->setRoot(dirname(__FILE__));
```
 
## Set the Classes
```php
$Loading->setClass('core\Bootstrap', 'Bootstrap');
$Loading->setClass('core\Controller', 'Controller');
$Loading->setClass('app\controllers\User', 'User');
```

## Register 
 ```php
$Loading->register()
```

## Call the Classes in your Awasome Code
```php
$InstantiatedBootstrap = new core\Bootstrap\Bootstrap;
$InstantiatedController = new core\Controller\Controller;
$InstantiatedUser = new app\controllers\User\User;
```


:pray:
