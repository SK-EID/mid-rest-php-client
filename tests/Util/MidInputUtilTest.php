<?php
namespace Sk\Mid\Tests\Util;



use PHPUnit\Framework\TestCase;
use Sk\Mid\Exception\InvalidPhoneNumberException;
use Sk\Mid\Tests\Mock\TestData;
use Sk\Mid\Util\MidInputUtil;

class MidInputUtilTest extends TestCase
{

    /**
     * @test
     * @throws \Exception
     */
    public function validateUserInput_validPhone_shouldRemoveSpaces()
    {
        $phoneNumber = MidInputUtil::getValidatedPhoneNumber(" +372 00000 766 ");

        $this->assertThat($phoneNumber, $this->equalTo("+37200000766"));
    }

    /**
     * @test
     */
    public function validateUserInput_invalidPhone_shouldThrowException()
    {
        $this->expectException(InvalidPhoneNumberException::class);

        MidInputUtil::getValidatedPhoneNumber('123');
    }


    /**
     * @test
     */
    public function validateUserInput_withValidData()
    {
        MidInputUtil::getValidatedUserInput(TestData::VALID_PHONE, TestData::VALID_NAT_IDENTITY);

        $this->addToAssertionCount(1);
    }

}