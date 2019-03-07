<?php
/**
 * Created by PhpStorm.
 * User: mikks
 * Date: 2/21/2019
 * Time: 4:34 PM
 */
namespace Sk\Mid\Tests;

use PHPUnit\Framework\TestCase;

use Sk\Mid\CertificateParser;
use Sk\Mid\Exception\MidInternalErrorException;
use Sk\Mid\Tests\Mock\TestData;

class CertificateParserTest extends TestCase
{
    /**
     * @test
     */
    public function parseInvalidCertificate_shouldThrowException()
    {
        $this->expectException(MidInternalErrorException::class);

        CertificateParser::parseX509Certificate("HACKERMAN");
    }
}
