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
namespace Sk\Mid\Tests\integration;
use PHPUnit\Framework\TestCase;
use Sk\Mid\Rest\Dao\Request\CertificateRequest;
use Sk\Mid\Tests\Mock\MobileIdRestServiceRequestDummy;
use Sk\Mid\Tests\Mock\TestData;
use Sk\Mid\MobileIdClient;
use Sk\Mid\Exception\MidNotMidClientException;

class MobileIdCertificateIT extends TestCase
{

    /**
     * @test
     */
    public function getCertificateTest()
    {
        $client = MobileIdClient::newBuilder()
            ->withRelyingPartyUUID(TestData::DEMO_RELYING_PARTY_UUID)
            ->withRelyingPartyName(TestData::DEMO_RELYING_PARTY_NAME)
            ->withHostUrl(TestData::DEMO_HOST_URL)
            ->withSslPinnedPublicKeys(TestData::DEMO_HOST_PUBLIC_KEY_HASH)
            ->build();


        $certRequest = CertificateRequest::newBuilder()
            ->withNationalIdentityNumber(TestData::VALID_NAT_IDENTITY)
            ->withPhoneNumber(TestData::VALID_PHONE)
            ->build();

        $resp = $client->getMobileIdConnector()->pullCertificate($certRequest);

        $this->assertEquals('OK', $resp->getResult());
        $this->assertNotNull($resp->getCert());

    }

    /**
     * @test
     */
    public function getCertificate_notMidClient_shouldThrowNotMIDClientException()
    {
        $this->expectException(MidNotMidClientException::class);

        $client = MobileIdClient::newBuilder()
            ->withRelyingPartyUUID(TestData::DEMO_RELYING_PARTY_UUID)
            ->withRelyingPartyName(TestData::DEMO_RELYING_PARTY_NAME)
            ->withHostUrl(TestData::DEMO_HOST_URL)
            ->withSslPinnedPublicKeys(TestData::DEMO_HOST_PUBLIC_KEY_HASH)
            ->build();

        $certRequest = CertificateRequest::newBuilder()
            ->withNationalIdentityNumber(60001019928)
            ->withPhoneNumber("+37060000366")
            ->build();

        $client->getMobileIdConnector()->pullCertificate($certRequest);
    }

    /**
     * @test
     */
    public function getCertificate_certificateNotActive_shouldThrowNotMIDClientException()
    {
        $this->expectException(MidNotMidClientException::class);

        $client = MobileIdClient::newBuilder()
            ->withRelyingPartyUUID(TestData::DEMO_RELYING_PARTY_UUID)
            ->withRelyingPartyName(TestData::DEMO_RELYING_PARTY_NAME)
            ->withHostUrl(TestData::DEMO_HOST_URL)
            ->withSslPinnedPublicKeys(TestData::DEMO_HOST_PUBLIC_KEY_HASH)
            ->build();

        $certRequest = CertificateRequest::newBuilder()
            ->withNationalIdentityNumber(60001019939)
            ->withPhoneNumber("+37060000266")
            ->build();

        $client->getMobileIdConnector()->pullCertificate($certRequest);
    }

}
