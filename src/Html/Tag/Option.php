<?php

declare(strict_types=1);

namespace Imadepurnamayasa\PhpInti\Html\Tag;

use Imadepurnamayasa\PhpInti\Html\Element;

class Option extends Element
{
    private $content;

    public function __construct(string $content = '', string $value = '')
    {
        parent::__construct('option');
        $this->content = $content;
        if ($value !== '') {
            $this->addAttribute('value', $value);
        }
    }

    public function getContent(): string
    {
        return $this->content;
    }
}
