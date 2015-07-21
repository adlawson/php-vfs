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

use DateTime;
use Vfs\Node\LinkInterface;
use Vfs\FileSystemRegistry;

class StreamWrapper
{
    protected $cursor = 0;
    protected $handle;
    protected $mode;
    protected $url;

    /**
     * @return boolean
     */
    public function dir_closedir()
    {
        $this->stream_close();

        return true;
    }

    /**
     * @param  string  $url
     * @param  integer $options
     * @return boolean
     */
    public function dir_opendir($url, $options)
    {
        $this->handle = $this->buildDirectoryHandle($url);

        return (boolean) $this->handle->open();
    }

    /**
     * @return string
     */
    public function dir_readdir()
    {
        return $this->handle->read($this->cursor++);
    }

    /**
     * @return boolean
     */
    public function dir_rewinddir()
    {
        $this->cursor = 0;

        return true;
    }

    /**
     * @param  string  $url
     * @param  integer $perms
     * @param  integer $flags
     * @return boolean
     */
    public function mkdir($url, $perms, $flags)
    {
        $this->handle = $this->buildDirectoryHandle($url);
        $recursive = $this->checkBit($flags, STREAM_MKDIR_RECURSIVE);

        return (boolean) $this->handle->create($perms, $recursive);
    }

    /**
     * @param  string  $origin
     * @param  string  $target
     * @return boolean
     */
    public function rename($origin, $target)
    {
        $this->handle = $this->buildFileHandle($origin);

        return (boolean) $this->handle->rename($target);
    }

    /**
     * @param  string  $url
     * @param  integer $options
     * @return boolean
     */
    public function rmdir($url, $options)
    {
        $this->handle = $this->buildDirectoryHandle($url);

        return (boolean) $this->handle->destroy();
    }

    /**
     * @param  integer          $cast
     * @return resource|boolean
     */
    public function stream_cast($cast)
    {
        return false; // No underlying resource
    }

    /**
     */
    public function stream_close()
    {
        $this->cursor = 0;
        $this->handle = null;
    }

    /**
     * @return boolean
     */
    public function stream_eof()
    {
        $node = $this->handle->getNode();

        return !$node || $node->getSize() <= $this->cursor;
    }

    /**
     * @return boolean
     */
    public function stream_flush()
    {
        return true; // Non-buffered writing
    }

    /**
     * @param string $url
     * @param integer $option
     * @param mixed $args
     * @return boolean
     */
    public function stream_metadata($url, $option, $args)
    {
        if (STREAM_META_TOUCH === $option) {
            $this->handle = $this->buildFileHandle($url, HandleInterface::MODE_WRITE_NEW);

            $mtime = isset($args[0]) ? new DateTime(sprintf('@%s', $args[0])) : null;
            $atime = isset($args[1]) ? new DateTime(sprintf('@%s', $args[1])) : $mtime;

            $this->handle->touch($mtime, $atime);

            return true;
        }

        return false;
    }

    /**
     * @param  string  $url
     * @param  string  $mode
     * @param  integer $options
     * @param  string  $openedPath
     * @return boolean
     */
    public function stream_open($url, $mode, $options, &$openedPath)
    {
        $this->cursor = 0;
        $this->handle = $this->buildFileHandle($url, $mode);
        $node = $this->handle->open();

        if ($node && $this->checkBit($options, STREAM_USE_PATH)) {
            $openedPath = $url;
        }

        if (isset($mode[0]) && $node && HandleInterface::MODE_APPEND === $mode[0]) {
            $this->cursor = $node->getSize();
        }

        return (boolean) $node;
    }

    /**
     * @param  integer        $length
     * @return string|boolean
     */
    public function stream_read($length)
    {
        if ($this->handle->canRead()) {
            $out = $this->handle->read($this->cursor, $length);
            $this->cursor += strlen($out);

            return $out;
        }

        return false;
    }

    /**
     * @param  integer $offset
     * @param  integer $whence
     * @return boolean
     */
    public function stream_seek($offset, $whence = SEEK_SET)
    {
        switch ($whence) {
            case SEEK_SET:
                $this->cursor = (integer) $offset;
                break;
            case SEEK_CUR:
                $this->cursor += (integer) $offset;
                break;
            case SEEK_END:
                $length = strlen($this->wrapper->read());
                $this->cursor = $length + (integer) $offset;
                break;
            default:
                return false;
        }

        return true;
    }

    /**
     * @param  integer $option
     * @param  integer $arg1
     * @param  integer $arg2
     * @return boolean
     */
    public function stream_set_option($option, $arg1, $arg2)
    {
        return true;
    }

    /**
     * @param  boolean       $followLink
     * @return array|boolean
     */
    public function stream_stat($followLink = false)
    {
        $node = $this->handle->getNode();

        if (!$node) {
            return false;
        } elseif ($followLink && $node instanceof LinkInterface) {
            $node = $node->getTarget();
        }

        $stat = [
            'dev'     => 0,
            'ino'     => 0,
            'mode'    => $node->getMode(),
            'nlink'   => 0,
            'uid'     => 0,
            'gid'     => 0,
            'rdev'    => 0,
            'size'    => $node->getSize(),
            'atime'   => $node->getDateAccessed()->getTimestamp(),
            'mtime'   => $node->getDateModified()->getTimestamp(),
            'ctime'   => $node->getDateCreated()->getTimestamp(),
            'blksize' => -1,
            'blocks'  => -1
        ];

        return array_values($stat) + $stat;
    }

    /**
     * @return integer
     */
    public function stream_tell()
    {
        return $this->cursor;
    }

    /**
     * @param  integer $size
     * @return boolean
     */
    public function stream_truncate($size)
    {
        if ($size > $current) {
            $this->handle->write($this->handle->read() . str_repeat("\0", $size - $current));
        } else {
            $this->handle->write($this->handle->read(0, $size));
        }

        return true;
    }

    /**
     * @param  string  $data
     * @return integer
     */
    public function stream_write($data)
    {
        $content = substr($this->handle->read(0, $this->cursor), 0, $this->cursor) . $data;
        $written = $this->handle->write($content);

        $this->cursor = strlen($content);

        return $written ? strlen($data) : 0;
    }

    /**
     * @param  string  $url
     * @return boolean
     */
    public function unlink($url)
    {
        $this->handle = $this->buildFileHandle($url);

        return (boolean) $this->handle->destroy();
    }

    /**
     * @param  string  $url
     * @param  integer $flags
     * @return array
     */
    public function url_stat($url, $flags)
    {
        $this->handle = $this->buildFileHandle($url);
        $this->handle->open();

        return $this->stream_stat(!$this->checkBit($flags, STREAM_URL_STAT_LINK));
    }

    /**
     * @param  string          $url
     * @return DirectoryHandle
     */
    protected function buildDirectoryHandle($url)
    {
        return new DirectoryHandle($this->getFileSystemForUrl($url), $url);
    }

    /**
     * @param  string     $url
     * @param  string     $mode
     * @return FileHandle
     */
    protected function buildFileHandle($url, $mode = null)
    {
        return new FileHandle($this->getFileSystemForUrl($url), $url, $mode);
    }

    /**
     * @param  integer $mask
     * @param  integer $bit
     * @return boolean
     */
    protected function checkBit($mask, $bit)
    {
        return ($mask & $bit) === $bit;
    }

    /**
     * @param  string              $url
     * @return FileSystemInterface
     */
    protected function getFileSystemForUrl($url)
    {
        $parts = parse_url($url);
        $scheme = isset($parts['scheme']) ? $parts['scheme'] : strstr($url, '://', true);

        return FileSystemRegistry::getInstance()->get($scheme);
    }
}
