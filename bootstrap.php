<?php declare(strict_types=1);

namespace Trumpet;

use Interop\Container\ContainerInterface as Container;

error_reporting(E_ALL);
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');

require __DIR__ . '/vendor/autoload.php';

/** @var Container $container */
$container = require __DIR__ . '/conf/di.php';

set_error_handler($container->get('ErrorHandler'));

return $container;
