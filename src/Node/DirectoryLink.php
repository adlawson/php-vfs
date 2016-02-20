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

class DirectoryLink implements NodeContainerInterface, LinkInterface
{
    protected $dateAccessed;
    protected $dateCreated;
    protected $dateModified;
    protected $file;
    protected $mode;

    /**
     * @param NodeContainerInterface $directory
     */
    public function __construct(NodeContainerInterface $directory)
    {
        $this->directory = $directory;
        $this->mode = self::TYPE_LINK | self::OTHER_FULL | self::USER_FULL;

        $this->dateAccessed = new DateTime();
        $this->dateCreated  = clone $this->dateAccessed;
        $this->dateModified = clone $this->dateAccessed;
    }

    public function add($name, NodeInterface $node)
    {
        $this->directory->add($name, $node);
    }

    public function get($name)
    {
        return $this->directory->get($name);
    }

    public function has($name)
    {
        return $this->directory->has($name);
    }

    public function remove($name)
    {
        $this->directory->remove($name);
    }

    public function set($name, NodeInterface $node)
    {
        $this->directory->set($name, $node);
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

    public function getIterator()
    {
        return $this->directory->getIterator();
    }

    public function getMode()
    {
        return $this->mode;
    }

    public function getSize()
    {
        return $this->directory->getSize();
    }

    public function getTarget()
    {
        return $this->directory;
    }
}
