<?php
namespace Vfs\Stream\StreamWrapper;

use Vfs\Test\AcceptanceTestCase;

class StatAcceptanceTest extends AcceptanceTestCase
{
    protected $tree = [
        'foo' => [
            'bar' => 'baz'
        ]
    ];

    public function testIsDir()
    {
        $this->assertTrue(is_dir("$this->scheme:///foo"));
    }

    public function testIsFile()
    {
        $this->assertTrue(is_file("$this->scheme:///foo/bar"));
    }

    public function testTouch()
    {
        $this->assertTrue(touch("$this->scheme://foo/bar"));
    }

    public function testTouchNew()
    {
        $this->assertTrue(touch("$this->scheme://foo/baz"));
    }
}
