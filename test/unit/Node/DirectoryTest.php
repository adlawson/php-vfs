<?php
namespace Vfs\Node;

use Mockery;
use Vfs\Test\UnitTestCase;

class DirectoryTest extends UnitTestCase
{
    public function setUp()
    {
        $this->nodeA = $a = Mockery::mock('Vfs\Node\NodeInterface');
        $this->nodeB = $b = Mockery::mock('Vfs\Node\NodeInterface');
        $this->nodeC = $c = Mockery::mock('Vfs\Node\NodeInterface');
        $this->nodes = ['foo' => $a, 'bar' => $b, 'baz' => $c];
    }

    public function testInstance()
    {
        $dir = new Directory();

        $this->assertInstanceOf('Vfs\Node\NodeContainerInterface', $dir);
        $this->assertInstanceOf('Vfs\Node\NodeInterface', $dir);
        $this->assertInstanceOf('Vfs\Node\StatInterface', $dir);
    }

    public function testConstructSetsDotReference()
    {
        $dir = new Directory();

        $this->assertSame($dir, $dir->get('.'));
    }

    public function testAdd()
    {
        $dir = new Directory();
        $dir->add('foo', $this->nodeA);

        $this->assertSame($this->nodeA, $dir->get('foo'));
    }

    public function testAddContainerSetsDotReference()
    {
        $dir = new Directory();
        $node = Mockery::mock('Vfs\Node\NodeContainerInterface');

        $node->shouldReceive('set')->once()->with('..', $dir);

        $dir->add('foo', $node);
    }

    public function testGet()
    {
        $dir = new Directory($this->nodes);

        $this->assertSame($this->nodeA, $dir->get('foo'));
    }

    public function testGetThrowsMissingNode()
    {
        $dir = new Directory();
        $this->setExpectedException('Vfs\Exception\MissingNodeException');

        $dir->get('foo');
    }

    public function testHasIsTrue()
    {
        $dir = new Directory($this->nodes);

        $this->assertTrue($dir->has('foo'));
    }

    public function testHasIsFalse()
    {
        $dir = new Directory();

        $this->assertFalse($dir->has('foo'));
    }

    public function testSet()
    {
        $dir = new Directory();
        $dir->set('foo', $this->nodeA);

        $this->assertSame($this->nodeA, $dir->get('foo'));
    }

    public function testSetContainerSetsDotReference()
    {
        $dir = new Directory();
        $node = Mockery::mock('Vfs\Node\NodeContainerInterface');

        $node->shouldReceive('set')->once()->with('..', $dir);

        $dir->set('foo', $node);
    }

    public function testGetDateAccessed()
    {
        $dir = new Directory();

        $this->assertInstanceOf('DateTime', $dir->getDateAccessed());
    }

    public function testGetDateCreated()
    {
        $dir = new Directory();

        $this->assertInstanceOf('DateTime', $dir->getDateCreated());
    }

    public function testGetDateModified()
    {
        $dir = new Directory();

        $this->assertInstanceOf('DateTime', $dir->getDateModified());
    }

    public function testGetMode()
    {
        $dir = new Directory();

        $this->assertEquals(StatInterface::TYPE_DIR, $dir->getMode() & StatInterface::TYPE_MASK);
    }

    public function testGetSize()
    {
        $dir = new Directory($this->nodes);

        $this->nodeA->shouldReceive('getSize')->once()->withNoArgs()->andReturn(1);
        $this->nodeB->shouldReceive('getSize')->once()->withNoArgs()->andReturn(2);
        $this->nodeC->shouldReceive('getSize')->once()->withNoArgs()->andReturn(3);

        $this->assertEquals(6, $dir->getSize());
    }
}
