<?php

declare(strict_types=1);

namespace Imadepurnamayasa\PhpInti;

/**
 * Class Helpers
 * Kumpulan fungsi bantuan (helpers) untuk mempermudah operasi umum.
 */
class Helpers
{
    /**
     * Menampilkan isi variabel menggunakan fungsi print_r di dalam tag <pre>.
     *
     * @param mixed $content Data yang akan ditampilkan.
     * @return void
     */
    public static function print_r($content)
    {
        echo '<pre>';
        print_r($content);
        echo '</pre>';
    }

    /**
     * Menampilkan struktur dan tipe data variabel menggunakan fungsi var_dump di dalam tag <pre>.
     *
     * @param mixed $content Data yang akan ditampilkan.
     * @return void
     */
    public static function var_dump($content)
    {
        echo '<pre>';
        var_dump($content);
        echo '</pre>';
    }

    /**
     * Menghasilkan hash kata sandi menggunakan algoritma bcrypt default PHP.
     *
     * @param string $data Kata sandi mentah (plaintext).
     * @return string String hash hasil pengacakan.
     */
    public static function passwordHashDefault(string $data): string
    {
        return password_hash($data, PASSWORD_DEFAULT);
    }

    /**
     * Memverifikasi apakah sebuah kata sandi mentah cocok dengan hash-nya.
     *
     * @param string $password Kata sandi mentah.
     * @param string $hash Hash kata sandi yang tersimpan.
     * @return bool True jika cocok, false jika tidak.
     */
    public static function passwordVerify($password, $hash)
    {
        if (password_verify($password, $hash)) {
            return true;
        } else {
            return false;
        }
    }
}
