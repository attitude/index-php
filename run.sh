#!/usr/bin/php
<?php

$config = [
  'backup' => true,
  'dry' => false,
  'path' => null,
  'recursive' => true,
];

$argumentsConfig = [
  'p' => [
    'name' => 'path',
    'description' => 'Path to directory to process.',
    'required' => true,
  ],
  'b' => [
    'name' => 'backup',
    'description' => 'Create backups of index files. Default true.',
    'type' => 'boolean',
  ],
  'd' => [
    'name' => 'dry',
    'description' => 'Dry run without applying changes.',
    'type' => 'boolean',
  ],
  'r' => [
    'name' => 'recursive',
    'description' => 'Walk directories recursively.',
    'type' => 'boolean',
  ],
];

require_once('bootstrap.php');

if ($config['path'] === '.') {
  $config['path'] = getcwd();
}

require_once(__DIR__.'/index.php');

use IndexPHP\Generator;
(new Generator($config))->run();


echo "\n";
