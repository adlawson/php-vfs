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
        $this->fs->shouldReceive('get')->times(2)->with('/');
        $this->fs->shouldReceive('getLogger')->once()->withNoArgs()->andReturn($logger);

        $handle->rename('foo://bar');
    }
}
