<?php
/*-
 * #%L
 * Mobile ID sample PHP client
 * %%
 * Copyright (C) 2018 - 2019 SK ID Solutions AS
 * %%
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 * #L%
 */
namespace Sk\Mid;

use InvalidArgumentException;

class VerificationCodeCalculator
{
    const MINIMUM_HASH_LENGTH = 20;

    public static function calculateMobileIdVerificationCode(string $hash) : string
    {
        $binary = self::hexToBinary($hash);
        $sixLeftBits = substr($binary, 0, 6);
        $sevenRightBits = substr($binary, -7);
        if (self::isHashValid($hash)) {
            return str_pad(bindec($sixLeftBits.$sevenRightBits), 4, "0", STR_PAD_LEFT);
        }
        throw new InvalidArgumentException("Invalid hash.". $hash);
    }

    private static function isHashValid($hash) : bool
    {
        return !is_null($hash) && strlen($hash) >= self::MINIMUM_HASH_LENGTH;
    }

    private static function hexToBinary(?string $hash) : string {
        $bin = "";
        $array = str_split($hash);
        foreach ($array as $char) {
            $bin .= str_pad(decbin(hexdec($char)), 4, "0", STR_PAD_LEFT);;
        }
        return $bin;
    }

}
