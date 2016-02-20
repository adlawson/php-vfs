<?php
namespace Vfs\Stream;

use Mockery;
use Vfs\Test\UnitTestCase;

class FileHandleTest extends UnitTestCase
{
    public function setUp()
    {
        $this->fs = Mockery::mock('Vfs\FileSystem');
    }

    public function dataCanRead()
    {
        return [
            ['',  false], [null, false],
            ['a', false], ['ab', false], ['a+', true], ['at', false],
            ['r', true ], ['rb', true ], ['r+', true], ['rt', true ],
            ['w', false], ['wb', false], ['w+', true], ['wt', false],
            ['c', false], ['cb', false], ['c+', true], ['ct', false],
            ['x', false], ['xb', false], ['x+', true], ['xt', false]
        ];
    }

    public function dataOpenMissingFileIsCreated()
    {
        return [
            ['a'], ['ab'], ['a+'], ['at'],
            ['w'], ['wb'], ['w+'], ['wt'],
            ['c'], ['cb'], ['c+'], ['ct'],
            ['x'], ['xb'], ['x+'], ['xt']
        ];
    }

    public function dataRead()
    {
        return [
            [0, null, 'bar'], [1, null, 'ar'], [2, null, 'r'], [3, null, ''],
            [0, 0, ''], [1, 0, ''], [2, 0, ''], [3, 0, ''],
            [0, 1, 'b'], [1, 1, 'a'], [2, 1, 'r'], [3, 1, ''],
            [0, 2, 'ba'], [1, 2, 'ar'], [2, 2, 'r'], [3, 2, ''],
            [0, 3, 'bar'], [1, 3, 'ar'], [2, 3, 'r'], [3, 3, ''],
        ];
    }

    public function dataTouch()
    {
        return [
            ['a'], ['ab'], ['a+'], ['at'],
            ['w'], ['wb'], ['w+'], ['wt'],
            ['c'], ['cb'], ['c+'], ['ct']
        ];
    }

    public function testInterface()
    {
        $handle = new FileHandle($this->fs, '');

        $this->assertInstanceOf('Vfs\Stream\HandleInterface', $handle);
    }

    public function testRename()
    {
        $handle = new FileHandle($this->fs, 'foo://foo/bar');
        $foo = Mockery::mock('Vfs\Node\NodeContainerInterface');
        $bar = Mockery::mock('Vfs\Node\NodeInterface');

        $this->fs->shouldReceive('get')->once()->with('/foo/bar')->andReturn($bar);
        $this->fs->shouldReceive('get')->times(2)->with('/foo')->andReturn($foo);

        $foo->shouldReceive('remove')->once()->with('bar');
        $foo->shouldReceive('add')->once()->with('baz', $bar);

        $handle->rename('foo://foo/baz');
    }

    public function testRenameMissingSource()
    {
        $handle = new FileHandle($this->fs, 'foo://foo');
        $foo = Mockery::mock('Vfs\Node\NodeInterface');

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

    /**
     * @dataProvider dataCanRead
     */
    public function testCanRead($mode, $expected)
    {
        $handle = new FileHandle($this->fs, '', $mode);

        $this->assertEquals($expected, $handle->canRead());
    }

    public function testCreate()
    {
        $handle = new FileHandle($this->fs, 'foo://foo/bar/baz', 'w+');

        $file = Mockery::mock('Vfs\Node\NodeInterface');
        $dir = Mockery::mock('Vfs\Node\NodeContainerInterface');
        $dir->shouldReceive('set')->once()->with('baz', $file);
        $factory = Mockery::mock('Vfs\Node\Factory\NodeFactoryInterface');
        $factory->shouldReceive('buildFile')->once()->withNoArgs()->andReturn($file);

        $this->fs->shouldReceive('get')->once()->with('/foo/bar/baz');
        $this->fs->shouldReceive('get')->once()->with('/foo/bar')->andReturn($dir);
        $this->fs->shouldReceive('getNodeFactory')->once()->withNoArgs()->andReturn($factory);

        $this->assertSame($file, $handle->create(0777));
    }

    public function testCreateWithoutWriteMode()
    {
        $handle = new FileHandle($this->fs, 'foo://foo/bar/baz');

        $this->fs->shouldReceive('get')->once()->with('/foo/bar/baz');

        $this->assertNull($handle->create(0777));
    }

    public function testCreateWithMissingDir()
    {
        $handle = new FileHandle($this->fs, 'foo://foo/bar/baz', 'w+');

        $this->fs->shouldReceive('get')->once()->with('/foo/bar/baz');
        $this->fs->shouldReceive('get')->once()->with('/foo/bar');

        $this->assertNull($handle->create(0777));
    }

    public function testDestroy()
    {
        $handle = new FileHandle($this->fs, 'foo://foo/bar/baz');

        $file = Mockery::mock('Vfs\Node\NodeInterface');
        $dir = Mockery::mock('Vfs\Node\NodeContainerInterface');
        $dir->shouldReceive('remove')->once()->with('baz');

        $this->fs->shouldReceive('get')->once()->with('/foo/bar/baz')->andReturn($file);
        $this->fs->shouldReceive('get')->once()->with('/foo/bar')->andReturn($dir);

        $this->assertTrue($handle->destroy());
    }

    public function testDestroyMissingFile()
    {
        $handle = new FileHandle($this->fs, 'foo://foo/bar/baz');

        $file = Mockery::mock('Vfs\Node\NodeInterface');
        $logger = Mockery::mock('Pser\Log\LoggerInterface');
        $logger->shouldReceive('warning')->once()->with(Mockery::type('string'), [
            'url' => 'foo://foo/bar/baz'
        ]);

        $this->fs->shouldReceive('get')->once()->with('/foo/bar/baz');
        $this->fs->shouldReceive('getLogger')->once()->withNoArgs()->andReturn($logger);

        $this->assertFalse($handle->destroy());
    }

    public function testOpen()
    {
        $handle = new FileHandle($this->fs, 'foo://foo/bar/baz');
        $file = Mockery::mock('Vfs\Node\NodeInterface');

        $this->fs->shouldReceive('get')->once()->with('/foo/bar/baz')->andReturn($file);

        $this->assertSame($file, $handle->open());
    }

    public function testOpenMissingFileIsNotCreated()
    {
        $handle = new FileHandle($this->fs, 'foo://foo/bar/baz');

        $this->fs->shouldReceive('get')->once()->with('/foo/bar/baz');

        $this->assertNull($handle->open());
    }

    /**
     * @dataProvider dataOpenMissingFileIsCreated
     */
    public function testOpenMissingFileIsCreated($mode)
    {
        $handle = new FileHandle($this->fs, 'foo://foo/bar/baz', $mode);
        $factory = Mockery::mock('Vfs\Node\Factory\NodeFactoryInterface');
        $dir = Mockery::mock('Vfs\Node\NodeContainerInterface');
        $file = Mockery::mock('Vfs\Node\FileInterface');

        $this->fs->shouldReceive('get')->once()->with('/foo/bar/baz');
        $this->fs->shouldReceive('get')->once()->with('/foo/bar')->andReturn($dir);
        $this->fs->shouldReceive('getNodeFactory')->once()->withNoArgs()->andReturn($factory);

        $dir->shouldReceive('set')->once()->with('baz', $file);
        $factory->shouldReceive('buildFile')->once()->withNoArgs()->andReturn($file);

        if (in_array($mode, ['w', 'wb', 'w+', 'wt'])) {
            $file->shouldReceive('setContent')->once()->with('');
        }

        $this->assertSame($file, $handle->open());
    }

    /**
     * @dataProvider dataRead
     */
    public function testRead($offset, $length, $expected)
    {
        $handle = new FileHandle($this->fs, 'foo://bar');

        $file = Mockery::mock('Vfs\Node\FileInterface');
        $file->shouldReceive('getContent')->once()->withNoArgs()->andReturn('bar');

        $this->fs->shouldReceive('get')->once()->with('/bar')->andReturn($file);

        $handle->open();
        $this->assertEquals($expected, $handle->read($offset, $length));
    }

    public function testReadNonFile()
    {
        $handle = new FileHandle($this->fs, 'foo://bar');

        $file = Mockery::mock('Vfs\Node\NodeInterface');

        $this->fs->shouldReceive('get')->once()->with('/bar')->andReturn($file);

        $handle->open();
        $this->assertEquals('', $handle->read(0, PHP_INT_MAX));
    }

    public function testReadThrowsWithoutOpening()
    {
        $handle = new FileHandle($this->fs, 'foo://bar');

        $this->setExpectedException('Vfs\Exception\UnopenedHandleException');

        $handle->read();
    }

    public function testWrite()
    {
        $handle = new FileHandle($this->fs, 'foo://bar');

        $file = Mockery::mock('Vfs\Node\FileInterface');
        $file->shouldReceive('setContent')->once()->with('foo');

        $this->fs->shouldReceive('get')->once()->with('/bar')->andReturn($file);

        $handle->open();
        $handle->write('foo');
    }

    public function testWriteThrowsWithoutOpening()
    {
        $handle = new FileHandle($this->fs, 'foo://bar');

        $this->setExpectedException('Vfs\Exception\UnopenedHandleException');

        $handle->write('');
    }

    /**
     * @dataProvider dataTouch
     */
    public function testTouch($mode)
    {
        $handle = new FileHandle($this->fs, 'foo://bar', $mode);
        $atime = Mockery::mock('DateTime');
        $mtime = Mockery::mock('DateTime');

        $file = Mockery::mock('Vfs\Node\FileInterface');
        $file->shouldReceive('setDateAccessed')->once()->with($atime);
        $file->shouldReceive('setDateModified')->once()->with($mtime);

        $factory = Mockery::mock('Vfs\Node\Factory\NodeFactoryInterface');
        $factory->shouldReceive('buildFile')->once()->withNoArgs()->andReturn($file);

        $root = Mockery::mock('Vfs\Node\NodeContainerInterface');
        $root->shouldReceive('set')->once()->with('bar', $file);

        $this->fs->shouldReceive('get')->once()->with(DIRECTORY_SEPARATOR)->andReturn($root);
        $this->fs->shouldReceive('get')->once()->with('/bar');
        $this->fs->shouldReceive('getNodeFactory')->once()->withNoArgs()->andReturn($factory);

        $node = $handle->touch($mtime, $atime);

        $this->assertSame($file, $node);
    }

    /**
     * @dataProvider dataTouch
     */
    public function testTouchExistingFile($mode)
    {
        $handle = new FileHandle($this->fs, 'foo://bar', $mode);
        $atime = Mockery::mock('DateTime');
        $mtime = Mockery::mock('DateTime');

        $file = Mockery::mock('Vfs\Node\FileInterface');
        $file->shouldReceive('setDateAccessed')->once()->with($atime);
        $file->shouldReceive('setDateModified')->once()->with($mtime);

        $this->fs->shouldReceive('get')->once()->with('/bar')->andReturn($file);

        $node = $handle->touch($mtime, $atime);

        $this->assertSame($file, $node);
    }
}
