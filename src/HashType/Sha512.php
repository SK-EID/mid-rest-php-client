<?php
/**
 * Created by IntelliJ IDEA.
 * User: mikks
 * Date: 3/6/2019
 * Time: 12:55 PM
 */

namespace Sk\Mid\HashType;


class Sha512 extends HashType
{

    public function __construct()
    {
        parent::__construct("SHA-512", "SHA512", 512, array(48, 81, 48, 13, 6, 9, 96, -122, 72, 1, 101, 3, 4, 2, 3, 5, 0, 4, 64));
    }

}
