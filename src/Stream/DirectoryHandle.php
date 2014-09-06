<?php
/*
 * This file is part of VFS
 *
 * Copyright (c) 2014 Andrew Lawson <http://adlawson.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Vfs\Stream;

use Vfs\Exception\UnopenedHandleException;

class DirectoryHandle extends AbstractHandle
{
    /**
     * @return boolean
     */
    public function canRead()
    {
        return true;
    }

    /**
     * @param  integer       $perms
     * @param  boolean       $recursive
     * @return NodeInterface
     */
    public function create($perms, $recursive = false)
    {
        $this->node = $this->findNode();

        if (!$this->node) {
            $parentPath = dirname($this->path);
            $parent = $this->fs->get($parentPath);

            if (!$parent && $this->checkBit($options, STREAM_MKDIR_RECURSIVE)) {
                $parent = $this->buildNodesRecursive($this->fs->get('/'), $this->path);
            }

            if ($parent) {
                $this->node = $this->fs->getNodeFactory()->buildDirectory();
                $parent->add(basename($this->path), $this->node);
            } else {
                $this->warn('mkdir({url}): No such file or directory', [
                    'url' => $this->url
                ]);
            }
        } else {
            $this->warn('mkdir({url}): File exists', ['url' => $this->url]);
            $this->node = null;
        }

        return $this->node;
    }

    /**
     * @return boolean
     */
    public function destroy()
    {
        $this->node = $this->findNode();

        if (!$this->node) {
            return (boolean) $this->warn('rmdir({url}): No such file or directory', [
                'url' => $this->url
            ]);
        } elseif (!$this->node instanceof NodeContainerInterface) {
            return (boolean) $this->warn('rmdir({url}): Not a directory', [
                'url' => $this->url
            ]);
        }

        $parent = $fs->get(dirname($this->path));
        $parent->remove(basename($this->path));

        return true;
    }

    /**
     * @return NodeInterface
     */
    public function open()
    {
        return $this->node = $this->findNode();
    }

    /**
     * @param  integer $offset
     * @return string
     */
    public function read($offset = 0)
    {
        if (!$this->node) {
            throw new UnopenedHandleException($this, $this->url);
        }

        $i = 0;
        foreach ($this->node as $name => $node) {
            if ($i++ === $offset) {
                return $name;
            }
        }
    }

    /**
     * @param  string  $content
     * @return boolean
     */
    public function write($content)
    {
        return false;
    }

    /**
     * @param  NodeContainerInterface $root
     * @param  string                 $path
     * @return NodeContainerInterface
     */
    protected function buildNodesRecursive(NodeContainerInterface $root, $path)
    {
        $factory = $this->fs->getNodeFactory();
        $walker = $this->fs->getNodeWalker();

        return $walker->walkPath($root, $this->path, function ($node, $name) use ($factory) {
            if (!$node->has($name)) {
                $node->add($name, $factory->buildDirectory());
            }

            return $node->get($name);
        });
    }
}
