<?php

namespace Sk\Mid\Language;


class EST extends Language
{
    public function __construct()
    {
        parent::__construct("EST");
    }

    public static function asType() : Language
    {
        return new EST();
    }
}

