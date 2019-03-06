<?php
/**
 * Created by PhpStorm.
 * User: mikks
 * Date: 2/21/2019
 * Time: 4:34 PM
 */
namespace sk\mid\tests;

use PHPUnit\Framework\TestCase;

use sk\mid\CertificateParser;
use sk\mid\exception\MidInternalErrorException;
use sk\mid\tests\mock\TestData;

class CertificateParserTest extends TestCase
{
    /**
     * @test
     * @expectedException MidInternalErrorException
     */
    public function parseInvalidCertificate_shouldThrowException()
    {
        CertificateParser::parseX509Certificate("HACKERMAN");
    }
}
