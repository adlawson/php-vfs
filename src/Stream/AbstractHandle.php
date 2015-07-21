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

use Vfs\FileSystemInterface;
use Vfs\Node\NodeInterface;

abstract class AbstractHandle implements HandleInterface
{
    protected $fs;
    protected $node;
    protected $mode;
    protected $modifier;
    protected $path;
    protected $scheme;
    protected $url;

    /**
     * @param FileSystemInterface $fs
     * @param string              $url
     * @param string              $mode
     */
    public function __construct(FileSystemInterface $fs, $url, $mode = null)
    {
        $this->fs = $fs;
        $this->url = $url;

        list($this->mode, $this->modifier) = $this->parseMode($mode);
        list($this->scheme, $this->path) = $this->parseUrl($url);
    }

    public function getNode()
    {
        return $this->node;
    }

    public function rename($target)
    {
        $this->node = $this->findNode($this->path);
        $parent = $this->fs->get(dirname($this->path));

        list($_, $targetPath) = $this->parseUrl($target);
        $targetParent = $this->fs->get(dirname($targetPath));

        if (!$this->node || !$targetPath) {
            $this->node = null;
            $this->warn('rename({origin},{target}): No such file or directory', [
                'origin' => $this->url,
                'target' => $target
            ]);
        } else {
            $parent->remove(basename($this->path));
            $targetParent->add(basename($targetPath), $this->node);
        }

        return $this->node;
    }

    /**
     * @return NodeInterface
     */
    protected function findNode()
    {
        return $this->fs->get($this->path);
    }

    /**
     * @param  string   $mode
     * @return string[]
     */
    protected function parseMode($mode)
    {
        return [substr($mode, 0, 1), substr($mode, 1, 2)];
    }

    /**
     * @param  string   $url
     * @return string[]
     */
    protected function parseUrl($url)
    {
        $parts = parse_url($url);
        $path = null;
        $scheme = null;

        if (isset($parts['scheme'])) {
            $scheme = $parts['scheme'];
        } else {
            $scheme = strstr($url, '://', true);
        }

        $path = '/' . ltrim(substr($url, strlen($scheme)), ':\/');

        return [$scheme, $path];
    }

    /**
     * @param string $message
     * @param array  $context
     */
    protected function warn($message, array $context = [])
    {
        $this->fs->getLogger()->warning($message, $context);
    }
}
