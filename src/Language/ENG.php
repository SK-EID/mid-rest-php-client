<?php

namespace Sk\Mid\Language;

class ENG extends Language
{
    public function __construct()
    {
        parent::__construct("ENG");
    }
    public static function asType() : Language
    {
        return new ENG();
    }
}

