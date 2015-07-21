<?php
/*
 * This file is part of VFS
 *
 * Copyright (c) 2015 Andrew Lawson <http://adlawson.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Vfs;

use Vfs\Exception\RegisteredSchemeException;
use Vfs\Exception\UnregisteredSchemeException;

class FileSystemRegistry implements RegistryInterface
{
    protected static $instance;
    protected $registered = [];

    /**
     * @param FileSystemRegistry[] $fss
     */
    public function __construct(array $fss = [])
    {
        foreach ($fss as $name => $fs) {
            $this->add($name, $fs);
        }
    }

    /**
     * @return FileSystemRegistry
     */
    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function add($scheme, FileSystemInterface $fs)
    {
        if ($this->has($scheme)) {
            throw new RegisteredSchemeException($scheme);
        }

        $this->registered[$scheme] = $fs;
    }

    public function get($scheme)
    {
        if (!$this->has($scheme)) {
            throw new UnregisteredSchemeException($scheme);
        }

        return $this->registered[$scheme];
    }

    public function has($scheme)
    {
        return isset($this->registered[$scheme]);
    }

    public function remove($scheme)
    {
        if (!$this->has($scheme)) {
            throw new UnregisteredSchemeException($scheme);
        }

        unset($this->registered[$scheme]);
    }
}
