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
namespace Sk\Mid\Tests;
use Sk\Mid\Exception\MidSslException;
use Sk\Mid\Exception\MidNotMidClientException;
use Sk\Mid\Rest\MobileIdConnector;
use Sk\Mid\Tests\Mock\MobileIdConnectorSpy;
use Sk\Mid\Tests\Mock\TestData;

use Sk\Mid\MobileIdClient;
use Sk\Mid\Rest\MobileIdRestConnector;
use Sk\Mid\Rest\Dao\Request\CertificateRequest;
use Sk\Mid\Rest\Dao\Response\CertificateResponse;
use Sk\Mid\Exception\MissingOrInvalidParameterException;
use Sk\Mid\Exception\MidInternalErrorException;

use PHPUnit\Framework\TestCase;
use TypeError;

class CertificateRequestBuilderTest extends TestCase
{
    private $connector;

    private function getConnector() : MobileIdConnectorSpy
    {
        return $this->connector;
    }

    protected function setUp() : void
    {
        $this->connector = new MobileIdConnectorSpy();
        $this->connector->setCertificateChoiceResponseToRespond(self::createDummyCertificateChoiceResponse());
    }

    /**
     * @test
     */
    public function getCertificate_withoutRelyingPartyUUID_shouldThrowException()
    {
        $this->expectException(MissingOrInvalidParameterException::class);
        $this->expectExceptionMessage("Missing or invalid parameter: Relying Party UUID parameter must be set in client or request");

        $request = CertificateRequest::newBuilder()
            ->withPhoneNumber(TestData::VALID_PHONE)
            ->withNationalIdentityNumber(TestData::VALID_NAT_IDENTITY)
            ->build();

        $connector = MobileIdRestConnector::newBuilder()
            ->withEndpointUrl(TestData::DEMO_HOST_URL)
            ->withRelyingPartyName(TestData::DEMO_RELYING_PARTY_NAME)
            ->withSslPinnedPublicKeys( TestData::DEMO_HOST_PUBLIC_KEY_HASH)
            ->build();

        $connector->pullCertificate($request);
    }

    /**
     * @test
     */
    public function getCertificate_withIncorrectSslPinnedPublicKey_shouldThrowSslException()
    {
        $this->expectException(MidSslException::class);
        $this->expectExceptionMessage("SSL public key is untrusted for host: https://tsp.demo.sk.ee/mid-api/certificate. See README.md for setting API host certificate as trusted.");


        $request = CertificateRequest::newBuilder()
            ->withPhoneNumber(TestData::VALID_PHONE)
            ->withRelyingPartyUUID(TestData::DEMO_RELYING_PARTY_UUID)
            ->withRelyingPartyName(TestData::DEMO_RELYING_PARTY_NAME)
            ->withNationalIdentityNumber(TestData::VALID_NAT_IDENTITY)
            ->build();

        $connector = MobileIdRestConnector::newBuilder()
            ->withEndpointUrl(TestData::DEMO_HOST_URL)
            ->withRelyingPartyName(TestData::DEMO_RELYING_PARTY_NAME)
            ->withSslPinnedPublicKeys(TestData::SOME_OTHER_HOST_PUBLIC_KEY_HASH)
            ->build();

        $connector->pullCertificate($request);
    }

    /**
     * @test
     */
    public function getCertificate_withoutRelyingPartyName_shouldThrowException()
    {
        $this->expectException(MissingOrInvalidParameterException::class);
        $this->expectExceptionMessage("Missing or invalid parameter: Relying Party Name parameter must be set in client or request");

        $request = CertificateRequest::newBuilder()
            ->withPhoneNumber(TestData::VALID_PHONE)
            ->withNationalIdentityNumber(TestData::VALID_NAT_IDENTITY)
            ->build();

        $connector = MobileIdRestConnector::newBuilder()
            ->withEndpointUrl(TestData::DEMO_HOST_URL)
            ->withRelyingPartyUUID(TestData::DEMO_RELYING_PARTY_UUID)
            ->withSslPinnedPublicKeys(TestData::DEMO_HOST_PUBLIC_KEY_HASH)
            ->build();

        $connector->pullCertificate($request);
    }

    /**
     * @test
     */
    public function getCertificate_withoutPhoneNumber_shouldThrowException()
    {
        $this->expectException(MissingOrInvalidParameterException::class);
        $this->expectExceptionMessage("Missing or invalid parameter: Phone number and national identity number must be set");

        $request = CertificateRequest::newBuilder()
            ->withNationalIdentityNumber(TestData::VALID_NAT_IDENTITY)
            ->build();

        $connector = MobileIdRestConnector::newBuilder()
            ->withEndpointUrl(TestData::DEMO_HOST_URL)
            ->withRelyingPartyUUID(TestData::DEMO_RELYING_PARTY_UUID)
            ->withRelyingPartyName(TestData::DEMO_RELYING_PARTY_NAME)
            ->build();

        $connector->pullCertificate($request);
    }

    /**
     * @test
     */
    public function getCertificate_withoutNationalIdentityNumber_shouldThrowException()
    {
        $this->expectException(MissingOrInvalidParameterException::class);
        $this->expectExceptionMessage("Missing or invalid parameter: Phone number and national identity number must be set");

        $request = CertificateRequest::newBuilder()
            ->withPhoneNumber(TestData::VALID_PHONE)
            ->build();

        $connector = MobileIdRestConnector::newBuilder()
            ->withEndpointUrl(TestData::DEMO_HOST_URL)
            ->withRelyingPartyUUID(TestData::DEMO_RELYING_PARTY_UUID)
            ->withRelyingPartyName(TestData::DEMO_RELYING_PARTY_NAME)
            ->build();

        $connector->pullCertificate($request);
    }

    /**
     * @test
     */
    public function getCertificate_withCertificateNotPresent_shouldThrowException()
    {
        $this->expectException(MidNotMidClientException::class);

        $this->getConnector()->getCertificateChoiceResponseToRespond()->setResult("NOT_FOUND");
        $this->makeCertificateRequest($this->getConnector());
    }

    /**
     * @test
     */
    public function getCertificate_withResultMissingInResponse_shouldThrowException()
    {
        $this->expectException(TypeError::class);

        $this->getConnector()->getCertificateChoiceResponseToRespond()->setResult(null);
        $this->makeCertificateRequest($this->getConnector());
    }

    /**
     * @test
     */
    public function getCertificate_withResultBlankInResponse_shouldThrowException()
    {
        $this->expectException(MidInternalErrorException::class);

        $this->getConnector()->getCertificateChoiceResponseToRespond()->setResult("");
        $this->makeCertificateRequest($this->getConnector());
    }

    /**
     * @test
     */
    public function getCertificate_withCertificateMissingInResponse_shouldThrowException()
    {
        $this->expectException(TypeError::class);

        $this->getConnector()->getCertificateChoiceResponseToRespond()->setCert(null);
        $this->makeCertificateRequest($this->getConnector());
    }

    /**
     * @test
     */
    public function getCertificate_withCertificateBlankInResponse_shouldThrowException()
    {
        $this->expectException(MidInternalErrorException::class);

        $this->getConnector()->getCertificateChoiceResponseToRespond()->setCert("");
        $this->makeCertificateRequest($this->getConnector());
    }

    private function makeCertificateRequest(MobileIdConnector $connector)
    {
        $request = CertificateRequest::newBuilder()
            ->withPhoneNumber(TestData::VALID_PHONE)
            ->withNationalIdentityNumber(TestData::VALID_NAT_IDENTITY)
            ->build();

        $response = $connector->pullCertificate($request);
        $client = MobileIdClient::newBuilder()
            ->withRelyingPartyUUID(TestData::DEMO_RELYING_PARTY_UUID)
            ->withRelyingPartyName(TestData::DEMO_RELYING_PARTY_NAME)
            ->withHostUrl(TestData::DEMO_HOST_URL)
            ->withSslPinnedPublicKeys("sha256//...")
            ->build();
        $client->createMobileIdCertificate($response);
    }

    private static function createDummyCertificateChoiceResponse()
    {
        $params = array('result' => 'OK', 'cert' => TestData::AUTH_CERTIFICATE_EE);

        return new CertificateResponse($params);

    }


}
