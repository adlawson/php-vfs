<?php
/*
 * This file is part of VFS
 *
 * Copyright (c) 2015 Andrew Lawson <http://adlawson.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Vfs\Exception;

use Exception;
use OutOfRangeException;
use Vfs\Node\NodeContainerInterface;

class MissingNodeException extends OutOfRangeException implements ExceptionInterface
{
    protected $container;
    protected $name;

    /**
     * @param string                 $name
     * @param NodeContainerInterface $container
     * @param integer                $code
     * @param Exception              $previous
     */
    public function __construct($name, NodeContainerInterface $container, $code = 0, Exception $previous = null)
    {
        $this->container = $container;
        $this->name = $name;

        $message = sprintf('Node with name "%s" doesn\'t exist in container.', $name);

        parent::__construct($message, $code, $previous);
    }

    /**
     * @return NodeContainerInterface
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}
