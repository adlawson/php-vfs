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

use Vfs\Node\NodeContainerInterface;
use Vfs\Node\NodeInterface;

class NodeWalker implements NodeWalkerInterface
{
    protected $separator;

    /**
     * @param string $separator
     */
    public function __construct($separator = DIRECTORY_SEPARATOR)
    {
        $this->separator = $separator;
    }

    public function findNode(NodeInterface $root, $path)
    {
        return $this->walkPath($root, $path, function (NodeInterface $node, $name) {
            if (!$node instanceof NodeContainerInterface || !$node->has($name)) {
                return null;
            }

            return $node->get($name);
        });
    }

    public function walkPath(NodeInterface $root, $path, callable $fn)
    {
        $parts = $this->splitPath($path);
        $name = current($parts);
        $node = $root;

        while ($node && $name) {
            $node = $fn($node, $name);
            $name = next($parts);
        }

        return $node;
    }

    /**
     * @param  string   $path
     * @return string[]
     */
    protected function splitPath($path)
    {
        $path = str_replace('/', DIRECTORY_SEPARATOR, $path);

        return array_filter(explode($this->separator, $path));
    }
}
