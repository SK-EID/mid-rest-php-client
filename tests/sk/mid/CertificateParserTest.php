<?php

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../../src/sk/mid/CertificateParser.php';
require_once __DIR__ . '/../../../src/sk/mid/exception/MidInternalErrorException.php';
require_once __DIR__ . '/mock/TestData.php';

/**
 * Created by PhpStorm.
 * User: mikks
 * Date: 2/21/2019
 * Time: 4:34 PM
 */
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
