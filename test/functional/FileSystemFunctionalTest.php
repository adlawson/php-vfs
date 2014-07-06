<?php
namespace Vfs;

use RuntimeException;
use Vfs\Test\FunctionalTestCase;

class FileSystemFunctionalTest extends FunctionalTestCase
{
    public function testMount()
    {
        $this->assertFalse($this->isMounted($this->scheme));

        $this->fs->mount();

        $this->assertTrue($this->isMounted($this->scheme));
    }

    public function testUnmount()
    {
        $this->assertFalse($this->isMounted($this->scheme));

        $this->fs->mount();

        $this->assertTrue($this->isMounted($this->scheme));

        $this->fs->unmount();

        $this->assertFalse($this->isMounted($this->scheme));
    }
}
