<?php
/*
 * This file is part of VFS
 *
 * Copyright (c) 2015 Andrew Lawson <http://adlawson.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Vfs\Logger;

use Psr\Log\AbstractLogger;
use Psr\Log\LogLevel;

class PhpErrorLogger extends AbstractLogger
{
    public function log($level, $message, array $context = [])
    {
        switch ($level) {
            case LogLevel::EMERGENCY:
            case LogLevel::ALERT:
            case LogLevel::CRITICAL:
            case LogLevel::ERROR:
                trigger_error($this->format($message, $context), E_USER_ERROR);
                break;
            case LogLevel::WARNING:
                trigger_error($this->format($message, $context), E_USER_WARNING);
                break;
            case LogLevel::NOTICE:
            case LogLevel::INFO:
            case LogLevel::DEBUG:
                trigger_error($this->format($message, $context), E_USER_NOTICE);
                break;
        }
    }

    /**
     * @param  string $message
     * @param  array  $context
     * @return string
     */
    protected function format($message, array $context)
    {
        foreach ($context as $key => $value) {
            $message = str_replace(sprintf('{%s}', $key), $value, $message);
        }

        return $message . $this->formatTrace(debug_backtrace(false));
    }

    /**
     * @param  array  $backtrace
     * @return string
     */
    protected function formatTrace(array $backtrace)
    {
        $index = min((count($backtrace) + 1), 6);
        $origin = $backtrace[$index];

        $file = isset($origin['file']) ? $origin['file'] : 'unknown';
        $line = isset($origin['line']) ? $origin['line'] : 0;

        return sprintf(' in %s on line %d; triggered', $file, $line);
    }
}
