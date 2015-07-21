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
use RuntimeException;
use Vfs\Stream\HandleInterface;

class UnopenedHandleException extends RuntimeException implements ExceptionInterface
{
    protected $handle;
    protected $url;

    /**
     * @param HandleInterface $handle
     * @param string          $url
     * @param integer         $code
     * @param Exception       $previous
     */
    public function __construct(HandleInterface $handle, $url, $code = 0, Exception $previous = null)
    {
        $this->handle = $handle;
        $this->url = $url;

        $message = sprintf('Handle at url "%s" hasn\'t been opened.', $url);

        parent::__construct($message, $code, $previous);
    }

    /**
     * @return HandleInterface
     */
    public function getHandle()
    {
        return $this->handle;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }
}
