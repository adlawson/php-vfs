<?php
/*
 * This file is part of VFS
 *
 * Copyright (c) 2014 Andrew Lawson <http://adlawson.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Vfs\Node\Factory;

use LogicException;
use Vfs\Node\Directory;
use Vfs\Node\File;
use Vfs\Node\NodeContainerInterface;
use Vfs\Node\NodeInterface;

class NodeFactory implements NodeFactoryInterface
{
    public function buildDirectory(array $children = [])
    {
        return new Directory($children);
    }

    public function buildFile($content = '')
    {
        return new File($content);
    }

    public function buildLink($content = '')
    {
        throw new LogicException('Symlinks aren\'t supported yet.');
    }

    public function buildTree(array $tree)
    {
        $nodes = [];

        foreach ($tree as $name => $content) {
            if (is_array($content)) {
                $nodes[$name] = $this->buildTree($content);
            } else {
                $nodes[$name] = $this->buildFile($content);
            }
        }

        return $this->buildDirectory($nodes);
    }
}
