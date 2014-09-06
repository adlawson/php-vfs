<?php
namespace Vfs\Stream\StreamWrapper;

use Vfs\Test\AcceptanceTestCase;

/**
 * @todo testRequireMissingFile correctly triggers fatal error, not easy to test
 */
class RequireAcceptanceTest extends AcceptanceTestCase
{
    protected $tree = [
        'foo' => [
            'bar.php' => '<?php return "baz";'
        ]
    ];

    public function testIncludeFile()
    {
        $this->assertEquals('baz', include "$this->scheme:///foo/bar.php");
    }

    public function testRequireFile()
    {
        $this->assertEquals('baz', require "$this->scheme:///foo/bar.php");
    }

    public function testIncludeMissingFile()
    {
        $this->setExpectedException('PHPUnit_Framework_Error_Warning');

        include "$this->scheme:///bar.php";
    }
}
