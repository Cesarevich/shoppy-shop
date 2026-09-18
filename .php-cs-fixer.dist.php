<?php

declare(strict_types=1);

use PhpCsFixer\Config;
use PhpCsFixer\Finder;

$finder = Finder::create()
    ->in([
        __DIR__ . '/bin',
        __DIR__ . '/config',
        __DIR__ . '/migrations',
        __DIR__ . '/public',
        __DIR__ . '/src',
        __DIR__ . '/tests',
    ])
    ->name([
        '*.php',
        'console',
    ]);

return (new Config())
    ->setRiskyAllowed(false)
    ->setRules([
        '@PSR12' => true,
        '@PER-CS2x0' => true,
        '@DoctrineAnnotation' => true,
        'no_unused_imports' => true,
        'php_unit_method_casing' => ['case' => 'camel_case'],
    ])
    ->setFinder($finder);
