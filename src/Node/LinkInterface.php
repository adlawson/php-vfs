<?php
/*
 * This file is part of VFS
 *
 * Copyright (c) 2015 Andrew Lawson <http://adlawson.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Vfs\Node;

/**
 * Link node type
 *
 * This is an implementation of a hard link rather than a symbolic link; the
 * key difference being it links directly to a node and not a path. If the
 * target node is moved or renamed the link remains intact.
 *
 * The implementation is a simple proxy, whereby most other method calls should
 * proxy through to the target where suitable.
 */
interface LinkInterface extends NodeInterface
{
    /**
     * @return NodeInterface
     */
    public function getTarget();
}
