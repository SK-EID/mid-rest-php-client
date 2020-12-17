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
namespace Sk\Mid\Util;

const MULTIPLIERS1 = array(1, 2, 3, 4, 5, 6, 7, 8, 9, 1);
const MULTIPLIERS2 = array(3, 4, 5, 6, 7, 8, 9, 1, 2, 3);

class MidNationalIdentificationCodeValidator
{

    public function isValid(?string $nationalIdentityNumber) : bool
    {
        if (!preg_match("/^\d{11}$/", $nationalIdentityNumber)) {
            return false;
        }
        else if ($nationalIdentityNumber == null || strlen($nationalIdentityNumber) != 11) {
            return false;
        }

        $controlDigit = intval(substr($nationalIdentityNumber, -1));

        if ($controlDigit != $this->calculateControlDigit($nationalIdentityNumber)) {
            return false;
        }

        return $this->isValidBirthDate($nationalIdentityNumber);
    }

    public function calculateControlDigit(?string $nationalIdentityNumber) : int
    {

        $mod = $this->multiplyDigits($nationalIdentityNumber, MULTIPLIERS1);
        if ($mod == 10) {
            $mod = $this->multiplyDigits($nationalIdentityNumber, MULTIPLIERS2);
        }
        return $mod%10;
    }

    private function multiplyDigits(string $code, array $multipliers) : int
    {
        $total = 0;

        for ($i = 0; $i < 10; $i++) {
            $total += intval($code[$i]) * $multipliers[$i];
        }
        return $total % 11;
    }

    public function isValidBirthDate(?string $nationalIdentityNumber) :bool
    {
        $year = intval(substr($nationalIdentityNumber, 1, 2));
        $month = intval(substr($nationalIdentityNumber, 3, 2));
        $dayOfMonth = intval(substr($nationalIdentityNumber, 5, 2));

        $firstNumber = intval(substr($nationalIdentityNumber, 0, 1));

        switch ($firstNumber) {
            case 5:
            case 6:
                $year += 2000;
                break;
            case 3:
            case 4:
                $year += 1900;
                break;
            default:
                $year += 1800;
        }
        return checkdate ( $month , $dayOfMonth , $year );
    }

}
