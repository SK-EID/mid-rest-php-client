<?php
/*-
 * #%L
 * Mobile ID sample PHP client
 * %%
 * Copyright (C) 2018 - 2019 SK ID Solutions AS
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
class SignatureVerifier
{

    public static $logger;

    public function __construct()
    {
        $this->logger = new Logger('SignatureVerifier');
    }

    public static function verifyWithRSA($signersPublicKey, $authentication)
    {

        try {
            $signature = Signature::getInstance('NONEwithRSA');
            $signature->initVerify($signersPublicKey);
            $signedHash = Base64::decodeBase64($authentication->getSignedHashInBase64());
            $signedDigest = self::addPadding($authentication->getHashType()->getDigestInfoPrefix(), $signedHash);
            $signature->update($signedDigest);
            return $signature->verify($authentication->getSignatureValue());
        } catch (GeneralSecurityException $var5) {
            self::$logger->error('Signature verification with RSA failed');
            throw new TechnicalErrorException('Signature verification with RSA failed', $var5);
        }
    }

    private function addPadding($digestInfoPrefix, $digest)
    {
        return array_push($digestInfoPrefix, $digest);
    }

    public static function verifyWithECDSA($signersPublicKey, $authentication)
    {
        try {
            Security::addProvider(new BouncyCastleProvider());
            $signature = Signature::getInstance('NONEwithEDSA', 'BC');
            $signature->initVerify($signersPublicKey);
            $signedDigest = Base64::decodeBase64($authentication->getSignedHashInBase64());
            $signature->update($signedDigest);
            return $signature->verify(fromCVCEncoding($authentication->getSignatureValue()));
        } catch (GeneralSecurityException $var4) {
            self::$logger->error('Signature verification with ECDSA failed');
            throw new TechnicalErrorException('Signature verification with ECDSA failed');
        }
    }

    private static function fromCVCEncoding($cvcEncoding)
    {
        $elements = self::splitArrayInTheMiddle($cvcEncoding);
        $r = $elements[0];
        $s = $elements[1];
        return self::encodeInAsnl($r, $s);
    }

    private static function splitArrayInTheMiddle($array)
    {
        return array_chunk($array, count($array) / 2);
    }

    private static function encodeInAsnl($r, $s)
    {
        $sequence = array();
        array_push($sequence, $r, $s);
        return $sequence;
    }




}
