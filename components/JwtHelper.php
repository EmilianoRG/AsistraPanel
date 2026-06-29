<?php
namespace app\components;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Yii;

class JwtHelper
{
    // TTL en segundos (8 horas por defecto)
    protected static int $ttl = 3600 * 8;

    public static function getTtlSeconds(): int
    {
        return self::$ttl;
    }

    public static function generateToken(int $userId, string $username): string
    {
        $now = time();
        $exp = $now + self::$ttl;
        $payload = [
            'iss' => Yii::$app->request->getHostInfo(),
            'aud' => Yii::$app->request->getHostInfo(),
            'iat' => $now,
            'nbf' => $now,
            'exp' => $exp,
            'sub' => $userId,
            'user' => $username,
        ];

        $key = self::getSecret();
        return JWT::encode($payload, $key, 'HS256');
    }

    public static function validateToken(string $token)
    {
        try {
            $key = self::getSecret();
            $decoded = JWT::decode($token, new Key($key, 'HS256'));
            return $decoded;
        } catch (\Throwable $e) {
            Yii::warning('JWT validation failed: ' . $e->getMessage(), __METHOD__);
            return false;
        }
    }

    protected static function getSecret(): string
    {
        $params = Yii::$app->params;
        if (empty($params['jwtSecret'])) {
            throw new \RuntimeException('JWT secret not configured in params.php (jwtSecret).');
        }
        return (string)$params['jwtSecret'];
    }
}

