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

use ArrayIterator;
use DateTime;
use Vfs\Exception\ExistingNodeException;
use Vfs\Exception\MissingNodeException;

class Directory implements NodeContainerInterface
{
    const DOT_SELF = '.';
    const DOT_UP   = '..';

    protected $dateAccessed;
    protected $dateCreated;
    protected $dateModified;
    protected $mode;
    protected $nodes = [];

    /**
     * @param NodeInterface[] $nodes
     */
    public function __construct(array $nodes = [])
    {
        $this->mode = self::TYPE_DIR | self::OTHER_FULL | self::USER_FULL;

        $this->dateAccessed = new DateTime();
        $this->dateCreated  = $this->dateAccessed;
        $this->dateModified = $this->dateAccessed;

        foreach ($nodes as $name => $node) {
            $this->add($name, $node);
        }

        $this->set(self::DOT_SELF, $this);
    }

    public function add($name, NodeInterface $node)
    {
        if ($this->has($name)) {
            throw new ExistingNodeException($name, $this);
        }

        $this->set($name, $node);
    }

    public function get($name)
    {
        if (!$this->has($name)) {
            throw new MissingNodeException($name, $this);
        }

        return $this->nodes[$name];
    }

    public function has($name)
    {
        return isset($this->nodes[$name]);
    }

    public function remove($name)
    {
        if (!$this->has($name)) {
            throw new MissingNodeException($name, $this);
        }

        unset($this->nodes[$name]);
    }

    public function set($name, NodeInterface $node)
    {
        $this->nodes[$name] = $node;

        if (self::DOT_UP !== $name && $node instanceof NodeContainerInterface) {
            $node->set(self::DOT_UP, $this);
        }
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
        return new ArrayIterator($this->nodes);
    }

    public function getMode()
    {
        return $this->mode;
    }

    public function getSize()
    {
        $size = 0;

        foreach ($this->nodes as $name => $node) {
            if (!in_array($name, [self::DOT_SELF, self::DOT_UP])) {
                $size += $node->getSize();
            }
        }

        return $size;
    }
}
