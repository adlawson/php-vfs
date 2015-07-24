<?php
namespace Vfs\Node\Factory;

use Mockery;
use Vfs\Node\Directory;
use Vfs\Node\File;
use Vfs\Test\UnitTestCase;

class NodeFactoryTest extends UnitTestCase
{
    public function setUp()
    {
        $this->factory = new NodeFactory();
    }

    public function testInterface()
    {
        $this->assertInstanceOf('Vfs\Node\Factory\NodeFactoryInterface', $this->factory);
    }

    public function testBuildFile()
    {
        $file = $this->factory->buildFile('foo');

        $this->assertInstanceOf('Vfs\Node\FileInterface', $file);
        $this->assertEquals('foo', $file->getContent());
    }

    public function testBuildFileLink()
    {
        $file = new File();
        $link = $this->factory->buildFileLink($file);

        $this->assertInstanceOf('Vfs\Node\FileInterface', $link);
        $this->assertInstanceOf('Vfs\Node\LinkInterface', $link);
    }

    public function testBuildDirectory()
    {
        $node = Mockery::mock('Vfs\Node\NodeInterface');
        $dir = $this->factory->buildDirectory(['foo' => $node]);

        $this->assertInstanceof('Vfs\Node\NodeContainerInterface', $dir);
        $this->assertSame($node, $dir->get('foo'));
    }

    public function testBuildDirectoryLink()
    {
        $dir = new Directory();
        $link = $this->factory->buildDirectoryLink($dir);

        $this->assertInstanceOf('Vfs\Node\NodeContainerInterface', $link);
        $this->assertInstanceOf('Vfs\Node\LinkInterface', $link);
    }

    public function testBuildTree()
    {
        $root = $this->factory->buildTree([
            'foo' => [
                'bar' => [
                    'baz' => 'foobarbaz'
                ]
            ],
            'bar' => [
                'baz' => 'barbaz'
            ]
        ]);

        $this->assertInstanceof('Vfs\Node\NodeContainerInterface', $root);
        $this->assertInstanceof('Vfs\Node\NodeContainerInterface', $root->get('foo'));
        $this->assertInstanceof('Vfs\Node\NodeContainerInterface', $root->get('bar'));
        $this->assertInstanceof('Vfs\Node\NodeContainerInterface', $root->get('foo')->get('bar'));
        $this->assertInstanceof('Vfs\Node\FileInterface', $root->get('foo')->get('bar')->get('baz'));
        $this->assertInstanceof('Vfs\Node\FileInterface', $root->get('bar')->get('baz'));
        $this->assertEquals('foobarbaz', $root->get('foo')->get('bar')->get('baz')->getContent());
        $this->assertEquals('barbaz', $root->get('bar')->get('baz')->getContent());
    }
}
