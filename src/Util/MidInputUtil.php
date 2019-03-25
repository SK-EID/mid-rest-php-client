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

use Sk\Mid\Exception\InvalidNationalIdentityNumberException;
use Sk\Mid\Exception\InvalidPhoneNumberException;

class MidInputUtil
{

    public static function isPhoneNumberValid(?string $phoneNumber) : bool
    {
        return preg_match("/^\+\d{8,30}$/", $phoneNumber);
    }

    public static function isNationalIdentityNumberValid(?string $nationalIdentityNumber) : bool
    {
        // TODO validate checksum
        return preg_match("/^\d{11}$/", $nationalIdentityNumber);
    }


    public static function validatePhoneNumber(?string $phoneNumber) : void
    {
        if (!self::isPhoneNumberValid($phoneNumber)) {
            throw new InvalidPhoneNumberException($phoneNumber);
        }
    }

    public static function validateNationalIdentityNumber(?string $nationalIdentityNumber) : void
    {
        if (!self::isNationalIdentityNumberValid($nationalIdentityNumber)) {
            throw new InvalidNationalIdentityNumberException($nationalIdentityNumber);
        }
    }
    public static function validateUserInput(?string $phoneNumber, ?string $nationalIdentityNumber) : void
    {
        self::validatePhoneNumber($phoneNumber);
        self::validateNationalIdentityNumber($nationalIdentityNumber);
    }

    public static function getValidatedPhoneNumber(?string $phoneNumberInput) :string
    {
        $cleanedPhoneNumber = preg_replace("/\s+/", "", $phoneNumberInput);

        self::validatePhoneNumber($cleanedPhoneNumber);

        return $cleanedPhoneNumber;
    }

    public static function getValidatedNationalIdentityNumber(?string $nationalIdentityNumber) :string
    {
        $cleanedNationalIdentityNumber = preg_replace("/\s+/", "", $nationalIdentityNumber);

        self::validateNationalIdentityNumber($cleanedNationalIdentityNumber);

        return $cleanedNationalIdentityNumber;
    }



    public static function getValidatedUserInput(?string $phoneNumber, ?string $nationalIdentityNumber) : array
    {
        $cleanedPhoneNumber = self::getValidatedPhoneNumber($phoneNumber);
        $cleanedNationalIdentityNumber = self::getValidatedNationalIdentityNumber($nationalIdentityNumber);
        return array('phoneNumber' => $cleanedPhoneNumber, 'nationalIdentityNumber' => $cleanedNationalIdentityNumber);
    }

    public static function isUserInputValid(?string $phoneNumber, ?string $nationalIdentityNumber) : bool
    {
        return self::isPhoneNumberValid($phoneNumber) && self::isNationalIdentityNumberValid($nationalIdentityNumber);
    }



}