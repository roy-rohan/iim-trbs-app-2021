<?php
include __DIR__ . '/../vendor/autoload.php';
include_once __DIR__ . "/error_messages.php";

use Firebase\JWT\BeforeValidException;
use Firebase\JWT\ExpiredException;
use Laminas\Config\Factory as ConfigFactory;
use Firebase\JWT\JWT;
use Firebase\JWT\SignatureInvalidException;


function encodeToken($data)
{
    $config = ConfigFactory::fromFile(__DIR__ . '/config.php', true);
    $tokenId    = base64_encode(openssl_random_pseudo_bytes(32));
    $issuedAt   = time();
    $expire     = $issuedAt + 60 * 60 * 2;
    $serverName = $config->get('serverName');

    /*
     * Create the token as an array
     */
    $data = [
        'iat'  => $issuedAt,
        'jti'  => $tokenId,
        'iss'  => $serverName,
        'exp'  => $expire,
        'data' => $data
    ];

    $secretKey = base64_decode($config->get('jwt')->get('key'));

    $algorithm = $config->get('jwt')->get('algorithm');

    $jwt = JWT::encode(
        $data,
        $secretKey,
        $algorithm
    );

    return $jwt;
}


function decodeToken($token)
{
    $config = ConfigFactory::fromFile(__DIR__ . '/config.php', true);

    $secretKey = base64_decode($config->get('jwt')->get('key'));

    try {
        $algorithm = $config->get('jwt')->get('algorithm');
        $token = JWT::decode($token, $secretKey, [$algorithm]);
    } catch (UnexpectedValueException $e) {
        $message = $e->getMessage();
        throw new RuntimeException($message);
    } catch (SignatureInvalidException $e) {
        $message = JWT_INVALID_SIGNATURE;
        throw new RuntimeException($message);
    } catch (BeforeValidException $e) {
        $message = JWT_INVALID_TOKEN;
        throw new RuntimeException($message);
    } catch (ExpiredException $e) {
        $message = JWT_TOKEN_EXPIRED;
        throw new RuntimeException($message);
    }

    return $token->data;
}


function authenicateRequest($authHeader)
{

    $matches = [];

    if (!preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
        $exp = new CustomException('No authentication token found');
        $exp->sendBadRequest();
        exit(1);
    } else {
        $jwt = $matches[1];
        if (!$jwt) {
            $exp = new CustomException('No authentication token found');
            $exp->sendBadRequest();
            exit(1);
        } else {
            try {
                $user_data = decodeToken($jwt);
                return $user_data;
            } catch (Exception $e) {
                $exp = new CustomException($e->getMessage());
                $exp->sendBadRequest();
                exit(1);
            }
        }
    }
}
