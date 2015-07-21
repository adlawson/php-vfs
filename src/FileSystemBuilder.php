<?php
/*
 * This file is part of VFS
 *
 * Copyright (c) 2015 Andrew Lawson <http://adlawson.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Vfs;

use Psr\Log\LoggerInterface;
use Vfs\Logger\PhpErrorLogger;
use Vfs\Node\Factory\NodeFactory;
use Vfs\Node\Factory\NodeFactoryInterface;
use Vfs\Node\Walker\NodeWalker;
use Vfs\Node\Walker\NodeWalkerInterface;

class FileSystemBuilder
{
    protected $factory;
    protected $registry;
    protected $logger;
    protected $scheme;
    protected $walker;
    protected $wrapperClass;
    protected $tree = [];

    /**
     * @param string $scheme
     */
    public function __construct($scheme = FileSystemInterface::SCHEME)
    {
        $this->setScheme($scheme);
    }

    /**
     * @return FileSystemInterface
     */
    public function build()
    {
        $fs = new FileSystem(
            $this->getScheme(),
            $this->getStreamWrapper() ?: $this->buildDefaultStreamWrapper(),
            $this->getNodeFactory() ?: $this->buildDefaultNodeFactory(),
            $this->getNodeWalker() ?: $this->buildDefaultNodeWalker(),
            $this->getRegistry() ?: $this->buildDefaultRegistry(),
            $this->getLogger() ?: $this->buildDefaultLogger()
        );

        $root = $fs->get('/');
        $factory = $fs->getNodeFactory();
        foreach ($factory->buildTree($this->getTree()) as $name => $node) {
            $root->set($name, $node);
        }

        return $fs;
    }

    /**
     * @return LoggerInterface
     */
    public function getLogger()
    {
        return $this->logger;
    }

    /**
     * @param  LoggerInterface   $logger
     * @return FileSystemBuilder
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;

        return $this;
    }

    /**
     * @return NodeFactoryInterface
     */
    public function getNodeFactory()
    {
        return $this->factory;
    }

    /**
     * @param  NodeFactoryInterface $factory
     * @return FileSystemBuilder
     */
    public function setNodeFactory(NodeFactoryInterface $factory)
    {
        $this->factory = $factory;

        return $this;
    }

    /**
     * @return NodeWalkerInterface
     */
    public function getNodeWalker()
    {
        return $this->walker;
    }

    /**
     * @param  NodeWalkerInterface $walker
     * @return FileSystemBuilder
     */
    public function setNodeWalker(NodeWalkerInterface $walker)
    {
        $this->walker = $walker;

        return $this;
    }

    /**
     * @return RegistryInterface
     */
    public function getRegistry()
    {
        return $this->registry;
    }

    /**
     * @param  RegistryInterface $registry
     * @return FileSystemBuilder
     */
    public function setRegistry(RegistryInterface $registry)
    {
        $this->registry = $registry;

        return $this;
    }

    /**
     * @return string
     */
    public function getScheme()
    {
        return $this->scheme;
    }

    /**
     * @param  string            $scheme
     * @return FileSystemBuilder
     */
    public function setScheme($scheme)
    {
        $this->scheme = $scheme;

        return $this;
    }

    /**
     * @return string
     */
    public function getStreamWrapper()
    {
        return $this->wrapperClass;
    }

    /**
     * @param  string            $class
     * @return FileSystemBuilder
     */
    public function setStreamWrapper($class)
    {
        $this->wrapperClass = $class;

        return $this;
    }

    /**
     * @return array
     */
    public function getTree()
    {
        return $this->tree;
    }

    /**
     * @param  array             $tree
     * @return FileSystemBuilder
     */
    public function setTree($tree)
    {
        $this->tree = $tree;

        return $this;
    }

    /**
     * @return LoggerInterface
     */
    protected function buildDefaultLogger()
    {
        return new PhpErrorLogger();
    }

    /**
     * @return NodeFactoryInterface
     */
    protected function buildDefaultNodeFactory()
    {
        return new NodeFactory();
    }

    /**
     * @return NodeWalkerInterface
     */
    protected function buildDefaultNodeWalker()
    {
        return new NodeWalker();
    }

    /**
     * @return RegistryInterface
     */
    protected function buildDefaultRegistry()
    {
        return FileSystemRegistry::getInstance();
    }

    /**
     * @return string
     */
    protected function buildDefaultStreamWrapper()
    {
        return 'Vfs\\Stream\\StreamWrapper';
    }
}
