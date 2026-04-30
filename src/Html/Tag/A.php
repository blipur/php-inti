<?php

declare(strict_types=1);

namespace Imadepurnamayasa\PhpInti\Html\Tag;

use Imadepurnamayasa\PhpInti\Html\Element;

class A extends Element
{
    private $content;

    public function __construct(string $content = '', string $href = '')
    {
        parent::__construct('a');
        $this->content = $content;
        if ($href !== '') {
            $this->addAttribute('href', $href);
        }
    }

    public function getContent(): string
    {
        return $this->content;
    }
}
