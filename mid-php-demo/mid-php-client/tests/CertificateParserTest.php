<?php

use PHPUnit\Framework\TestCase;

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
     * @throws Exception
     */
    public function parseCertificate()
    {
        $X509Certificate = CertificateParser::parseX509Certificate(TestData::AUTH_CERTIFICATE_EE);
        $this->assertEquals(TestData::AUTH_CERTIFICATE_EE, base64_encode($X509Certificate->getEncoded()));
    }

    /**
     * @test
     * @expectedException TechnicalErrorException
     */
    public function parseInvalidCertificate_shouldThrowException()
    {
        CertificateParser::parseX509Certificate("HACKERMAN");
    }
}