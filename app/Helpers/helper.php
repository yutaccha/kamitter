<?php

namespace App\Helpers;

use Illuminate\Support\HtmlString;

class Helper
{

    public static function unEscapedLine(string $string): HtmlString
    {
        return new HtmlString(nl2br(e($string)));
    }

}