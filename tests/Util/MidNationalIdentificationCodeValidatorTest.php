<?php
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
