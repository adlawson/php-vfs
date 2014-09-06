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

use DateTime;
use Vfs\Exception\UnopenedHandleException;
use Vfs\Node\FileInterface;
use Vfs\Node\NodeContainerInterface;

class FileHandle extends AbstractHandle
{
    /**
     * @return boolean
     */
    public function canRead()
    {
        return self::MODE_READ === $this->mode || self::MOD_EXTENDED === $this->modifier;
    }

    /**
     * @param  integer       $perms
     * @return NodeInterface
     */
    public function create($perms)
    {
        return $this->node = $this->findOrBuildNode();
    }

    /**
     * @return boolean
     */
    public function destroy()
    {
        $this->node = $this->findNode();

        if (!$this->node) {
            return (boolean) $this->warn('unlink({url}): No such file or directory', [
                'url' => $this->url
            ]);
        } elseif ($this->node instanceof NodeContainerInterface) {
            return (boolean) $this->warn('unlink({url}): Is a directory', [
                'url' => $this->url
            ]);
        }

        $parent = $this->fs->get(dirname($this->path));
        $parent->remove(basename($this->path));

        return true;
    }

    /**
     * @return NodeInterface
     */
    public function open()
    {
        $this->node = $this->findOrBuildNode();

        if ($this->node instanceof FileInterface && self::MODE_TRUNCATE === $this->mode) {
            $this->node->setContent('');
        }

        return $this->node;
    }

    /**
     * @param  integer $offset
     * @param  integer $length
     * @return string
     */
    public function read($offset = 0, $length = null)
    {
        if (!$this->node) {
            throw new UnopenedHandleException($this, $this->url);
        } elseif (!$this->node instanceof FileInterface) {
            return '';
        }

        if (null !== $length) {
            return substr($this->node->getContent(), $offset, $length);
        }

        return substr($this->node->getContent(), $offset);
    }

    /**
     * @param  DateTime      $mtime
     * @param  DateTime      $atime
     * @return NodeInterface
     */
    public function touch(DateTime $mtime = null, DateTime $atime = null)
    {
        $node = $this->findOrBuildNode();

        if (!$node) {
            throw new UnopenedHandleException($this, $this->url);
        }

        $mtime = $mtime ?: new DateTime();
        $atime = $atime ?: clone $mtime;

        $node->setDateAccessed($atime);
        $node->setDateModified($mtime);

        return $node;
    }

    /**
     * @param  string  $content
     * @return boolean
     */
    public function write($content)
    {
        if (!$this->node) {
            throw new UnopenedHandleException($this, $this->url);
        }

        $this->node->setContent($content);

        return true;
    }

    /**
     * @return NodeInterface
     */
    protected function findOrBuildNode()
    {
        $this->node = $this->fs->get($this->path);

        if ($this->node && self::MODE_WRITE === $this->mode) {
            $this->node = null;
        } elseif (!$this->node && in_array($this->mode, [
            self::MODE_APPEND,
            self::MODE_TRUNCATE,
            self::MODE_WRITE,
            self::MODE_WRITE_NEW
        ])) {
            $dir = $this->fs->get(dirname($this->path));

            if ($dir && $dir instanceof NodeContainerInterface) {
                $this->node = $this->fs->getNodeFactory()->buildFile();
                $dir->set(basename($this->path), $this->node);
            }
        }

        return $this->node;
    }
}
