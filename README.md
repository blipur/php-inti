# php-inti

PHP Package untuk memudahkan membuat aplikasi. Kumpulan utilitas untuk mempercepat pengembangan aplikasi PHP.

## Instalasi

Anda dapat menginstal package ini melalui Composer:

```bash
composer require imadepurnamayasa/php-inti
```

## Penggunaan

Berikut adalah beberapa contoh penggunaan class yang tersedia di dalam package ini.

### 1. Terbilang

Mengonversi angka menjadi teks (terbilang) dalam bahasa Indonesia.

```php
use Imadepurnamayasa\PhpInti\Terbilang;

$terbilang = new Terbilang();
echo $terbilang->konversi(1234567);
// Output: Satu Juta Dua Ratus Tiga Puluh Empat Ribu Lima Ratus Enam Puluh Tujuh
```

### 2. TanggalJam

Memanipulasi dan menghitung selisih waktu atau tanggal dengan mudah.

```php
use Imadepurnamayasa\PhpInti\TanggalJam;

$waktu = new TanggalJam();
echo "Sekarang: " . $waktu->sekarang() . "\n";

$waktu->tambahHari(5);
echo "5 hari kemudian: " . $waktu->sekarang() . "\n";

$selisih = $waktu->selisihHari('2024-01-01 00:00:00');
echo "Selisih hari: " . $selisih . " hari\n";
```

## Lisensi

[MIT License](LICENSE)
