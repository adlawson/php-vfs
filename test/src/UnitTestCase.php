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

class UnitTestCase extends TestCase
{
    protected $factory;
    protected $fs;
    protected $logger;
    protected $registry;
    protected $scheme;
    protected $walker;
    protected $wrapperClass;
}
