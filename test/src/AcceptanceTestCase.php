<?php
/*
 * This file is part of VFS
 *
 * Copyright (c) 2015 Andrew Lawson <http://adlawson.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Vfs\Test;

use PHPUnit_Framework_TestCase as TestCase;
use Vfs\FileSystemBuilder;
use Vfs\FileSystemInterface;

class AcceptanceTestCase extends TestCase
{
    protected $fs;
    protected $tree = [];
    protected $scheme = 'vfs';
    protected $wrapperClass = 'Vfs\Stream\StreamWrapper';

    public function setUp()
    {
        $this->fs = $this->buildFileSystem();
    }

    public function tearDown()
    {
        if (in_array($this->scheme, stream_get_wrappers())) {
            $this->fs->unmount();
        }
    }

    protected function buildFileSystem()
    {
        $builder = new FileSystemBuilder($this->scheme);
        $builder->setStreamWrapper($this->wrapperClass);
        $builder->setTree($this->tree);

        $fs = $builder->build();
        $fs->mount();

        return $fs;
    }
}
