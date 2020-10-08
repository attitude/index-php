<?php

function printLine($message = "") {
  echo "${message}\n";
}

function exitWithMessage($message = '') {
  exit("${message}\n");
}

function exitWithErrorMessage($message = '') {
  exit("[ERROR]: ${message}\n");
}

if (!isset($config)) {
  exitWithMessage('Please define `array $config` variable first. Don\'t miss any keys as it serves as the defaults.');
}

/**
 * type ArgumentsConfig = {
 *   // Short parameter is the key
 *   string: {
 *     !name: !string, // Full parameter name [required]
 *     !description: !string, // Description of the paramter shown in help [required]
 *     type: 'string' | 'boolean' | 'number', // Enum of types to cast, default is 'string'.
 *     required: bool, // Sets parameter as required.
 *   }
 * }[]
 */
if (!isset($argumentsConfig)) {
  exitWithErrorMessage('Please define `array $argumentsConfig` variable first.');
}

$argumentNamesMap = [];

foreach($argumentsConfig as $shortName => $argumentConfig) {
    $name = $argumentsConfig[$shortName]['name'];
    $argumentNamesMap[$name] = $shortName;
}

function getUsage($argumentsConfig = []) {
  return
    "[USAGE]:\n\n".
    "-h,\t-help\t\tShows this help.\n".
    implode(
      "\n",
      array_map(function($abbr = '', $definition = []) {
        return "-${abbr},\t-{$definition['name']}\t{$definition['description']}";
      }, array_keys($argumentsConfig), array_values($argumentsConfig))
    );
}

$arguments = array_slice($argv, 1);

if (
  in_array('-h', $arguments) ||
  in_array('-help', $arguments) ||
  in_array('--h', $arguments) ||
  in_array('--help', $arguments)
) {
  printLine(getUsage($argumentsConfig));
  exit;
}

function boolify($value) {
  if (is_string($value)) { $value = strtolower($value); }

  if (in_array($value, ['no', 'off', 'false', 0, '0', 'n', 'null', null], true)) {
    return false;
  }

  return true;
}

function numberify($value) {
  return (float) $value;
}

while (sizeof($arguments) > 0) {
  $maybeLongArgumentName = trim(array_shift($arguments), '-');

  $argumentName = isset($argumentNamesMap[$maybeLongArgumentName])
    ? $argumentNamesMap[$maybeLongArgumentName]
    : $maybeLongArgumentName;

  $argumentValue = array_shift($arguments);

  if (!array_key_exists($argumentName, $argumentsConfig)) {
    exitWithErrorMessage("Unknown argument `${argumentName}`.\n\n".getUsage($argumentsConfig));
  }

  $argumentConfig = $argumentsConfig[$argumentName];

  $argumentName = $argumentConfig['name'];
  $argumentType = $argumentConfig['type'];

  if (!array_key_exists($argumentName, $config)) {}

  if (!isset($argumentValue)) {
    exitWithErrorMessage("Argument `${$argumentName}` requires value.");
  }

  switch($argumentType) {
    case 'boolean':
    case 'bool':
      $config[$argumentName] = boolify($argumentValue);
    break;

    case 'number':
    case 'float':
    case 'int':
    case 'numeric':
      $config[$argumentName] = numberify($argumentValue);
    break;

    default:
      $config[$argumentName] = $argumentValue;
    break;
  }
}

// Check required
foreach ($argumentsConfig as $argumentConfig) {
  if (
    isset($argumentConfig['required']) &&
    $argumentConfig['required'] &&
    $config[$argumentConfig['name']] === null
  ) {
    $name = $argumentConfig['name'];
    exitWithErrorMessage("Argument `${name}` is required");
  }
}
