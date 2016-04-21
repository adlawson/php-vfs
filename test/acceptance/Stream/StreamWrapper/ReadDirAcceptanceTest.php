<?php
namespace Vfs\Stream\StreamWrapper;

use Vfs\Test\AcceptanceTestCase;

class ReadDirAcceptanceTest extends AcceptanceTestCase
{
    protected $tree = [
        'foo' => [
            'bar' => 'baz'
        ]
    ];

    public function testReadDirectory()
    {
        $dHandler = opendir("$this->scheme:///foo");
        $expects = ['bar' => true, '.' => true, '..' => true];
        while(($file = readdir($dHandler)) !== false) {
            $this->assertArrayHasKey($file, $expects);
        }
    }
}

