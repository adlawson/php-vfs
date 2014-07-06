<?php
/*
 * This file is part of VFS
 *
 * Copyright (c) 2014 Andrew Lawson <http://adlawson.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Vfs\Node;

use DateTime;

interface NodeInterface extends StatInterface
{
    /**
     * @return DateTime
     */
    public function getDateAccessed();

    /**
     * @return DateTime
     */
    public function getDateCreated();

    /**
     * @return DateTime
     */
    public function getDateModified();

    /**
     * @return integer
     */
    public function getSize();
}
