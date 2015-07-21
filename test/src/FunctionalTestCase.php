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
use RuntimeException;
use Vfs\FileSystemBuilder;

class FunctionalTestCase extends TestCase
{
    protected $scheme = 'foo';

    public function setUp()
    {
        $builder = new FileSystemBuilder($this->scheme);
        $this->fs = $builder->build();
    }

    public function tearDown()
    {
        if ($this->isMounted($this->scheme)) {
            $this->fs->unmount();

            if ($this->isMounted($this->scheme)) {
                throw new RuntimeException('Problem unmounting file system ' . $this->scheme);
            }
        }
    }

    protected function isMounted($scheme)
    {
        return in_array($scheme, stream_get_wrappers());
    }
}
