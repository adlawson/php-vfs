<?php
/*
 * This file is part of VFS
 *
 * Copyright (c) 2015 Andrew Lawson <http://adlawson.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Vfs\Stream;

use Vfs\Node\NodeInterface;

interface HandleInterface
{
    const MODE_APPEND    = 'a';
    const MODE_READ      = 'r';
    const MODE_TRUNCATE  = 'w';
    const MODE_WRITE     = 'x';
    const MODE_WRITE_NEW = 'c';
    const MOD_BINARY     = 'b';
    const MOD_EXTENDED   = '+';
    const MOD_TEXT       = 't';

    /**
     * @return boolean
     */
    public function canRead();

    /**
     * @param  integer       $perms
     * @return NodeInterface
     */
    public function create($perms);

    /**
     * @return boolean
     */
    public function destroy();

    /**
     * @return NodeInterface
     */
    public function getNode();

    /**
     * @return NodeInterface
     */
    public function open();

    /**
     * @param  string        $origin
     * @param  string        $target
     * @return NodeInterface
     */
    public function rename($target);

    /**
     * @param  integer $offset
     * @return string
     */
    public function read($offset = 0);

    /**
     * @param  string  $content
     * @return boolean
     */
    public function write($content);
}
