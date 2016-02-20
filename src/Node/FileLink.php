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

use DateTime;

class FileLink implements FileInterface, LinkInterface
{
    protected $dateAccessed;
    protected $dateCreated;
    protected $dateModified;
    protected $file;
    protected $mode;

    /**
     * @param FileInterface $file
     */
    public function __construct(FileInterface $file)
    {
        $this->file = $file;
        $this->mode = self::TYPE_LINK | self::OTHER_FULL | self::USER_FULL;

        $this->dateAccessed = new DateTime();
        $this->dateCreated  = clone $this->dateAccessed;
        $this->dateModified = clone $this->dateAccessed;
    }

    public function getContent()
    {
        return $this->file->getContent();
    }

    public function setContent($content)
    {
        $this->file->setContent($content);
    }

    public function getDateAccessed()
    {
        return $this->dateAccessed;
    }

    public function setDateAccessed(DateTime $dateTime)
    {
        $this->dateAccessed = $dateTime;
    }

    public function getDateCreated()
    {
        return $this->dateCreated;
    }

    public function getDateModified()
    {
        return $this->dateModified;
    }

    public function setDateModified(DateTime $dateTime)
    {
        $this->dateModified = $dateTime;
    }

    public function getMode()
    {
        return $this->mode;
    }

    public function getSize()
    {
        return $this->file->getSize();
    }

    public function getTarget()
    {
        return $this->file;
    }
}
