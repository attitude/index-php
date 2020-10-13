# index.php [devtool]

Walks the directory tree and generates `index.php` files for deeply nested file structures:

```
(root)
├- dir-1
|  ├- dir-1-1
|  |  ├- file-1.php
|  |  ├- file-2.php
|  |  ├- ...
|  |  ├- file-n.php
|  |  └> index.php
|  ├- dir-1-2
|  |  └> index.php
|  ├- ...
|  ├- dir-1-n
|  |  └> index.php
|  ├- file-1.php
|  ├- file-2.php
|  ├- ...
|  ├- file-n.php
|  └> index.php
├- dir-2
|  └> index.php
├- ...
├- dir-n
|  └> index.php
└> index.php
```

All the `*.php` files of the directory are required within its `index.php` as well as the `index.php`s of the subdirectories directories of the directory and so on up to the root.

## Using via command line

Only `-path` parameter is required. Use `-dry on` to test results. Turn `-backup off` as soon as you don't need backups and nothing gets lost/overwritten.

```
$ /path/to/index-php/run.sh -path "/to/walk"
```

Shorthand | Argument   | Description                       | Default
----------|------------|-----------------------------------|--------
-h        | -help      | Shows help.                       | –
-p        | -path      | Path to directory to process.     | -
-b        | -backup	   | Create backups of index files.    | `true`
-d        | -dry       | Dry run without applying changes. | `false`
-r        | -recursive | Walk directories recursively.     | `true`
-e        | -exclude   | Regex pattern to exclude files.   | –
-i        | -include   | Regex pattern to include files.   | –
