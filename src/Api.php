<?php

namespace Atlantis;

use Exception;
use Firebase\JWT\{ExpiredException, JWT};
use stdClass;

/**
 * Atlantis API core class
 *
 * Response status codes:
 * 0 - Failure,
 * 1 - Success,
 * 2 - Bad credentials,
 * 3 - Access token expired,
 * 4 - Refresh token expired;
 *
 */
class Api extends Model
{
    private string $privateKey = <<<EOD
-----BEGIN RSA PRIVATE KEY-----
MIICXAIBAAKBgQC8kGa1pSjbSYZVebtTRBLxBz5H4i2p/llLCrEeQhta5kaQu/Rn
vuER4W8oDH3+3iuIYW4VQAzyqFpwuzjkDI+17t5t0tyazyZ8JXw+KgXTxldMPEL9
5+qVhgXvwtihXC1c5oGbRlEDvDF6Sa53rcFVsYJ4ehde/zUxo6UvS7UrBQIDAQAB
AoGAb/MXV46XxCFRxNuB8LyAtmLDgi/xRnTAlMHjSACddwkyKem8//8eZtw9fzxz
bWZ/1/doQOuHBGYZU8aDzzj59FZ78dyzNFoF91hbvZKkg+6wGyd/LrGVEB+Xre0J
Nil0GReM2AHDNZUYRv+HYJPIOrB0CRczLQsgFJ8K6aAD6F0CQQDzbpjYdx10qgK1
cP59UHiHjPZYC0loEsk7s+hUmT3QHerAQJMZWC11Qrn2N+ybwwNblDKv+s5qgMQ5
5tNoQ9IfAkEAxkyffU6ythpg/H0Ixe1I2rd0GbF05biIzO/i77Det3n4YsJVlDck
ZkcvY3SK2iRIL4c9yY6hlIhs+K9wXTtGWwJBAO9Dskl48mO7woPR9uD22jDpNSwe
k90OMepTjzSvlhjbfuPN1IdhqvSJTDychRwn1kIJ7LQZgQ8fVz9OCFZ/6qMCQGOb
qaGwHmUK6xzpUbbacnYrIM6nLSkXgOAwv7XXCojvY614ILTK3iXiLBOxPu5Eu13k
eUz9sHyD6vkgZzjtxXECQAkp4Xerf5TGfQXGXhxIX52yH+N2LtujCdkQZjXAsGdm
B2zNzvrlgRmgBrklMTrMYgm1NPcW+bRLGcwgW2PTvNM=
-----END RSA PRIVATE KEY-----
EOD;

    public string $publicKey = <<<EOD
-----BEGIN PUBLIC KEY-----
MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQC8kGa1pSjbSYZVebtTRBLxBz5H
4i2p/llLCrEeQhta5kaQu/RnvuER4W8oDH3+3iuIYW4VQAzyqFpwuzjkDI+17t5t
0tyazyZ8JXw+KgXTxldMPEL95+qVhgXvwtihXC1c5oGbRlEDvDF6Sa53rcFVsYJ4
ehde/zUxo6UvS7UrBQIDAQAB
-----END PUBLIC KEY-----
EOD;

    public string $algorithm = 'RS256';
    public int $expire = 1440; // 24 minutes
    public ?string $accessToken = null;
    public ?string $refreshToken = null;
    public ?stdClass $decoded = null;

    public static string $tableName = 'api';

    function __construct()
    {
        $this->accessToken = $this->getAccessToken();
        $this->refreshToken = $this->getRefreshToken();
    }

    function getAccessToken()
    {
        $request = new Request();
        return $request->bearer() ?? $request->jwt ?? null;
    }

    function getRefreshToken()
    {
        $request = new Request();
        return $request->jwr;
    }

    function generateAccessToken($data)
    {
        $issued = time();

        $token = [
            "iat" => $issued,
            "exp" => $issued + $this->expire,
            "iss" => $_SERVER['HTTP_HOST'],
            "data" => $data
        ];

        return JWT::encode($token, $this->privateKey, $this->algorithm);
    }

    function generateRefreshToken()
    {
        return bin2hex(random_bytes(64));
    }

    function fetchRefreshToken(int $userId)
    {
        return $this::where('id', '=', $userId)
            ->pluck('refresh_token')[0] ?? null;
    }

    function storeRefreshToken(int $userId, string $token): bool
    {
        return $this::upsert(
            [
                'id' => $userId,
                'refresh_token' => $token
            ],
            ['refresh_token' => $token]
        );
    }

    function deleteRefreshToken(string $token): bool
    {
        return $this::where('refresh_token', '=', $token)
            ->delete();
    }

    function headers()
    {
        header("Access-Control-Allow-Origin: *");
        header("Content-Type: application/json; charset=UTF-8");
        header("Access-Control-Allow-Methods: POST, GET");
        header("Access-Control-Max-Age: {$this->expire}");
        header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
    }

    function auth()
    {
        if (!$this->accessToken) {
            $this->response(401, 0, App::$lang->get('no_token'));
        }

        try {
            $this->decoded = $this->decode($this->accessToken);
            return $this;
        } catch (ExpiredException $e) {
            $this->response(401, 3, App::$lang->get('access_token_expired'));
        } catch (Exception $e) {
            $this->response(401, 0, $e->getMessage());
        }
    }

    function issue($data)
    {
        $jwt = $this->generateAccessToken($data);
        $jwr = $this->fetchRefreshToken($data->user->id);

        if (!$jwr) {
            $jwr = $this->generateRefreshToken();
            $this->storeRefreshToken($data->user->id, $jwr);
        }

        $this->response(200, 1, App::$lang->get('success'), ["jwt" => $jwt, "jwr" => $jwr]);
    }

    function decode($token)
    {
        return JWT::decode($token, $this->publicKey, [$this->algorithm]);
    }

    function refresh()
    {
        $jwr = $this->fetchRefreshToken($this->decoded->data->user->id);
        $this->deleteRefreshToken($this->refreshToken);

        if ($jwr != $this->refreshToken) {
            $this->response(401, 4, App::$lang->get('refresh_token_expired'));
        }

        $this->issue($this->decoded->data);
    }

    function response(int $code = 401, int $status = 0, string $message = '', array $data = [])
    {
        $this->headers();

        $data = array_merge(
            [
                "status" => $status,
                "message" => $message
            ],
            $data
        );

        http_response_code($code);
        die(json_encode($data, 256 | 32));
    }

    function accessDenied()
    {
        $this->response(401, 0, App::$lang->get('access_denied'));
    }
}
