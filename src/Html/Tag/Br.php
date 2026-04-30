<?php

declare(strict_types=1);

namespace Imadepurnamayasa\PhpInti\Html\Tag;

use Imadepurnamayasa\PhpInti\Html\Element;

class Br extends Element
{
    public function __construct()
    {
        parent::__construct('br');
    }

    public function getContent(): string
    {
        return '';
    }
}
