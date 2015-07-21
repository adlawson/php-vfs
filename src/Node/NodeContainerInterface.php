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

use IteratorAggregate;
use Vfs\Exception\ExistingNodeException;
use Vfs\Exception\MissingNodeException;

interface NodeContainerInterface extends NodeInterface, IteratorAggregate
{
    /**
     * @param  string                $name
     * @param  NodeInterface         $node
     * @throws ExistingNodeException If a node exists in container with name
     */
    public function add($name, NodeInterface $node);

    /**
     * @param  string               $name
     * @throws MissingNodeException If a node doesn't exist in container with name
     */
    public function get($name);

    /**
     * @param  string  $name
     * @return boolean
     */
    public function has($name);

    /**
     * @param  string               $name
     * @throws MissingNodeException If a node doesn't exist in container with name
     */
    public function remove($name);

    /**
     * @param string        $name
     * @param NodeInterface $node
     */
    public function set($name, NodeInterface $node);
}
