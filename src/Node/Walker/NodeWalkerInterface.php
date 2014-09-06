<?php
/*
 * This file is part of VFS
 *
 * Copyright (c) 2014 Andrew Lawson <http://adlawson.com>
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
}
