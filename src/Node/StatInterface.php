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
 * Mode bits for node description and permissions
 *
 * @link http://www.gnu.org/software/libc/manual/html_node/Permission-Bits.html
 * @link http://man7.org/linux/man-pages/man2/stat.2.html
 */
interface StatInterface
{
    const S_IFMT   = 0170000;
    const S_IFSOCK = 0140000;
    const S_IFLNK  = 0120000;
    const S_IFREG  = 0100000;
    const S_IFBLK  = 0060000;
    const S_IFDIR  = 0040000;
    const S_IFCHR  = 0020000;
    const S_IFIFO  = 0010000;
    const S_ISUID  = 0004000;
    const S_ISGID  = 0002000;
    const S_ISVTX  = 0001000;
    const S_IRWXU  = 0000700;
    const S_IRUSR  = 0000400;
    const S_IWUSR  = 0000200;
    const S_IXUSR  = 0000100;
    const S_IRWXG  = 0000070;
    const S_IRGRP  = 0000040;
    const S_IWGRP  = 0000020;
    const S_IXGRP  = 0000010;
    const S_IRWXO  = 0000007;
    const S_IROTH  = 0000004;
    const S_IWOTH  = 0000002;
    const S_IXOTH  = 0000001;

    const TYPE_MASK   = self::S_IFMT;
    const TYPE_SOCKET = self::S_IFSOCK;
    const TYPE_LINK   = self::S_IFLNK;
    const TYPE_FILE   = self::S_IFREG;
    const TYPE_DIR    = self::S_IFDIR;
    const TYPE_PIPE   = self::S_IFIFO;
    const DEV_BLOCK   = self::S_IFBLK;
    const DEV_CHAR    = self::S_IFCHR;

    const SET_GROUP  = self::S_ISGID;
    const SET_USER   = self::S_ISUID;
    const SET_STICKY = self::S_ISVTX;

    const GROUP_EXEC  = self::S_IXGRP;
    const GROUP_FULL  = self::S_IRWXG;
    const GROUP_WRITE = self::S_IWGRP;
    const GROUP_READ  = self::S_IRGRP;
    const OTHER_EXEC  = self::S_IXOTH;
    const OTHER_FULL  = self::S_IRWXO;
    const OTHER_WRITE = self::S_IWOTH;
    const OTHER_READ  = self::S_IROTH;
    const USER_EXEC   = self::S_IXUSR;
    const USER_FULL   = self::S_IRWXU;
    const USER_READ   = self::S_IRUSR;
    const USER_WRITE  = self::S_IWUSR;

    /**
     * @return integer
     */
    public function getMode();
}
