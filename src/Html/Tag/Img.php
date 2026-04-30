<?php

declare(strict_types=1);

namespace Imadepurnamayasa\PhpInti\Html\Tag;

use Imadepurnamayasa\PhpInti\Html\Element;

class Img extends Element
{
    public function __construct(string $src = '')
    {
        parent::__construct('img');
        if ($src !== '') {
            $this->addAttribute('src', $src);
        }
    }

    public function getContent(): string
    {
        return '';
    }
}
