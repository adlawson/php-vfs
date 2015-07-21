<?php
/*
 * This file is part of VFS
 *
 * Copyright (c) 2015 Andrew Lawson <http://adlawson.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Vfs\Node;

/**
 * @link http://www.gnu.org/software/libc/manual/html_node/Permission-Bits.html
 */
interface StatInterface
{
    const S_IFMT  = 0170000;
    const S_IFLNK = 0120000;
    const S_IFREG = 0100000;
    const S_IFDIR = 0040000;
    const S_IRUSR = 0000400;
    const S_IWUSR = 0000200;
    const S_IXUSR = 0000100;
    const S_IRGRP = 0000040;
    const S_IWGRP = 0000020;
    const S_IXGRP = 0000010;
    const S_IROTH = 0000004;
    const S_IWOTH = 0000002;
    const S_IXOTH = 0000001;

    const GROUP_EXEC  = self::S_IXGRP;
    const GROUP_WRITE = self::S_IWGRP;
    const GROUP_READ  = self::S_IRGRP;
    const OTHER_EXEC  = self::S_IXOTH;
    const OTHER_WRITE = self::S_IWOTH;
    const OTHER_READ  = self::S_IROTH;
    const USER_EXEC   = self::S_IXUSR;
    const USER_READ   = self::S_IRUSR;
    const USER_WRITE  = self::S_IWUSR;
    const TYPE_BLOCK  = self::S_IFMT;
    const TYPE_FILE   = self::S_IFREG;
    const TYPE_DIR    = self::S_IFDIR;
    const TYPE_LINK   = self::S_IFLNK;

    /**
     * @return integer
     */
    public function getMode();
}
