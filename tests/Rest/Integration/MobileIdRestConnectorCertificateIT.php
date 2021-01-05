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
namespace Sk\Mid\Tests\Rest\Integration;
use Sk\Mid\Exception\MidNotMidClientException;
use Sk\Mid\Rest\Dao\Request\CertificateRequest;
use Sk\Mid\Tests\Mock\MobileIdRestServiceRequestDummy;
use Sk\Mid\Exception\MidUnauthorizedException;
use Sk\Mid\Tests\Mock\TestData;
use Sk\Mid\Rest\MobileIdRestConnector;

use PHPUnit\Framework\TestCase;

class MobileIdRestConnectorCertificateIT extends TestCase
{

    /** @var MobileIdRestConnector $connector */
    private $connector;

    private function getConnector() : MobileIdRestConnector
    {
        return $this->connector;
    }

    protected function setUp() : void
    {
        $this->connector = MobileIdRestConnector::newBuilder()
            ->withEndpointUrl(TestData::DEMO_HOST_URL)
            ->withRelyingPartyUUID(TestData::DEMO_RELYING_PARTY_UUID)
            ->withRelyingPartyName(TestData::DEMO_RELYING_PARTY_NAME)
            ->build();
    }

    /**
     * @test
     */
    public function getCertificate()
    {
        $request = new CertificateRequest();
        $request->setPhoneNumber(TestData::VALID_PHONE);
        $request->setNationalIdentityNumber(TestData::VALID_NAT_IDENTITY);
        MobileIdRestServiceRequestDummy::assertCorrectCertificateRequestMade($request);

        $response = $this->getConnector()->pullCertificate($request);

        assert(!is_null($response));
        try {
            $this->assertEquals("OK", $response->getResult());
        } catch (Exception $e) {
        }
        assert(!is_null($response->getCert()) && !empty($response->getCert()));
    }

    /**
     * @test
     */
    public function getCertificate_withWrongPhoneNumber_shouldThrowException()
    {
        $this->expectException(MidNotMidClientException::class);

        $request = CertificateRequest::newBuilder()
            ->withPhoneNumber(TestData::WRONG_PHONE)
            ->withNationalIdentityNumber(TestData::VALID_NAT_IDENTITY)
            ->build();

        $this->getConnector()->pullCertificate($request);
    }

    /**
     * @test
     */
    public function getCertificate_withWrongNationalIdentityNumber_shouldThrowException()
    {
        $this->expectException(MidNotMidClientException::class);

        $request = CertificateRequest::newBuilder()
                ->withPhoneNumber(TestData::VALID_PHONE)
                ->withNationalIdentityNumber(TestData::WRONG_NAT_IDENTITY)
                ->build();


        $this->getConnector()->pullCertificate($request);
    }

    /**
     * @test
     */
    public function getCertificate_withWrongRelyingPartyUUID_shouldThrowException()
    {
        $this->expectException(MidUnauthorizedException::class);

        $connector = MobileIdRestConnector::newBuilder()
            ->withEndpointUrl(TestData::DEMO_HOST_URL)
            ->withRelyingPartyUUID(TestData::WRONG_RELYING_PARTY_UUID)
            ->withRelyingPartyName(TestData::DEMO_RELYING_PARTY_NAME)
            ->build();

        $request = new CertificateRequest();
        $request->setPhoneNumber(TestData::VALID_PHONE);
        $request->setNationalIdentityNumber(TestData::VALID_NAT_IDENTITY);
        $connector->pullCertificate($request);
    }

    /**
     * @test
     */
    public function getCertificate_withWrongRelyingPartyName_shouldThrowException()
    {
        $this->expectException(MidUnauthorizedException::class);

        $connector = MobileIdRestConnector::newBuilder()
            ->withEndpointUrl(TestData::DEMO_HOST_URL)
            ->withRelyingPartyUUID(TestData::DEMO_RELYING_PARTY_UUID)
            ->withRelyingPartyName("wrong name")
            ->build();

        $request = new CertificateRequest();
        $request->setPhoneNumber(TestData::VALID_PHONE);
        $request->setNationalIdentityNumber(TestData::VALID_NAT_IDENTITY);
        $connector->pullCertificate($request);
    }

    /**
     * @test
     */
    public function getCertificate_withUnknownRelyingPartyUUID_shouldThrowException()
    {
        $this->expectException(MidUnauthorizedException::class);

        $connector = MobileIdRestConnector::newBuilder()
            ->withEndpointUrl(TestData::DEMO_HOST_URL)
            ->withRelyingPartyUUID(TestData::DEMO_RELYING_PARTY_UUID)
            ->withRelyingPartyName(TestData::UNKNOWN_RELYING_PARTY_NAME)
            ->build();

        $request = new CertificateRequest();
        $request->setPhoneNumber(TestData::VALID_PHONE);
        $request->setNationalIdentityNumber(TestData::VALID_NAT_IDENTITY);
        $connector->pullCertificate($request);
    }

    /**
     * @test
     */
    public function getCertificate_withUnknownRelyingPartyName_shouldThrowException()
    {
        $this->expectException(MidUnauthorizedException::class);

        $connector = MobileIdRestConnector::newBuilder()
            ->withEndpointUrl(TestData::DEMO_HOST_URL)
            ->withRelyingPartyUUID(TestData::UNKNOWN_RELYING_PARTY_UUID)
            ->withRelyingPartyName(TestData::DEMO_RELYING_PARTY_NAME)
            ->build();

        $request = new CertificateRequest();
        $request->setPhoneNumber(TestData::VALID_PHONE);
        $request->setNationalIdentityNumber(TestData::VALID_NAT_IDENTITY);
        $connector->pullCertificate($request);
    }


}
