<?php

namespace PolyCliniqueBorinage\Services;

use Namshi\JOSE\SimpleJWS;


class AuthenticationService extends BaseService {

  const SSL_KEY_PASSPHRASE = 'thisissogood';

  /**
   * Create a token.
   *
   * @param array $user
   *   The current user
   *
   * @return array|FALSE
   *   Return the booking if succeed or FALSE if not.
   *
   */
  public function createToken($user) {

    $secure['key.private'] = "-----BEGIN RSA PRIVATE KEY-----
MIICXAIBAAKBgQDfmlc2EgrdhvakQApmLCDOgP0nNERInBheMh7J/r5aU8PUAIpG
XET/8+kOGI1dSYjoux80AuHvkWp1EeHfMwC/SZ9t6rF4sYqV5Lj9t32ELbh2VNbE
/7QEVZnXRi5GdhozBZtS1gJHM2/Q+iToyh5dfTaAU8bTnLEPMNC1h3qcUQIDAQAB
AoGAcbh6UFqewgnpGKIlZ89bpAsANVckv1T8I7QT6qGvyBrABut7Z8t3oEE5r1yX
UPGcOtkoRniM1h276ex9VtoGr09sUn7duoLiEsp8aip7p7SB3X6XXWJ9K733co6C
dpXotfO0zMnv8l3O9h4pHrrBkmWDBEKbUeuE9Zz7uy6mFAECQQDygylLjzX+2rvm
FYd5ejSaLEeK17AiuT29LNPRHWLu6a0zl923299FCyHLasFgbeuLRCW0LMCs2SKE
Y+cIWMSRAkEA7AnzWjby8j8efjvUwIWh/L5YJyWlSgYKlR0zdgKxxUy9+i1MGRkn
m81NLYza4JLvb8/qjUtvw92Zcppxb7E7wQJAIuQWC+X12c30nLzaOfMIIGpgfKxd
jhFivZX2f66frkn2fmbKIorCy7c3TIH2gn4uFmJenlaV/ghbe/q3oa7L0QJAFP19
ipRAXpKGX6tqbAR2N0emBzUt0btfzYrfPKtYq7b7XfgRQFogT5aeOmLARCBM8qCG
tzHyKnTWZH6ff9M/AQJBAIToUPachXPhDyOpDBcBliRNsowZcw4Yln8CnLqgS9H5
Ya8iBJilFm2UlcXfpUOk9bhBTbgFp+Bv6BZ2Alag7pY=
-----END RSA PRIVATE KEY-----";

    $jws  = new SimpleJWS(array(
      'alg' => 'RS256'
    ));

    // iss: The issuer of the token
    // sub: The subject of the token
    // aud: The audience of the token
    // exp: Token expiration time defined in Unix time
    // nbf: “Not before” time that identifies the time before which the JWT must not be accepted for processing
    // iat: “Issued at” time, in Unix time, at which the token was issued
    // jti: JWT ID claim provides a unique identifier for the JWT

    // “iss”: “toptal.com”,
    // “exp”: 1426420800,
    // “http://toptal.com/jwt_claims/is_admin”: true,
    // “company”: “Toptal”,
    // “awesome”: true

    $jws->setPayload($user);

    $privateKey = openssl_pkey_get_private($secure['key.private'], self::SSL_KEY_PASSPHRASE);
    $jws->sign($privateKey);

    return $jws->getTokenString();
  }

  /**
   * Create a token.
   *
   * @return array|FALSE
   *   Return the booking if succeed or FALSE if not.
   *
   */
  public function validToken($token) {

    $secure['key.private'] = "-----BEGIN PUBLIC KEY-----
MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQDfmlc2EgrdhvakQApmLCDOgP0n
NERInBheMh7J/r5aU8PUAIpGXET/8+kOGI1dSYjoux80AuHvkWp1EeHfMwC/SZ9t
6rF4sYqV5Lj9t32ELbh2VNbE/7QEVZnXRi5GdhozBZtS1gJHM2/Q+iToyh5dfTaA
U8bTnLEPMNC1h3qcUQIDAQAB
-----END PUBLIC KEY-----";

    $jws = SimpleJWS::load($token);
    $public_key = openssl_pkey_get_public($secure['key.private']);

    if ($jws->isValid($public_key, 'RS256')) {
      $payload = $jws->getPayload();
      echo sprintf("Hey, my JS app just did an action authenticated as user #%s", $payload['id']);
    }
  }

}
