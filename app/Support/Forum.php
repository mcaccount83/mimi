<?php

namespace App\Support;

class Forum extends \TeamTeaTime\Forum\Support\Frontend\Forum
{
    public static function render(string $content): string
    {
        return $content;
    }
}
