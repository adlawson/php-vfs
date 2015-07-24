<?php
namespace Vfs\Stream\StreamWrapper;

use Vfs\Test\AcceptanceTestCase;

class SymlinkAcceptanceTest extends AcceptanceTestCase
{
    protected $tree = [
        'foo' => [
            'bar' => 'baz'
        ]
    ];

    public function testIsLink()
    {
        $factory = $this->fs->getNodeFactory();

        $file = $this->fs->get('/foo/bar');
        $this->fs->get('/')->add('symlink', $factory->buildFileLink($file));

        $this->assertTrue(is_link("$this->scheme:///symlink"));
    }

    public function testDirectoryLink()
    {
        $this->markTestSkipped('`symlink()` isn\'t supported by PHP Stream Wrappers');

        symlink("$this->scheme:///foo/bar", "$this->scheme:///symlink");

        $this->assertTrue(is_link("$this->scheme:///symlink"));
    }

    public function testFileLink()
    {
        $this->markTestSkipped('`symlink()` isn\'t supported by PHP Stream Wrappers');

        symlink("$this->scheme:///foo", "$this->scheme:///symlink");

        $this->assertTrue(is_link("$this->scheme:///symlink"));
    }
}
