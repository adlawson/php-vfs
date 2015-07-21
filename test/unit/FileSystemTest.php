<?php
namespace Vfs;

use Mockery;
use Vfs\Test\UnitTestCase;

class FileSystemTest extends UnitTestCase
{
    public function setUp()
    {
        $this->scheme = 'foo';
        $this->wrapperClass = 'Vfs\Stream\StreamWrapper';
        $this->logger = Mockery::mock('Psr\Log\LoggerInterface');
        $this->walker = Mockery::mock('Vfs\Node\Walker\NodeWalkerInterface');
        $this->factory = Mockery::mock('Vfs\Node\Factory\NodeFactoryInterface');
        $this->registry = Mockery::mock('Vfs\RegistryInterface');
        $this->root = Mockery::mock('Vfs\Node\NodeContainerInterface');

        $this->factory->shouldReceive('buildDirectory')->once()->withNoArgs()->andReturn($this->root);

        $this->fs = new FileSystem($this->scheme, $this->wrapperClass, $this->factory, $this->walker, $this->registry, $this->logger);
    }

    public function testInterface()
    {
        $this->assertInstanceOf('Vfs\FileSystemInterface', $this->fs);
    }

    public function testGet()
    {
        $path = '/foo/bar/baz.txt';
        $node = Mockery::mock('Vfs\Node\NodeInterface');

        $this->walker->shouldReceive('findNode')->once()->with($this->root, $path)->andReturn($node);

        $this->assertSame($node, $this->fs->get($path));
    }

    public function testGetLogger()
    {
        $this->assertSame($this->logger, $this->fs->getLogger());
    }

    public function testGetNodeFactory()
    {
        $this->assertSame($this->factory, $this->fs->getNodeFactory());
    }

    public function testGetNodeWalker()
    {
        $this->assertSame($this->walker, $this->fs->getNodeWalker());
    }

    public function testMountThrowsRegisteredException()
    {
        $this->registry->shouldReceive('has')->once()->with($this->scheme)->andReturn(true);

        $this->setExpectedException('Vfs\Exception\RegisteredSchemeException');

        $this->fs->mount();
    }

    public function testUnmountThrowsUnregisteredException()
    {
        $this->registry->shouldReceive('has')->once()->with($this->scheme)->andReturn(false);

        $this->setExpectedException('Vfs\Exception\UnregisteredSchemeException');

        $this->fs->unmount();
    }

    public function testGetScheme()
    {
        $this->assertSame($this->scheme, $this->fs->getScheme());
    }

    public function testGetSchemeWithoutColonSlashSlash()
    {
        $factory = Mockery::mock('Vfs\Node\Factory\NodeFactoryInterface');
        $factory->shouldReceive('buildDirectory')->once()->withNoArgs()->andReturn($this->root);

        $fs = new FileSystem("foo://", $this->wrapperClass, $factory, $this->walker, $this->registry, $this->logger);

        $this->assertSame($this->scheme, $fs->getScheme());
    }
}
