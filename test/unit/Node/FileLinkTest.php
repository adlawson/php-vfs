<?php
namespace Vfs\Node;

use Vfs\Test\UnitTestCase;

class FileLinkTest extends UnitTestCase
{
    public function testInstance()
    {
        $link = new FileLink(new File());

        $this->assertInstanceOf('Vfs\Node\FileInterface', $link);
        $this->assertInstanceOf('Vfs\Node\LinkInterface', $link);
        $this->assertInstanceOf('Vfs\Node\NodeInterface', $link);
        $this->assertInstanceOf('Vfs\Node\StatInterface', $link);
    }

    public function testGetContent()
    {
        $link = new FileLink(new File('foo'));

        $this->assertEquals('foo', $link->getContent());
    }

    public function testSetContent()
    {
        $file = new File('');
        $link = new FileLink($file);
        $link->setContent('foo');

        $this->assertEquals('foo', $file->getContent());
    }

    public function testGetDateAccessed()
    {
        $file = new File();
        $link = new FileLink($file);

        $this->assertInstanceOf('DateTime', $link->getDateAccessed());
        $this->assertNotSame($link->getDateAccessed(), $file->getDateAccessed());
    }

    public function testGetDateCreated()
    {
        $file = new File();
        $link = new FileLink($file);

        $this->assertInstanceOf('DateTime', $link->getDateCreated());
        $this->assertNotSame($link->getDateCreated(), $file->getDateCreated());
    }

    public function testGetDateModified()
    {
        $file = new File();
        $link = new FileLink($file);

        $this->assertInstanceOf('DateTime', $link->getDateModified());
        $this->assertNotSame($link->getDateModified(), $file->getDateModified());
    }

    public function testGetMode()
    {
        $link = new FileLink(new File());

        $this->assertEquals(StatInterface::TYPE_LINK, $link->getMode() & StatInterface::TYPE_MASK);
    }

    public function testGetSize()
    {
        $link = new FileLink(new File('foo'));

        $this->assertEquals(3, $link->getSize());
    }
}
