<?php

namespace IndexPHP;

define('DO_NOT_EDIT_DIVIDER', '//------------------ DO NOT EDIT ABOVE THIS LINE ------------------//');

class Generator {
  private $config = [
    'backup' => false,
    'dry' => true,
    'path' => null,
    'recursive' => false,
  ];

  public function __construct(array $config = []) {
    $this->config = array_merge($this->config, $config);

    if (!is_dir($this->config['path'])) {
      throw new \Exception("Path is not a directory: {$this->config['path']}", 500);
    }
  }

  private function isRecursive() {
    return !!$this->config['recursive'];
  }

  private function isDryRun() {
    return !!$this->config['dry'];
  }

  public function createBackup() {
    return !!$this->config['backup'];
  }

  private function walk(string $path) {
    if ($this->isRecursive()) {
      foreach (glob("${path}/*", GLOB_ONLYDIR) as $dir) {
        $this->walk($dir);
      }
    }

    $files = array_map(function($file) use ($path) {
      return str_replace($path, '', $file);
    }, glob("${path}/*/index.php"));

    $files = array_merge($files, array_filter(array_map(function($file) use ($path) {
      if (basename($file) === 'index.php') {
        return;
      }

      return str_replace($path, '', $file);
    }, glob("${path}/*.php"))));

    printLine("${path}:");
    echo "|- ".implode("\n|- ", $files)."\n";

    $indexContent =
      "<?php\n".
      "// This list of required files was automatically generated:\n".
      "\n".
      implode("\n", array_map(function($file) { return "require_once(__DIR__.'${file}');"; }, $files));

    if (!$this->isDryRun()) {
      $destination = $path.'/index.php';

      $this->maybeReplaceFileContent($destination, $indexContent);
    }
  }

  public function maybeReplaceFileContent(string $destination, string $newContent) {
    $oldContent = '';
    $rest = '';

    if (file_exists($destination)) {
      list($oldContent, $rest) = explode(DO_NOT_EDIT_DIVIDER, file_get_contents($destination));
    }

    $oldContent = trim($oldContent);
    $newContent = trim($newContent);

    if ($oldContent === $newContent) {
      printLine("✅ Already up-to-date\n");
      return;
    }

    if ($this->createBackup() && file_exists($destination)) {
      rename($destination, $destination.'.'.time().'.bkp');
    }

    if (!file_put_contents($destination, trim($newContent."\n\n".DO_NOT_EDIT_DIVIDER."\n\n".trim($rest)."\n")."\n")) {
      exitWithErrorMessage("Failed to write index file: ${destination}");
    }
  }

  public function run() {
    $this->walk($this->config['path']);
  }
}