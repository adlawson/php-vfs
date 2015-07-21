<?php
/*
 * This file is part of VFS
 *
 * Copyright (c) 2015 Andrew Lawson <http://adlawson.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Vfs\Node\Factory;

use Vfs\Node\NodeContainerInterface;
use Vfs\Node\NodeInterface;

interface NodeFactoryInterface
{
    /**
     * @param  NodeInterface[]        $children
     * @return NodeContainerInterface
     */
    public function buildDirectory(array $children = []);

    /**
     * @param  string        $content
     * @return NodeInterface
     */
    public function buildFile($content = '');

    /**
     * @param  string        $content
     * @return NodeInterface
     */
    public function buildLink($content = '');

    /**
     * @param  array                  $tree
     * @return NodeContainerInterface
     */
    public function buildTree(array $tree);
}
