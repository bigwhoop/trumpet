<?php declare(strict_types=1);

use Bigwhoop\Trumpet\Tests\TestCase;

$container = require __DIR__.'/../bootstrap.php';
$container->set('WorkingDirectory', __DIR__.'/assets');

TestCase::setContainer($container);
