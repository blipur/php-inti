<?php

declare(strict_types=1);

namespace Imadepurnamayasa\PhpInti\Authentication;

use DateTime;
use Imadepurnamayasa\PhpInti\Database\ORM;
use Imadepurnamayasa\PhpInti\Helpers;

/**
 * Class BaseUser
 * Kelas abstrak dasar yang menyediakan fitur autentikasi dan manajemen token pengguna.
 */
abstract class BaseUser extends ORM
{
    /** @var string Nama tabel basis data. */
    protected $table = 'users';

    /** @var string Nama primary key tabel. */
    protected $primaryKey = 'id';

    public $id = 'id';
    public $username = 'username';
    public $password = 'password';
    public $email = 'email';
    public $token = 'token';
    public $tokenExpired = 'token_expired';
    public $secretKey = 'your_secret_key';

    /**
     * Mengautentikasi pengguna berdasarkan username dan password.
     *
     * @param string $username Username yang akan diotentikasi.
     * @param string $password Kata sandi.
     * @return bool True jika autentikasi berhasil, false sebaliknya.
     */
    public function loginByUsername($username, $password)
    {
        $stmt = $this->pdo->getConnection()->prepare("SELECT * FROM {$this->table} WHERE {$this->username} = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();
        if ($user && Helpers::passwordVerify($password, $user['password'])) {
            $this->generateTokenUsername($user['id'], $user['username']);
            $this->id = $user['id'];
            $this->username = $user['username'];
            $this->email = $user['email'];
            $this->password = $user['password'];
            if ($user['secret_key'] != null) {
                $this->secretKey = $user['secret_key'];
            }
            return true;
        }

        return false;
    }

    /**
     * Mengautentikasi pengguna berdasarkan email dan password.
     *
     * @param string $email Email pengguna.
     * @param string $password Kata sandi.
     * @return bool True jika autentikasi berhasil, false sebaliknya.
     */
    public function loginByEmail($email, $password)
    {
        $stmt = $this->pdo->getConnection()->prepare("SELECT * FROM {$this->table} WHERE {$this->email} = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        if ($user && Helpers::passwordVerify($password, $user['password'])) {
            $this->generateTokenEmail($user['id'], $user['email']);
            $this->id = $user['id'];
            $this->username = $user['username'];
            $this->email = $user['email'];
            $this->password = $user['password'];
            if ($user['secret_key'] != null) {
                $this->secretKey = $user['secret_key'];
            }
            return true;
        }
        return false;
    }

    /**
     * Menghasilkan token JWT baru untuk pengguna berdasarkan username dan menyimpannya di basis data.
     *
     * @param mixed $id ID pengguna.
     * @param string $username Username.
     * @return string Token JWT yang dihasilkan.
     */
    public function generateTokenUsername($id, $username)
    {
        $exp = time() + (60 * 60); // Token expiration time (1 hour)
        // Create a JSON Web Token (JWT)
        $header = [
            'typ' => 'JWT',
            'alg' => 'HS256'
        ];
        $payload = [
            'id' => $id,
            'username' => $username,
            'exp' => $exp
        ];
        $base64UrlHeader = base64_encode(json_encode($header));
        $base64UrlPayload = base64_encode(json_encode($payload));
        $signature = hash_hmac('sha256', "$base64UrlHeader.$base64UrlPayload", $this->secretKey, true);
        $base64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));
        $token = "$base64UrlHeader.$base64UrlPayload.$base64UrlSignature";
        $this->token = $token;
        $this->tokenExpired = $exp;
        $this->update(
            $this->id,
            [
                'token' => $this->token,
                'token_expired' => date('Y-m-d H:i:s', $this->tokenExpired)
            ]
        );
        return $this->token;
    }

    /**
     * Menghasilkan token JWT baru untuk pengguna berdasarkan email dan menyimpannya di basis data.
     *
     * @param mixed $id ID pengguna.
     * @param string $email Email.
     * @return string Token JWT yang dihasilkan.
     */
    public function generateTokenEmail($id, $email)
    {
        $exp = time() + (60 * 60); // Token expiration time (1 hour)
        // Create a JSON Web Token (JWT)
        $header = [
            'typ' => 'JWT',
            'alg' => 'HS256'
        ];
        $payload = [
            'id' => $id,
            'email' => $email,
            'exp' => $exp
        ];
        $base64UrlHeader = base64_encode(json_encode($header));
        $base64UrlPayload = base64_encode(json_encode($payload));
        $signature = hash_hmac('sha256', "$base64UrlHeader.$base64UrlPayload", $this->secretKey, true);
        $base64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));
        $token = "$base64UrlHeader.$base64UrlPayload.$base64UrlSignature";
        $this->token = $token;
        $this->tokenExpired = $exp;
        $this->update(
            $this->id,
            [
                'token' => $this->token,
                'token_expired' => date('Y-m-d H:i:s', $this->tokenExpired)
            ]
        );
        return $this->token;
    }

    /**
     * Memvalidasi token JWT berdasarkan signature dan waktu kadaluarsa (username payload).
     *
     * @param string $token Token JWT yang akan divalidasi.
     * @return array|false Payload token jika valid, false sebaliknya.
     */
    public function validateTokenUsername($token)
    {
        $tokenParts = explode('.', $token);
        $tokenHeader = isset($tokenParts[0]) ? $tokenParts[0] : '';
        $tokenPaylod = isset($tokenParts[1]) ? $tokenParts[1] : '';
        $tokenSignature = isset($tokenParts[2]) ? $tokenParts[2] : '';
        $header = json_decode(base64_decode($tokenHeader), true);
        $payload = json_decode(base64_decode($tokenPaylod), true);
        $signature = $tokenSignature;

        $base64UrlHeader = base64_encode(json_encode($header));
        $base64UrlPayload = base64_encode(json_encode($payload));
        $validSignature = hash_hmac('sha256', "$base64UrlHeader.$base64UrlPayload", $this->secretKey, true);
        $base64UrlValidSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($validSignature));

        if ($signature === $base64UrlValidSignature && $payload['exp'] >= time()) {
            $this->token = $token;
            return $payload;
        } else {
            return false;
        }
    }

    /**
     * Memvalidasi token JWT berdasarkan signature dan waktu kadaluarsa (email payload).
     *
     * @param string $token Token JWT yang akan divalidasi.
     * @return array|false Payload token jika valid, false sebaliknya.
     */
    public function validateTokenEmail($token)
    {
        $tokenParts = explode('.', $token);
        $tokenHeader = isset($tokenParts[0]) ? $tokenParts[0] : '';
        $tokenPaylod = isset($tokenParts[1]) ? $tokenParts[1] : '';
        $tokenSignature = isset($tokenParts[2]) ? $tokenParts[2] : '';
        $header = json_decode(base64_decode($tokenHeader), true);
        $payload = json_decode(base64_decode($tokenPaylod), true);
        $signature = $tokenSignature;

        $base64UrlHeader = base64_encode(json_encode($header));
        $base64UrlPayload = base64_encode(json_encode($payload));
        $validSignature = hash_hmac('sha256', "$base64UrlHeader.$base64UrlPayload", $this->secretKey, true);
        $base64UrlValidSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($validSignature));

        if ($signature === $base64UrlValidSignature && $payload['exp'] >= time()) {
            $this->token = $token;
            return $payload;
        } else {
            return false;
        }
    }
}
