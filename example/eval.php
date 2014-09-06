<?php
/*
 * This file is part of VFS
 *
 * Copyright (c) 2014 Andrew Lawson <http://adlawson.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
use Vfs\FileSystem;

require __DIR__ . '/../vendor/autoload.php';

FileSystem::factory('eval://');

file_put_contents('eval://foo.php', <<<'EOF'
<?php

class Foo
{
    protected $name;

    public function __construct($name)
    {
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }
}
EOF
);

require 'eval://foo.php';

$foo = new Foo('bar');

var_dump($foo->getName());
