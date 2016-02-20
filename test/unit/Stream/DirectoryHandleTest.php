<?php
namespace Vfs\Stream;

use Mockery;
use Vfs\Test\UnitTestCase;

class DirectoryHandleTest extends UnitTestCase
{
    public function setUp()
    {
        $this->fs = Mockery::mock('Vfs\FileSystem');
    }

    public function testInterface()
    {
        $handle = new DirectoryHandle($this->fs, '');

        $this->assertInstanceOf('Vfs\Stream\HandleInterface', $handle);
    }

    public function testRename()
    {
        $handle = new DirectoryHandle($this->fs, 'foo://foo/bar');
        $foo = Mockery::mock('Vfs\Node\NodeContainerInterface');
        $bar = Mockery::mock('Vfs\Node\NodeContainerInterface');

        $this->fs->shouldReceive('get')->once()->with('/foo/bar')->andReturn($bar);
        $this->fs->shouldReceive('get')->times(2)->with('/foo')->andReturn($foo);

        $foo->shouldReceive('remove')->once()->with('bar');
        $foo->shouldReceive('add')->once()->with('baz', $bar);

        $handle->rename('foo://foo/baz');
    }

    public function testRenameMissingSource()
    {
        $handle = new DirectoryHandle($this->fs, 'foo://foo');
        $foo = Mockery::mock('Vfs\Node\NodeContainerInterface');

        $logger = Mockery::mock('Psr\Log\LoggerInterface');
        $logger->shouldReceive('warning')->once()->with(Mockery::type('string'), [
            'origin' => 'foo://foo',
            'target' => 'foo://bar',
        ]);

        $this->fs->shouldReceive('get')->once()->with('/foo');
        $this->fs->shouldReceive('get')->times(2)->with(DIRECTORY_SEPARATOR);
        $this->fs->shouldReceive('getLogger')->once()->withNoArgs()->andReturn($logger);

        $handle->rename('foo://bar');
    }

    public function testCreateRecursively()
    {
        $handle = new DirectoryHandle($this->fs, 'foo://foo/bar');

        $rootContainer = Mockery::mock('Vfs\Node\NodeContainerInterface');
        $builtDir = Mockery::Mock('Vfs\Node\Directory');
        $builtContainer = Mockery::mock('Vfs\Node\NodeContainerInterface');
        $nodeFactory = Mockery::mock('Vfs\Node\NodeFactoryInterface');
        $nodeWalker = Mockery::mock('Vfs\Node\NodeWalkerInterface');

        $builtContainer->shouldReceive('add')->with(basename('/foo/bar'), $builtDir);
        $nodeFactory->shouldReceive('buildDirectory')->andReturn($builtDir);
        $nodeWalker->shouldReceive('walkPath')->once()->andReturn($builtContainer);

        $this->fs->shouldReceive('get')->once()->with('/foo/bar');
        $this->fs->shouldReceive('get')->once()->with('/foo');


        $this->fs->shouldReceive('get')->once()->with('/')->andReturn($rootContainer);
        $this->fs->shouldReceive('getNodeFactory')->times(2)->andReturn($nodeFactory);
        $this->fs->shouldReceive('getNodeWalker')->once()->andReturn($nodeWalker);


        $handle->create(0777, true);
    }
}
