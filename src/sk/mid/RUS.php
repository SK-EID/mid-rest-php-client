<?php

namespace sk\mid;


class RUS extends Language
{
    public function __construct()
    {
        parent::__construct("RUS");
    }
    public static function asType() : Language
    {
        return new RUS();
    }
}
