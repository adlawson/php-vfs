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

interface RegistryInterface
{
    /**
     * @param  string                    $scheme
     * @param  FileSystemInterface       $fs
     * @throws RegisteredSchemeException If a mounted file system exists at scheme
     */
    public function add($scheme, FileSystemInterface $fs);

    /**
     * @param  string                      $scheme
     * @return FileSystemInterface
     * @throws UnregisteredSchemeException If a mounted file system doesn't exist at scheme
     */
    public function get($scheme);

    /**
     * @param  string  $scheme
     * @return boolean
     */
    public function has($scheme);

    /**
     * @param  string                      $scheme
     * @return FileSystemInterface
     * @throws UnregisteredSchemeException If a mounted file system doesn't exist at scheme
     */
    public function remove($scheme);
}
