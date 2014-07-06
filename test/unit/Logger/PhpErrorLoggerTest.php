<?php
namespace Vfs\Logger;

use ArrayIterator;
use Mockery;
use Vfs\Test\UnitTestCase;

class PhpErrorLoggerTest extends UnitTestCase
{
    public function setUp()
    {
        $this->logger = new PhpErrorLogger();
    }

    public function dataLog()
    {
        return [
            ['emergency', 'PHPUnit_Framework_Error'],
            ['alert',     'PHPUnit_Framework_Error'],
            ['critical',  'PHPUnit_Framework_Error'],
            ['error',     'PHPUnit_Framework_Error'],
            ['warning',   'PHPUnit_Framework_Error_Warning'],
            ['notice',    'PHPUnit_Framework_Error_Notice'],
            ['info',      'PHPUnit_Framework_Error_Notice'],
            ['debug',     'PHPUnit_Framework_Error_Notice']
        ];
    }

    public function testInterface()
    {
        $this->assertInstanceOf('Psr\Log\LoggerInterface', $this->logger);
    }

    /**
     * @dataProvider dataLog
     */
    public function testLog($level, $expectation)
    {
        $this->setExpectedException($expectation);

        $this->logger->log($level, 'foo', []);
    }

    public function testLogReplacesPlaceholders()
    {
        try {
            $this->logger->log('debug', 'foo {bar} baz', ['bar' => 'BAR']);
        } catch (\PHPUnit_Framework_Error_Notice $e) {
            return $this->assertRegexp('/foo BAR baz/', $e->getMessage());
        }

        $this->fail('A PHP Notice should have been triggered');
    }

    public function testEmergency()
    {
        $this->setExpectedException('PHPUnit_Framework_Error');

        $this->logger->emergency('foo', []);
    }

    public function testAlert()
    {
        $this->setExpectedException('PHPUnit_Framework_Error');

        $this->logger->alert('foo', []);
    }

    public function testCritical()
    {
        $this->setExpectedException('PHPUnit_Framework_Error');

        $this->logger->critical('foo', []);
    }

    public function testError()
    {
        $this->setExpectedException('PHPUnit_Framework_Error');

        $this->logger->error('foo', []);
    }

    public function testWarning()
    {
        $this->setExpectedException('PHPUnit_Framework_Error_Warning');

        $this->logger->warning('foo', []);
    }

    public function testNotice()
    {
        $this->setExpectedException('PHPUnit_Framework_Error_Notice');

        $this->logger->notice('foo', []);
    }

    public function testInfo()
    {
        $this->setExpectedException('PHPUnit_Framework_Error_Notice');

        $this->logger->info('foo', []);
    }

    public function testDebug()
    {
        $this->setExpectedException('PHPUnit_Framework_Error_Notice');

        $this->logger->debug('foo', []);
    }
}
