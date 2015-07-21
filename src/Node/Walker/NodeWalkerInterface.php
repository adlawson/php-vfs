<?php
/*
 * This file is part of VFS
 *
 * Copyright (c) 2015 Andrew Lawson <http://adlawson.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Vfs\Node\Walker;

use Vfs\Node\NodeInterface;

interface NodeWalkerInterface
{
    /**
     * @param  NodeInterface $root
     * @param  string        $path
     * @return NodeInterface
     */
    public function findNode(NodeInterface $root, $path);

    /**
     * @param  NodeInterface $root
     * @param  string        $path
     * @param  callable      $fn
     * @return NodeInterface
     */
    public function walkPath(NodeInterface $root, $path, callable $fn);
}
