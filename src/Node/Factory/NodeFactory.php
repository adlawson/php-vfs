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

use LogicException;
use Vfs\Node\Directory;
use Vfs\Node\DirectoryLink;
use Vfs\Node\File;
use Vfs\Node\FileLink;
use Vfs\Node\FileInterface;
use Vfs\Node\NodeContainerInterface;

class NodeFactory implements NodeFactoryInterface
{
    public function buildDirectory(array $children = [])
    {
        return new Directory($children);
    }

    public function buildDirectoryLink(NodeContainerInterface $target)
    {
        return new DirectoryLink($target);
    }

    public function buildFile($content = '')
    {
        return new File($content);
    }

    public function buildFileLink(FileInterface $target)
    {
        return new FileLink($target);
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
