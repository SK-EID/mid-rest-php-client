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
class VerificationCodeCalculator
{
    const MINIMUM_HASH_LENGTH = 20;

    public function __construct()
    {
    }

    public static function calculateMobileIdVerificationCode($hash)
    {
        $binary = self::hexToBinary($hash);
        $sixLeftBits = substr($binary, 0, 6);
        $sevenRightBits = substr($binary, -7);
        $result = self::validateHash($hash) ? str_pad(bindec($sixLeftBits.$sevenRightBits), 4, "0", STR_PAD_LEFT) : 0;

        return $result;
    }

    private static function validateHash($hash)
    {
        return !is_null($hash) && strlen($hash) >= self::MINIMUM_HASH_LENGTH;
    }

    private static function hexToBinary($str) {
        $bin = "";
        $array = str_split($str);
        foreach ($array as $char) {
            $bin .= str_pad(decbin(hexdec($char)), 4, "0", STR_PAD_LEFT);;
        }
        return $bin;
    }

}