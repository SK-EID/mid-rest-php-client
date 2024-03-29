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
namespace Sk\Mid;
use Sk\Mid\Exception\MidInternalErrorException;
use Sk\Mid\Util\Logger;
use Spatie\SslCertificate\SslCertificate;

class CertificateParser
{
    /** @var Logger $logger */
    public static $logger;
    const BEGIN_CERT = '-----BEGIN CERTIFICATE-----';
    const END_CERT = '-----END CERTIFICATE-----';

    public function __construct()
    {
        self::$logger = new Logger('CertificateParser');
    }

    public static function parseX509Certificate( string $certificateValue ) : array
    {
        $certificateString = self::getPemCertificate(  $certificateValue );
        $result = openssl_x509_parse( $certificateString );
        if ($result === false || count($result) <= 1) {
            throw new MidInternalErrorException('Failed to parse certificate');
        }
        $result['certificateAsString'] = $certificateString;
        return $result;
    }

  /**
   * @param string $certificateValue
   * @return string
   */
    public static function getPemCertificate( string $certificateValue ) : string
    {
        if ( substr( $certificateValue, 0, strlen( self::BEGIN_CERT ) ) === self::BEGIN_CERT )
        {
            $certificateValue = substr( $certificateValue, strlen( self::BEGIN_CERT ) );
        }

        if ( substr( $certificateValue, -strlen( self::END_CERT ) ) === self::END_CERT )
        {
            $certificateValue = substr( $certificateValue, 0, -strlen( self::END_CERT ) );
        }

        $certificateValue = implode( "\n", str_split( $certificateValue, 64 ) );
        return self::BEGIN_CERT . "\n" . $certificateValue . "\n" . self::END_CERT;
    }
}
