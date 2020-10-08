# index.php [devtool]

Walks the directory tree and generates `index.php` files for deeply nested file structures.

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

