<?php
/**
 * Created by IntelliJ IDEA.
 * User: mikks
 * Date: 3/6/2019
 * Time: 12:43 PM
 */

namespace sk\mid;


class LIT extends Language
{
    public function __construct()
    {
        parent::__construct("LIT");
    }
    public static function asType() : Language
    {
        return new LIT();
    }
}

