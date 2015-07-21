<?php
/*
 * This file is part of VFS
 *
 * Copyright (c) 2015 Andrew Lawson <http://adlawson.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Vfs\Exception;

use Exception;
use OutOfRangeException;

class UnregisteredSchemeException extends OutOfRangeException implements ExceptionInterface
{
    protected $scheme;

    /**
     * @param string    $scheme
     * @param integer   $code
     * @param Exception $previous
     */
    public function __construct($scheme, $code = 0, Exception $previous = null)
    {
        $this->scheme = $scheme;

        $message = sprintf('File system with scheme "%s" has not been registered.', $scheme);

        parent::__construct($message, $code, $previous);
    }

    /**
     * @return string
     */
    public function getScheme()
    {
        return $this->scheme;
    }
}
