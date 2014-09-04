<?php
/*
 * This file is part of VFS
 *
 * Copyright (c) 2014 Andrew Lawson <http://adlawson.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Vfs\Node;

use DateTime;

class File implements FileInterface
{
    protected $dateAccessed;
    protected $dateCreated;
    protected $dateModified;
    protected $content;
    protected $mode;

    /**
     * @param string $content
     */
    public function __construct($content = '')
    {
        $this->content = (string) $content;
        $this->mode = self::TYPE_BLOCK & self::TYPE_FILE;

        $this->dateAccessed = new DateTime();
        $this->dateCreated  = new DateTime();
        $this->dateModified = new DateTime();
    }

    /**
     * {@inheritdoc}
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * {@inheritdoc}
     */
    public function setContent($content)
    {
        $this->content = (string) $content;
    }

    /**
     * {@inheritdoc}
     */
    public function getDateAccessed()
    {
        return $this->dateAccessed;
    }

    /**
     * {@inheritdoc}
     */
    public function getDateCreated()
    {
        return $this->dateCreated;
    }

    /**
     * {@inheritdoc}
     */
    public function getDateModified()
    {
        return $this->dateModified;
    }

    /**
     * {@inheritdoc}
     */
    public function getMode()
    {
        return $this->mode;
    }

    /**
     * {@inheritdoc}
     */
    public function getSize()
    {
        return strlen($this->content);
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return $this->content;
    }
}
