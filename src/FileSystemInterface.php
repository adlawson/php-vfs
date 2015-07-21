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

use Psr\Log\LoggerInterface;
use Vfs\Exception\RegisteredSchemeException;
use Vfs\Exception\UnregisteredSchemeException;
use Vfs\Node\Factory\NodeFactoryInterface;
use Vfs\Node\Walker\NodeWalkerInterface;
use Vfs\Node\NodeInterface;

interface FileSystemInterface
{
    const SCHEME = 'vfs';

    /**
     * @param $path
     * @return NodeInterface
     */
    public function get($path);

    /**
     * @return LoggerInterface
     */
    public function getLogger();

    /**
     * @return NodeFactoryInterface
     */
    public function getNodeFactory();

    /**
     * @return NodeWalkerInterface
     */
    public function getNodeWalker();

    /**
     * @return string
     */
    public function getScheme();

    /**
     * @return boolean
     * @throws RegisteredSchemeException If a mounted file system exists at scheme
     */
    public function mount();

    /**
     * @return boolean
     * @throws UnregisteredSchemeException If a mounted file system doesn't exist at scheme
     */
    public function unmount();
}
