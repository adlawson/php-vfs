<?php
namespace Vfs\Stream\StreamWrapper;

use Vfs\Test\AcceptanceTestCase;

class FileGetContentsAcceptanceTest extends AcceptanceTestCase
{
    protected $tree = [
        'foo' => [
            'bar' => 'baz'
        ]
    ];

    public function testGetDirectory()
    {
        $this->assertEquals('', file_get_contents("$this->scheme:///foo"));
    }

    public function testGetFile()
    {
        $this->assertEquals($this->tree['foo']['bar'], file_get_contents("$this->scheme:///foo/bar"));
    }

    public function testPutFile()
    {
        file_put_contents("$this->scheme:///foo/bar", 'bar');

        $this->assertEquals('bar', $this->fs->get('/foo/bar')->getContent());
    }

    public function testPutExistingFile()
    {
        file_put_contents("$this->scheme:///foo/bar", '_updated');

        $this->assertEquals('_updated', $this->fs->get('/foo/bar')->getContent());
    }

    public function testPutAppendExistingFile()
    {
        file_put_contents("$this->scheme:///foo/bar", '_updated', FILE_APPEND);

        $this->assertEquals($this->tree['foo']['bar'] . '_updated', $this->fs->get('/foo/bar')->getContent());
    }
}
