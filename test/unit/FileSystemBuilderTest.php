<?php
namespace Vfs;

use ArrayIterator;
use Mockery;
use Vfs\Test\UnitTestCase;

class FileSystemBuilderTest extends UnitTestCase
{
    public function setUp()
    {
        $this->builder = new FileSystemBuilder();
    }

    public function testGetLogger()
    {
        $this->assertNull($this->builder->getLogger());
    }

    public function testSetLogger()
    {
        $factory = Mockery::mock('Psr\Log\LoggerInterface');
        $this->builder->setLogger($factory);

        $this->assertSame($factory, $this->builder->getLogger());
    }

    public function testGetNodeFactory()
    {
        $this->assertNull($this->builder->getNodeFactory());
    }

    public function testSetNodeFactory()
    {
        $factory = Mockery::mock('Vfs\Node\Factory\NodeFactoryInterface');
        $this->builder->setNodeFactory($factory);

        $this->assertSame($factory, $this->builder->getNodeFactory());
    }

    public function testGetNodeWalker()
    {
        $this->assertNull($this->builder->getNodeWalker());
    }

    public function testSetNodeWalker()
    {
        $walker = Mockery::mock('Vfs\Node\Walker\NodeWalkerInterface');
        $this->builder->setNodeWalker($walker);

        $this->assertSame($walker, $this->builder->getNodeWalker());
    }

    public function testGetRegistry()
    {
        $this->assertNull($this->builder->getRegistry());
    }

    public function testSetRegistry()
    {
        $registry = Mockery::mock('Vfs\RegistryInterface');
        $this->builder->setRegistry($registry);

        $this->assertSame($registry, $this->builder->getRegistry());
    }

    public function testGetScheme()
    {
        $this->assertEquals('vfs', $this->builder->getScheme());
    }

    public function testSetScheme()
    {
        $scheme = 'foo';
        $this->builder->setScheme($scheme);

        $this->assertEquals($scheme, $this->builder->getScheme());
    }

    public function testGetStreamWrapper()
    {
        $this->assertNull($this->builder->getStreamWrapper());
    }

    public function testSetStreamWrapper()
    {
        $wrapperClass = 'Vfs\Stream\StreamWrapper';
        $this->builder->setStreamWrapper($wrapperClass);

        $this->assertEquals($wrapperClass, $this->builder->getStreamWrapper());
    }

    public function testBuild()
    {
        $scheme = 'foo';
        $wrapperClass = 'Vfs\Stream\StreamWrapper';
        $root = Mockery::mock('Vfs\Node\NodeContainerInterface');
        $tree = Mockery::mock('Vfs\Node\NodeContainerInterface', ['getIterator' => new ArrayIterator([])]);
        $factory = Mockery::mock('Vfs\Node\Factory\NodeFactoryInterface');
        $walker = Mockery::mock('Vfs\Node\Walker\NodeWalkerInterface');
        $logger = Mockery::mock('Psr\Log\LoggerInterface');
        $registry = Mockery::mock('Vfs\RegistryInterface');

        $this->builder->setScheme($scheme);
        $this->builder->setLogger($logger);
        $this->builder->setNodeFactory($factory);
        $this->builder->setNodeWalker($walker);
        $this->builder->setRegistry($registry);
        $this->builder->setStreamWrapper($wrapperClass);

        $factory->shouldReceive('buildDirectory')->once()->withNoArgs()->andReturn($root);
        $factory->shouldReceive('buildTree')->once()->with([])->andReturn($tree);
        $walker->shouldReceive('findNode')->once()->with($root, '/')->andReturn($root);

        $fs = $this->builder->build();

        $this->assertInstanceOf('Vfs\FileSystemInterface', $fs);
        $this->assertSame($factory, $fs->getNodeFactory());
        $this->assertSame($walker, $fs->getNodeWalker());
    }

    public function testBuildWithDefaults()
    {
        $fs = $this->builder->build();

        $this->assertInstanceOf('Vfs\FileSystemInterface', $fs);
        $this->assertEquals('vfs', $fs->getScheme());
        $this->assertInstanceOf('Psr\Log\LoggerInterface', $fs->getLogger());
        $this->assertInstanceOf('Vfs\Node\Factory\NodeFactoryInterface', $fs->getNodeFactory());
        $this->assertInstanceOf('Vfs\Node\Walker\NodeWalkerInterface', $fs->getNodeWalker());
    }
}
