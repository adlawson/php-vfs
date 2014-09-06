<?php
namespace Vfs\Stream\StreamWrapper;

use Vfs\Test\AcceptanceTestCase;

class FreadAcceptanceTest extends AcceptanceTestCase
{
    protected $tree = [
        'foo' => [
            'bar' => 'baz'
        ]
    ];

    public function dataFreadFile()
    {
        $b = 'bar';
        return [
            ['a',  $b, 1, '' ], ['a',  $b, 2, ''  ], ['a',  $b, 3, ''   ], ['a',  $b, 4, ''   ],
            ['r',  $b, 1, 'b'], ['r',  $b, 2, 'ba'], ['r',  $b, 3, 'baz'], ['r',  $b, 4, 'baz'],
            ['w',  $b, 1, '' ], ['w',  $b, 2, ''  ], ['w',  $b, 3, ''   ], ['w',  $b, 4, ''   ],
            ['c',  $b, 1, '' ], ['c',  $b, 2, ''  ], ['c',  $b, 3, ''   ], ['c',  $b, 4, ''   ],
            ['ab', $b, 1, '' ], ['ab', $b, 2, ''  ], ['ab', $b, 3, ''   ], ['ab', $b, 4, ''   ],
            ['rb', $b, 1, 'b'], ['rb', $b, 2, 'ba'], ['rb', $b, 3, 'baz'], ['rb', $b, 4, 'baz'],
            ['wb', $b, 1, '' ], ['wb', $b, 2, ''  ], ['wb', $b, 3, ''   ], ['wb', $b, 4, ''   ],
            ['cb', $b, 1, '' ], ['cb', $b, 2, ''  ], ['cb', $b, 3, ''   ], ['cb', $b, 4, ''   ],
            ['a+', $b, 1, '' ], ['a+', $b, 2, ''  ], ['a+', $b, 3, ''   ], ['a+', $b, 4, ''   ],
            ['r+', $b, 1, 'b'], ['r+', $b, 2, 'ba'], ['r+', $b, 3, 'baz'], ['r+', $b, 4, 'baz'],
            ['w+', $b, 1, '' ], ['w+', $b, 2, ''  ], ['w+', $b, 3, ''   ], ['w+', $b, 4, ''   ],
            ['c+', $b, 1, 'b'], ['c+', $b, 2, 'ba'], ['c+', $b, 3, 'baz'], ['c+', $b, 4, 'baz'],
            ['at', $b, 1, '' ], ['at', $b, 2, ''  ], ['at', $b, 3, ''   ], ['at', $b, 4, ''   ],
            ['rt', $b, 1, 'b'], ['rt', $b, 2, 'ba'], ['rt', $b, 3, 'baz'], ['rt', $b, 4, 'baz'],
            ['wt', $b, 1, '' ], ['wt', $b, 2, ''  ], ['wt', $b, 3, ''   ], ['wt', $b, 4, ''   ],
            ['ct', $b, 1, '' ], ['ct', $b, 2, ''  ], ['ct', $b, 3, ''   ], ['ct', $b, 4, ''   ]
        ];
    }

    public function dataFreadMissingFile()
    {
        $b = 'bar';
        return [
            ['a',  ''],
            ['x',  ''],
            ['w',  ''],
            ['c',  ''],
            ['ab', ''],
            ['xb', ''],
            ['wb', ''],
            ['cb', ''],
            ['a+', ''],
            ['x+', ''],
            ['w+', ''],
            ['c+', ''],
            ['at', ''],
            ['xt', ''],
            ['wt', ''],
            ['ct', '']
        ];
    }

    /**
     * @dataProvider dataFreadFile
     */
    public function testFreadFile($mode, $content, $size, $expectation)
    {
        $resource = fopen("$this->scheme:///foo/bar", $mode);

        $this->assertEquals($expectation, fread($resource, $size));

        fclose($resource);
    }

    /**
     * @dataProvider dataFreadMissingFile
     */
    public function testFreadMissingFile($mode)
    {
        $resource = fopen("$this->scheme:///bar", $mode);

        $this->assertEquals('', fread($resource, 10));

        fclose($resource);
    }
}
