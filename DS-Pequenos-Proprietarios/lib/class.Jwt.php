<?php
class Jwt {
    private static string $secret = 'ds-pequenos-proprietarios-jwt-secret';
    private static int $ttl = 86400;

    private static function b64url(string $data): string {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    private static function b64urlDecode(string $data): string {
        return base64_decode(strtr($data, '-_', '+/'));
    }

    public static function encode(array $payload): string {
        $header = self::b64url(json_encode(['typ' => 'JWT', 'alg' => 'HS256']));
        $payload['exp'] = time() + self::$ttl;
        $body = self::b64url(json_encode($payload));
        $sig = self::b64url(hash_hmac('sha256', "$header.$body", self::$secret, true));
        return "$header.$body.$sig";
    }

    public static function decode(string $token): array {
        $parts = explode('.', $token);
        if (count($parts) !== 3) {
            throw new RuntimeException('Token inválido');
        }

        [$header, $body, $sig] = $parts;
        $expected = self::b64url(hash_hmac('sha256', "$header.$body", self::$secret, true));

        if (!hash_equals($expected, $sig)) {
            throw new RuntimeException('Assinatura inválida');
        }

        $data = json_decode(self::b64urlDecode($body), true);
        if (!$data || ($data['exp'] ?? 0) < time()) {
            throw new RuntimeException('Token expirado');
        }

        return $data;
    }
}
