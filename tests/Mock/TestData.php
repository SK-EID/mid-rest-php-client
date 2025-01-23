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
namespace Sk\Mid\Tests\Mock;
class TestData
{
    const AUTHENTICATION_SESSION_PATH = "/authentication/session/{sessionId}";

    const AUTH_CERTIFICATE_EE = "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";
    const AUTH_CERTIFICATE_LV = "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";
    const AUTH_CERTIFICATE_LT = "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";
    const ECC_CERTIFICATE = "MIIEVzCCAz+gAwIBAgIQMhKpLxxSmoxRrcXdufmvWTANBgkqhkiG9w0BAQUFADBsMQswCQYDVQQGEwJFRTEiMCAGA1UECgwZQVMgU2VydGlmaXRzZWVyaW1pc2tlc2t1czEfMB0GA1UEAwwWVEVTVCBvZiBFU1RFSUQtU0sgMjAxMTEYMBYGCSqGSIb3DQEJARYJcGtpQHNrLmVlMB4XDTEzMDYwNDEwNDc1N1oXDTIzMDkwNzEyMDYwOVowgaExCzAJBgNVBAYTAkVFMRswGQYDVQQKDBJFU1RFSUQgKE1PQklJTC1JRCkxFzAVBgNVBAsMDmF1dGhlbnRpY2F0aW9uMSMwIQYDVQQDDBpURVNUTlVNQkVSLEVDQywxNDIxMjEyODAyOTETMBEGA1UEBAwKVEVTVE5VTUJFUjEMMAoGA1UEKgwDRUNDMRQwEgYDVQQFEwsxNDIxMjEyODAyOTBZMBMGByqGSM49AgEGCCqGSM49AwEHA0IABHYleZg39CkgQGU8z8b8ehctBEnaGlducij6eTETeOj2LpEwLedMS1pCfNEZAJjDwAZ2DJMBgB05QHrrvzersUKjggGIMIIBhDAJBgNVHRMEAjAAMA4GA1UdDwEB/wQEAwIEsDCBmQYDVR0gBIGRMIGOMIGLBgorBgEEAc4fAwMBMH0wWAYIKwYBBQUHAgIwTB5KAEEAaQBuAHUAbAB0ACAAdABlAHMAdABpAG0AaQBzAGUAawBzAC4AIABPAG4AbAB5ACAAZgBvAHIAIAB0AGUAcwB0AGkAbgBnAC4wIQYIKwYBBQUHAgEWFWh0dHA6Ly93d3cuc2suZWUvY3BzLzAdBgNVHQ4EFgQUnd6oi9wn+4hQ+LtPvEdaXM5kz+gwIAYDVR0lAQH/BBYwFAYIKwYBBQUHAwIGCCsGAQUFBwMEMCIGCCsGAQUFBwEDBBYwFDAIBgYEAI5GAQEwCAYGBACORgEEMB8GA1UdIwQYMBaAFEG2/sWxsbRTE4z6+mLQNG1tIjQKMEUGA1UdHwQ+MDwwOqA4oDaGNGh0dHA6Ly93d3cuc2suZWUvcmVwb3NpdG9yeS9jcmxzL3Rlc3RfZXN0ZWlkMjAxMS5jcmwwDQYJKoZIhvcNAQEFBQADggEBADAmT+luPaCMnO78z5L9QY3K5/5yfC7r1/PQ76rzvzKem/zfsJV2y0siVaWgLLoxu1ZzxnOe7zBGNGKF/w4pLoiDZkaj9HE9JCoirrvbOg6sCJF942RGtIFLSAYfM1N8vPXPIN8a9pgTLxSpWYox//hY4OHbPEbOysQ65hAkIH15yhnHTCQBcMvv7+vdZUw1siVqj3DRAXY0VnqI6WWsVBPgpSu8g5StXEihaHZsq8cDmu4VtZi6VcUzifCxZa0ZjvGeJr5LkVA0pE56u37W99PSNY0VS/xhrdqNW4EiRB6V8zAz7LaAk83kh1BoXa5ayOuWmSCs28NQCz0lDT4Zw2A=";
    const RANDOM_CERTIFICATE = "MIIDBzCCAe+gAwIBAgIJAOU7gvOXnUDEMA0GCSqGSIb3DQEBBQUAMBoxGDAWBgNVBAMMD3d3dy5leGFtcGxlLmNvbTAeFw0xOTEwMDExMzE2NDRaFw0yOTA5MjgxMzE2NDRaMBoxGDAWBgNVBAMMD3d3dy5leGFtcGxlLmNvbTCCASIwDQYJKoZIhvcNAQEBBQADggEPADCCAQoCggEBANvWbf8JT6fWOQhpe8z2/3fkG5JVekTXGx4jdkTdK0iI0UfbxMDDUy6BE8FxkssTNjI0aTw/tRkPUD7RqjORmGNtLnUCf8cRra1+dvuZLWv6/EXOHUX602tckRe/01tY8e2nKI4tqzX6w4lHx4WPCBVkQAUm5TGjUQ+mYQI2fVYN1QIoZoZpi1BxRBinSzjuIIvbtL6sOAK/xnGyXe9kJr0OGghPwy9A2TfgIKQGVor6UxTj3qxg8QM63hHXvFenDkmfO7ddvSZQZgwec3nT6Buspx60gx2KS0/TAzfHDG2phNPVf1eJ5NmuYhRRJFIvu7gHOtu8fps5Up6hkP4sjhcCAwEAAaNQME4wHQYDVR0OBBYEFAAU6lPcxfXZBJh7X+rbjlHe3vI2MB8GA1UdIwQYMBaAFAAU6lPcxfXZBJh7X+rbjlHe3vI2MAwGA1UdEwQFMAMBAf8wDQYJKoZIhvcNAQEFBQADggEBAHpWQHddJNhlGI+m7zgngKqxdsI3a9H2h7PCxEaJOVK+aeSjDWD3+klBRzUeqhjvY2L7/oxe+6z9G3lzOYNFAWCUg7wBd22Xyk/NuaMXG65HwufCRwJzl9JrULWDJB+hraMYy4jpDQ2C3xCZbXep6BORR9xuN6HCBJkNiZgfDhCux3xWagGWoB5uXTGH8JHUvHQogba+/ECLHMaTv6bQ4wzWiNrmzEHQrAB0Lw9TjfoFchsGFAY90IO6g5/VtWE39Y9c+NUl8laIlL2N5EjldFaZThnmQPOXK4Q6thRbppjai5tXOGFOj2XFtij1eh8VWUPh/eA7kmpuW0RL9KLgzt4=";

    const DEMO_HOST_URL = "https://tsp.demo.sk.ee/mid-api";
    const DEMO_HOST_PUBLIC_KEY_HASH = "sha256//Fnjp3/Yi4oPFBG+GtDV2ZVJEPcVVJA5r/T7QK+VXPpM=";
    const SOME_OTHER_HOST_PUBLIC_KEY_HASH = "sha256//fqp7yWK7iGGKj+3unYdm2DA3VCPDkwtyX+DrdZYSC6o=";

    const SESSION_ID = "97f5058e-e308-4c83-ac14-7712b0eb9d86";

    const DEMO_RELYING_PARTY_UUID = "00000000-0000-0000-0000-000000000000";
    const DEMO_RELYING_PARTY_NAME = "DEMO";
    const UNKNOWN_RELYING_PARTY_UUID = "de435d54-75b4-551b-adb2-eb6b9e546322";
    const UNKNOWN_RELYING_PARTY_NAME = "unknown-relying-party-name";
    const WRONG_RELYING_PARTY_UUID = "11111111-0000-0000-0000-000000000000";
    const WRONG_RELYING_PARTY_NAME = "12345678910123456789101234567891012345678910123456789101234567891012345678910123456789101234567891012345678910";

    const VALID_PHONE = "+37200000766";
    const VALID_NAT_IDENTITY = "60001019906";
    const WRONG_PHONE = "+3701230000";
    const WRONG_NAT_IDENTITY = "60000000000";

    const SHA256_HASH_IN_BASE64 = "AE7S1QxYjqtVv+Tgukv2bMMi9gDCbc9ca2vy/iIG6ug=";
    const SHA384_HASH_IN_BASE64 = "tciNHij/GJQovJJFEvpOOnanZQjpys2PoAooHxooR6EopvGvsTfXJAwpqOKZC2sV";
    const SHA512_HASH_IN_BASE64 = "kc42j4tGXa1Pc2LdMcJCKAgpOk9RCQgrBogF6fHA40VSPw1qITw8zQ8g5ZaLcW5jSlq67ehG3uSvQAWIFs3TOw==";

    const VALID_SIGNATURE_IN_BASE64 = "NADdzIcz1d8l44vived6VTK8ZKslgtcnYFYgwhX2bD/UDxg1C0zbvzCsOJtz1SJdiAk0YNmZf6IijvC8OsnD2A==";
    const VALID_ECC_SIGNATURE_IN_BASE64 = "AAAUr/OqPnEXT97w36t2JH9YO1B9Tte4hO3ZYNEw+HgAhfnvnZ9tiJzTJFWy603tmVtgNalRH2Ex0QOCYTYy8g==";
    const SIGNED_HASH_IN_BASE64 = "XN1Ej7bys/MlbZlDBN3GoRFv6JOJbm8/mLwulZlZQBIF/TYVsHDJiOCGx2yVaxiXbbrNmRQJcgR0AhZtTfFigQ==";
    const SIGNED_ECC_HASH_IN_BASE64 = "ChvbzfuBCQNxx7f0H0ImkhwW+1BDeg9ei15wBeO1Sxk4QS6uhZn14dmS1xqWTDxWGK7wAyJN7Bn12pUwBNWE3A==";

}
