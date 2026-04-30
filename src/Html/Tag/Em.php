<?php

declare(strict_types=1);

namespace Imadepurnamayasa\PhpInti\Html\Tag;

use Imadepurnamayasa\PhpInti\Html\Element;

class Em extends Element
{
    private $content;

    public function __construct(string $content = '')
    {
        parent::__construct('em');
        $this->content = $content;
    }

    public function getContent(): string
    {
        return $this->content;
    }
}
