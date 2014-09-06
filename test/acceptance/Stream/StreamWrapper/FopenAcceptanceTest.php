<?php
namespace Vfs\Stream\StreamWrapper;

use Vfs\Test\AcceptanceTestCase;

class FopenAcceptanceTest extends AcceptanceTestCase
{
    protected $tree = [
        'foo' => [
            'bar' => 'baz'
        ]
    ];

    public function dataFopen()
    {
        return [
            ['a' ], ['r' ], ['w' ], ['c' ],
            ['ab'], ['rb'], ['wb'], ['cb'],
            ['a+'], ['r+'], ['w+'], ['c+'],
            ['at'], ['rt'], ['wt'], ['ct']
        ];
    }

    public function dataFopenError()
    {
        return [
            ['x'], ['xb'], ['x+'], ['xt']
        ];
    }

    public function dataFopenMissing()
    {
        return [
            ['a' ], ['w' ], ['c' ], ['x' ],
            ['ab'], ['wb'], ['cb'], ['xb'],
            ['a+'], ['w+'], ['c+'], ['x+'],
            ['at'], ['wt'], ['ct'], ['xt']
        ];
    }

    public function dataFopenMissingError()
    {
        return [
            ['r'], ['rb'], ['r+'], ['rt']
        ];
    }

    /**
     * @dataProvider dataFopen
     */
    public function testFopenFile($mode)
    {
        $this->assertTrue(is_resource(fopen("$this->scheme:///foo/bar", $mode)));
    }

    /**
     * @dataProvider dataFopenError
     */
    public function testFopenFileError($mode)
    {
        $this->setExpectedException('PHPUnit_Framework_Error_Warning');

        fopen("$this->scheme:///foo/bar", $mode);
    }

    /**
     * @dataProvider dataFopenMissing
     */
    public function testFopenMissingFile($mode)
    {
        $this->assertTrue(is_resource(fopen("$this->scheme:///foo/baz", $mode)));
    }

    /**
     * @dataProvider dataFopenMissingError
     */
    public function testFopenMissingFileError($mode)
    {
        $this->setExpectedException('PHPUnit_Framework_Error_Warning');

        fopen("$this->scheme:///foo/baz", $mode);
    }

    /**
     * @dataProvider dataFopen
     */
    public function testFopenDirectory($mode)
    {
        $this->assertTrue(is_resource(fopen("$this->scheme:///foo", $mode)));
    }

    /**
     * @dataProvider dataFopenError
     */
    public function testFopenDirectoryError($mode)
    {
        $this->setExpectedException('PHPUnit_Framework_Error_Warning');

        fopen("$this->scheme:///foo", $mode);
    }

    /**
     * @dataProvider dataFopenMissing
     */
    public function testFopenMissingDirectory($mode)
    {
        $this->assertTrue(is_resource(fopen("$this->scheme:///baz", $mode)));
    }

    /**
     * @dataProvider dataFopenMissingError
     */
    public function testFopenMissingDirectoryError($mode)
    {
        $this->setExpectedException('PHPUnit_Framework_Error_Warning');

        fopen("$this->scheme:///baz", $mode);
    }
}
