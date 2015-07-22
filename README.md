# VFS (Virtual File System)

<img src="http://media.giphy.com/media/d6Unw9Ke0vCFO/giphy.gif" alt="Virtual File System" align="right" width=310/>

[![Master branch build status][ico-build]][travis]
[![Published version][ico-package]][package]
[![PHP ~5.4][ico-engine]][lang]
[![MIT Licensed][ico-license]][license]

**VFS** is a virtual file system for PHP built using the stream wrapper API.
Streams are exposed just as typical `file://` or `http://` streams are to PHP's
built-in functions and keywords like `fopen` and `require`. This implementation
attempts to stay true to the typical streams, including triggering warnings
and handling edge cases appropriately.

It can be installed in whichever way you prefer, but I recommend
[Composer][package].
```json
{
    "require": {
        "adlawson/vfs": "*"
    }
}
```

## Documentation
After creating and mounting the file system, you have the option of manipulating
the virtual file system either via PHP's built-in functions, the VFS interfaces,
or interfaces provided by another file system library.
```php
<?php
use Vfs\FileSystem;
use Vfs\Node\Directory;
use Vfs\Node\File;

// Create and mount the file system
$fs = FileSystem::factory('vfs://');
$fs->mount();

// Add `/foo` and `/foo/bar.txt`
$foo = new Directory(['bar.txt' => new File('Hello, World!')]);
$fs->get('/')->add('foo', $foo);

// Get contents of `/foo/bar.txt`
$fs->get('/foo/bar.txt')->getContent(); // Hello, World!
file_get_contents('vfs://foo/bar.txt'); // Hello, World!

// Add `/foo/bar` and `/foo/bar/baz.php`
mkdir('vfs://foo/bar');
file_put_contents('vfs://foo/bar.php', '<?php echo "Hello, World!";');

// Require `/foo/bar.php`
require 'vfs://foo/baz.php';

// Works with any other file system library too
$symfony = new Symfony\Component\Filesystem\Filesystem();
$symfony->mkdir('vfs://foo/bar/baz');
$laravel = new Illuminate\Filesystem();
$laravel->isDirectory('vfs://foo/bar/baz'); //true

// Triggers PHP warnings on error just like typical streams
rename('vfs://path/to/nowhere', 'vfs://path/to/somewhere');
// PHP Warning: rename(vfs://path/to/nowhere,vfs://path/to/somewhere): No such file or directory in /srv/index.php on line 1; triggered in /srv/src/Logger/PhpErrorLogger.php on line 32
```

### Example use cases
If you need to ask what you'd use a virtual file system for, you probably don't
need one, but just in case, I've compiled a small list of examples:
 - Testing file system libraries without writing to disc
 - Runtime evaluation without `eval` (via `write` and `require`)
 - ...we need some more!

### Todo
Current tasks are listed on the [github issues][issues] page, but some are
listed here for reference:
 - Symlinks
 - File locks
 - Permissions/ACL


## Contributing
Contributions are accepted via Pull Request, but passing unit tests must be
included before it will be considered for merge.
```bash
$ curl -O https://raw.githubusercontent.com/adlawson/vagrantfiles/master/php/Vagrantfile
$ vagrant up
$ vagrant ssh
...

$ cd /srv
$ composer install
$ vendor/bin/phpunit
```

### License
The content of this library is released under the **MIT License** by
**Andrew Lawson**.<br/> You can find a copy of this license in
[`LICENSE`][license] or at http://opensource.org/licenses/mit.

[travis]: https://travis-ci.org/adlawson/php-vfs
[lang]: http://php.net
[package]: https://packagist.org/packages/adlawson/vfs
[ico-license]: http://img.shields.io/packagist/l/adlawson/vfs.svg?style=flat
[ico-package]: http://img.shields.io/packagist/v/adlawson/vfs.svg?style=flat
[ico-build]: http://img.shields.io/travis/adlawson/php-vfs/master.svg?style=flat
[ico-engine]: http://img.shields.io/badge/php-~5.4-8892BF.svg?style=flat
[issues]: https://github.com/adlawson/php-vfs/issues
[license]: LICENSE
