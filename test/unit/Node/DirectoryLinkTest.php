<?php
namespace Vfs\Node;

use Mockery;
use Vfs\Test\UnitTestCase;

class DirectoryLinkTest extends UnitTestCase
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
        $link = new DirectoryLink(new Directory());

        $this->assertInstanceOf('Vfs\Node\NodeContainerInterface', $link);
        $this->assertInstanceOf('Vfs\Node\LinkInterface', $link);
        $this->assertInstanceOf('Vfs\Node\NodeInterface', $link);
        $this->assertInstanceOf('Vfs\Node\StatInterface', $link);
    }

    public function testAdd()
    {
        $dir = new Directory();
        $link = new DirectoryLink($dir);
        $link->add('foo', $this->nodeA);

        $this->assertSame($this->nodeA, $dir->get('foo'));
    }

    public function testGet()
    {
        $link = new DirectoryLink(new Directory($this->nodes));

        $this->assertSame($this->nodeA, $link->get('foo'));
    }

    public function testGetThrowsMissingNode()
    {
        $link = new DirectoryLink(new Directory());
        $this->setExpectedException('Vfs\Exception\MissingNodeException');

        $link->get('foo');
    }

    public function testHasIsTrue()
    {
        $link = new DirectoryLink(new Directory($this->nodes));

        $this->assertTrue($link->has('foo'));
    }

    public function testHasIsFalse()
    {
        $link = new DirectoryLink(new Directory());

        $this->assertFalse($link->has('foo'));
    }

    public function testSet()
    {
        $dir = new Directory();
        $link = new DirectoryLink($dir);
        $link->set('foo', $this->nodeA);

        $this->assertSame($this->nodeA, $dir->get('foo'));
    }

    public function testGetDateAccessed()
    {
        $dir = new Directory();
        $link = new DirectoryLink($dir);

        $this->assertInstanceOf('DateTime', $link->getDateAccessed());
        $this->assertNotSame($link->getDateAccessed(), $dir->getDateAccessed());
    }

    public function testGetDateCreated()
    {
        $dir = new Directory();
        $link = new DirectoryLink($dir);

        $this->assertInstanceOf('DateTime', $link->getDateCreated());
        $this->assertNotSame($link->getDateCreated(), $dir->getDateCreated());
    }

    public function testGetDateModified()
    {
        $dir = new Directory();
        $link = new DirectoryLink($dir);

        $this->assertInstanceOf('DateTime', $link->getDateModified());
        $this->assertNotSame($link->getDateModified(), $dir->getDateModified());
    }

    public function testGetMode()
    {
        $link = new DirectoryLink(new Directory());

        $this->assertEquals(StatInterface::TYPE_LINK, $link->getMode() & StatInterface::TYPE_MASK);
    }

    public function testGetSize()
    {
        $link = new DirectoryLink(new Directory($this->nodes));

        $this->nodeA->shouldReceive('getSize')->once()->withNoArgs()->andReturn(1);
        $this->nodeB->shouldReceive('getSize')->once()->withNoArgs()->andReturn(2);
        $this->nodeC->shouldReceive('getSize')->once()->withNoArgs()->andReturn(3);

        $this->assertEquals(6, $link->getSize());
    }
}
