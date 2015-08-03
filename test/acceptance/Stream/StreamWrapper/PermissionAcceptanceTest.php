<?php
namespace Vfs\Stream\StreamWrapper;

use Vfs\Test\AcceptanceTestCase;

class PermissionAcceptanceTest extends AcceptanceTestCase
{
    protected $tree = [
        'foo' => [
            'bar' => 'baz'
        ]
    ];

    public function testDirIsReadable()
    {
        $this->assertTrue(is_readable("$this->scheme:///foo"));
    }

    public function testDirIsWritable()
    {
        $this->assertTrue(is_writable("$this->scheme:///foo"));
    }

    public function testDirIsExecutable()
    {
        // Directory can't be executable
        $this->assertFalse(is_executable("$this->scheme:///foo"));
    }

    public function testFileIsReadable()
    {
        $this->assertTrue(is_readable("$this->scheme:///foo/bar"));
    }

    public function testFileIsWritable()
    {
        $this->assertTrue(is_writable("$this->scheme:///foo/bar"));
    }

    public function testFileIsExecutable()
    {
        $this->assertTrue(is_executable("$this->scheme:///foo/bar"));
    }
}
