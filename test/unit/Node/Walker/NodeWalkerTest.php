<?php
namespace Vfs\Node\Walker;

use Mockery;
use Vfs\Test\UnitTestCase;

class NodeWalkerTest extends UnitTestCase
{
    public function setUp()
    {
        $this->walker = new NodeWalker();
    }

    public function dataFindNodeReturnsRoot()
    {
        return [[''], ['/'], ['.'], ['./']];
    }

    public function testInterface()
    {
        $this->assertInstanceOf('Vfs\Node\Walker\NodeWalkerInterface', $this->walker);
    }

    public function testFindNode()
    {
        $bar = Mockery::mock('Vfs\Node\NodeInterface');

        $foo = Mockery::mock('Vfs\Node\NodeContainerInterface');
        $foo->shouldReceive('has')->once()->with('bar')->andReturn(true);
        $foo->shouldReceive('get')->once()->with('bar')->andReturn($bar);

        $root = Mockery::mock('Vfs\Node\NodeContainerInterface');
        $root->shouldReceive('has')->once()->with('foo')->andReturn(true);
        $root->shouldReceive('get')->once()->with('foo')->andReturn($foo);

        $this->assertSame($bar, $this->walker->findNode($root, '/foo/bar'));
    }

    /**
     * @dataProvider dataFindNodeReturnsRoot
     */
    public function testFindNodeReturnsRoot($path)
    {
        $root = Mockery::mock('Vfs\Node\NodeContainerInterface');

        if ($path && '.' == $path[0]) {
            $root->shouldReceive('has')->once()->with('.')->andReturn(true);
            $root->shouldReceive('get')->once()->with('.')->andReturn($root);
        }

        $this->assertSame($root, $this->walker->findNode($root, $path));
    }

    public function testFindNodeWithDotPaths()
    {
        $baz = Mockery::mock('Vfs\Node\NodeInterface');

        $bar = Mockery::mock('Vfs\Node\NodeContainerInterface');
        $bar->shouldReceive('has')->once()->with('baz')->andReturn(true);
        $bar->shouldReceive('get')->once()->with('baz')->andReturn($baz);

        $foo = Mockery::mock('Vfs\Node\NodeContainerInterface');
        $foo->shouldReceive('has')->times(2)->with('bar')->andReturn(true);
        $foo->shouldReceive('get')->times(2)->with('bar')->andReturn($bar);

        $root = Mockery::mock('Vfs\Node\NodeContainerInterface');
        $root->shouldReceive('has')->times(3)->with('foo')->andReturn(true);
        $root->shouldReceive('get')->times(3)->with('foo')->andReturn($foo);

        $bar->shouldReceive('has')->once()->with('.')->andReturn(true);
        $bar->shouldReceive('get')->once()->with('.')->andReturn($bar);
        $bar->shouldReceive('has')->once()->with('..')->andReturn(true);
        $bar->shouldReceive('get')->once()->with('..')->andReturn($foo);
        $foo->shouldReceive('has')->once()->with('.')->andReturn(true);
        $foo->shouldReceive('get')->once()->with('.')->andReturn($foo);
        $foo->shouldReceive('has')->times(2)->with('..')->andReturn(true);
        $foo->shouldReceive('get')->times(2)->with('..')->andReturn($root);

        $this->assertSame($baz, $this->walker->findNode($root, '/foo/../foo/./bar//../../foo/bar/./baz'));
    }

    public function testFindInvalidPath()
    {
        $foo = Mockery::mock('Vfs\Node\NodeContainerInterface');
        $foo->shouldReceive('has')->once()->with('bar')->andReturn(false);

        $root = Mockery::mock('Vfs\Node\NodeContainerInterface');
        $root->shouldReceive('has')->once()->with('foo')->andReturn(true);
        $root->shouldReceive('get')->once()->with('foo')->andReturn($foo);

        $this->assertNull($this->walker->findNode($root, '/foo/bar'));
    }

    public function testWalkPath()
    {
        $bar = Mockery::mock('Vfs\Node\NodeInterface');
        $foo = Mockery::mock('Vfs\Node\NodeContainerInterface');
        $root = Mockery::mock('Vfs\Node\NodeContainerInterface');

        $fn = function ($node, $name) use ($root, $foo, $bar) {
            if ('foo' === $name && $node === $root) {
                return $foo;
            } elseif ('bar' === $name && $node === $foo) {
                return $bar;
            }

            $this->fail('None of the walk conditions were met');
        };

        $this->assertSame($bar, $this->walker->walkPath($root, '/foo/bar', $fn));
    }
}
