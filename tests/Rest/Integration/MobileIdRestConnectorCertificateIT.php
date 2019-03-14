<?php
namespace Sk\Mid\Tests\Rest\Integration;
use Sk\Mid\Exception\NotMidClientException;
use Sk\Mid\Rest\Dao\Request\CertificateRequest;
use Sk\Mid\Tests\Mock\MobileIdRestServiceRequestDummy;
use Sk\Mid\Exception\UnauthorizedException;
use Sk\Mid\Tests\Mock\TestData;
use Sk\Mid\Rest\MobileIdRestConnector;

use PHPUnit\Framework\TestCase;

/**
 * Created by PhpStorm.
 * User: mikks
 * Date: 2/21/2019
 * Time: 1:42 PM
 */
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
     * @throws \Exception
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
        $this->expectException(NotMidClientException::class);

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
        $this->expectException(NotMidClientException::class);

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
        $this->expectException(UnauthorizedException::class);

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
        $this->expectException(UnauthorizedException::class);

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
        $this->expectException(UnauthorizedException::class);

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
        $this->expectException(UnauthorizedException::class);

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
