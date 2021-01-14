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
use Sk\Mid\Language\EST;
use Sk\Mid\Exception\MissingOrInvalidParameterException;
use Sk\Mid\Rest\Dao\Request\AuthenticationRequest;
use Sk\Mid\Rest\MobileIdRestConnector;
use Sk\Mid\Exception\MidUnauthorizedException;
use Sk\Mid\Tests\Mock\MobileIdRestServiceRequestDummy;
use Sk\Mid\Tests\Mock\MobileIdRestServiceResponseDummy;
use Sk\Mid\Tests\Mock\TestData;
use Sk\Mid\Tests\Mock\SessionStatusPollerDummy;
use PHPUnit\Framework\TestCase;


class MobileIdRestConnectorAuthenticationIT extends TestCase
{
    const AUTHENTICATION_SESSION_PATH = "/authentication/session/{sessionId}";

    private $connector;

    private function getConnector() : MobileIdRestConnector
    {
        return $this->connector;
    }

    protected function setUp() : void
    {
        $this->connector = MobileIdRestConnector::newBuilder()
            ->withEndpointUrl(TestData::DEMO_HOST_URL)
            ->withSslPinnedPublicKeys(TestData::DEMO_HOST_PUBLIC_KEY_HASH)
            ->build();
    }

    /**
     * @test
     */
    public function authenticate_withDisplayText()
    {
        $request = MobileIdRestServiceRequestDummy::createValidAuthenticationRequest();
        $request->setDisplayText("Log into internet banking system");
        MobileIdRestServiceRequestDummy::assertCorrectAuthenticationRequestMade($request);

        $response = $this->getConnector()->initAuthentication($request);
        $this->assertNotEmpty($response->getSessionId());

        $sessionStatus = SessionStatusPollerDummy::pollSessionStatus($this->connector, $response->getSessionId());
        MobileIdRestServiceResponseDummy::assertAuthenticationPolled($sessionStatus);
    }

    /**
     * @test
     */
    public function authenticate_withWrongPhoneNumber_shouldThrowException()
    {
        $this->expectException(MissingOrInvalidParameterException::class);

        $request = AuthenticationRequest::newBuilder()
            ->withRelyingPartyUUID(TestData::DEMO_RELYING_PARTY_UUID)
            ->withRelyingPartyName(TestData::DEMO_RELYING_PARTY_NAME)
            ->withPhoneNumber("123")
            ->withNationalIdentityNumber(TestData::VALID_NAT_IDENTITY)
            ->withHashToSign(MobileIdRestServiceRequestDummy::calculateMobileIdAuthenticationHash())
            ->withLanguage(EST::asType())
            ->build();

        $this->getConnector()->initAuthentication($request);
    }

    /**
     * @test
     */
    public function authenticate_withWrongRelyingPartyUUID_shouldThrowException()
    {
        $this->expectException(MidUnauthorizedException::class);

        $request = MobileIdRestServiceRequestDummy::createAuthenticationRequest(
            TestData::WRONG_RELYING_PARTY_UUID, TestData::DEMO_RELYING_PARTY_NAME, TestData::VALID_PHONE, TestData::VALID_NAT_IDENTITY
        );
        $this->getConnector()->initAuthentication($request);
    }

    /**
     * @test
     */
    public function authenticate_withWrongRelyingPartyName_shouldThrowException()
    {
        $this->expectException(MissingOrInvalidParameterException::class);

        $request = MobileIdRestServiceRequestDummy::createAuthenticationRequest(
            TestData::DEMO_RELYING_PARTY_UUID, TestData::WRONG_RELYING_PARTY_NAME, TestData::VALID_PHONE, TestData::VALID_NAT_IDENTITY
        );
        $this->getConnector()->initAuthentication($request);
    }

    /**
     * @test
     */
    public function authenticate_withUnknownRelyingPartyUUID_shouldThrowException()
    {
        $this->expectException(MidUnauthorizedException::class);

        $request = MobileIdRestServiceRequestDummy::createAuthenticationRequest(
            TestData::DEMO_RELYING_PARTY_UUID, TestData::UNKNOWN_RELYING_PARTY_NAME, TestData::VALID_PHONE, TestData::VALID_NAT_IDENTITY
        );
        $this->getConnector()->initAuthentication($request);
    }

    /**
     * @test
     */
    public function authenticate_withUnknownRelyingPartyName_shouldThrowException()
    {
        $this->expectException(MidUnauthorizedException::class);

        $request = MobileIdRestServiceRequestDummy::createAuthenticationRequest(
            TestData::UNKNOWN_RELYING_PARTY_UUID, TestData::DEMO_RELYING_PARTY_NAME, TestData::VALID_PHONE, TestData::VALID_NAT_IDENTITY
        );
        $this->getConnector()->initAuthentication($request);
    }




}
