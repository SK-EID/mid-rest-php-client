<?php
/*-
 * #%L
 * Mobile ID sample PHP client
 * %%
 * Copyright (C) 2018 - 2021 SK ID Solutions AS
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
namespace Sk\Mid\Tests\Util;

use PHPUnit\Framework\TestCase;
use Sk\Mid\Exception\MidInvalidPhoneNumberException;
use Sk\Mid\MobileIdAuthentication;
use Sk\Mid\MobileIdAuthenticationResult;
use Sk\Mid\MobileIdClient;
use Sk\Mid\Tests\Mock\TestData;
use Sk\Mid\Util\MidInputUtil;
use Sk\Mid\Util\MidNationalIdentificationCodeValidator;

class MidNationalIdentificationCodeValidatorTest extends TestCase
{
    /**
     * @test
     */
    public function validateNationalIdentityNumbers_invalid()
    {
        $validator = new MidNationalIdentificationCodeValidator();
        self::assertFalse($validator->isValid("60013019909"));
        self::assertFalse($validator->isValid("376050302993"));
        self::assertFalse($validator->isValid("3760503029y"));
    }

    /**
     * @test
     */
    public function validateNationalIdentityNumbers_valid()
    {
        $validator = new MidNationalIdentificationCodeValidator();
        self::assertTrue($validator->isValid("37605030299"));
    }

    /**
     * @test
     */
    public function calculateControlDigit()
    {
        $validator = new MidNationalIdentificationCodeValidator();
        self::assertEquals(9, $validator->calculateControlDigit("3760503029"));
    }

    /**
     * @test
     */
    public function isValidBirthDate_year1800()
    {
        $validator = new MidNationalIdentificationCodeValidator();
        self::assertTrue($validator->isValidBirthDate("17605030299"));
    }

    /**
     * @test
     */
    public function isValidBirthDate_year1900()
    {
        $validator = new MidNationalIdentificationCodeValidator();
        self::assertTrue($validator->isValidBirthDate("37605030299"));
    }

    /**
     * @test
     */
    public function isValidBirthDate_year2000()
    {
        $validator = new MidNationalIdentificationCodeValidator();
        self::assertTrue($validator->isValidBirthDate("60605030299"));
    }

}
