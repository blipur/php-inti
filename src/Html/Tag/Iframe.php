<?php

declare(strict_types=1);

namespace Imadepurnamayasa\PhpInti\Html\Tag;

use Imadepurnamayasa\PhpInti\Html\Element;

class Iframe extends Element
{
    private $content;

    public function __construct(string $src = '', string $content = '')
    {
        parent::__construct('iframe');
        $this->content = $content;
        if ($src !== '') {
            $this->addAttribute('src', $src);
        }
    }

    public function getContent(): string
    {
        return $this->content;
    }
}
