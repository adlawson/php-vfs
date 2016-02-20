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
use Vfs\Exception\RegisteredSchemeException;
use Vfs\Exception\UnregisteredSchemeException;
use Vfs\Node\Factory\NodeFactoryInterface;
use Vfs\Node\Walker\NodeWalkerInterface;

class FileSystem implements FileSystemInterface
{
    protected $logger;
    protected $nodeFactory;
    protected $nodeWalker;
    protected $registry;
    protected $scheme;
    protected $wrapperClass;
    protected $root;

    /**
     * @param string               $scheme
     * @param string               $wrapperClass
     * @param NodeFactoryInterface $nodeFactory
     * @param NodeWalkerInterface  $nodeWalker
     * @param RegistryInterface    $registry
     * @param LoggerInterface      $logger
     */
    public function __construct(
        $scheme,
        $wrapperClass,
        NodeFactoryInterface $nodeFactory,
        NodeWalkerInterface $nodeWalker,
        RegistryInterface $registry,
        LoggerInterface $logger
    ) {
        $this->wrapperClass = $wrapperClass;
        $this->scheme = rtrim($scheme, ':/\\');
        $this->nodeWalker = $nodeWalker;
        $this->logger = $logger;
        $this->nodeFactory = $nodeFactory;
        $this->registry = $registry;

        $this->root = $nodeFactory->buildDirectory();
    }

    /**
     * @param  string     $scheme
     * @return FileSystem
     */
    public static function factory($scheme = self::SCHEME)
    {
        $builder = new FileSystemBuilder($scheme);

        return $builder->build();
    }

    public function get($path)
    {
        return $this->nodeWalker->findNode($this->root, $path);
    }

    public function getLogger()
    {
        return $this->logger;
    }

    public function getNodeFactory()
    {
        return $this->nodeFactory;
    }

    public function getNodeWalker()
    {
        return $this->nodeWalker;
    }

    public function getScheme()
    {
        return $this->scheme;
    }

    public function mount()
    {
        if ($this->registry->has($this->scheme) || in_array($this->scheme, stream_get_wrappers())) {
            throw new RegisteredSchemeException($this->scheme);
        }

        if (stream_wrapper_register($this->scheme, $this->wrapperClass)) {
            $this->registry->add($this->scheme, $this);

            return true;
        }

        return false;
    }

    public function unmount()
    {
        if (!$this->registry->has($this->scheme) && !in_array($this->scheme, stream_get_wrappers())) {
            throw new UnregisteredSchemeException($this->scheme);
        }

        if (stream_wrapper_unregister($this->scheme)) {
            $this->registry->remove($this->scheme, $this);

            return true;
        }

        return false;
    }
}
