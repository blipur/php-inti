<?php

declare(strict_types=1);

namespace Imadepurnamayasa\PhpInti;

/**
 * Class TanggalJam
 * Utilitas untuk memanipulasi waktu dan menghitung selisih (interval) antar tanggal dan jam.
 */
class TanggalJam
{
    /**
     * @var \DateTime Instance objek DateTime.
     */
    private $tanggalJam;

    /**
     * Constructor untuk inisialisasi waktu.
     *
     * @param string $tanggalJamString String format tanggal/waktu (default: 'now').
     */
    public function __construct($tanggalJamString = 'now')
    {
        $this->tanggalJam = new \DateTime($tanggalJamString);
    }

    /**
     * Mendapatkan tanggal dan jam saat ini dari instance dalam format 'Y-m-d H:i:s'.
     *
     * @return string Format tanggal dan jam.
     */
    public function sekarang()
    {
        return $this->tanggalJam->format('Y-m-d H:i:s');
    }

    /**
     * Menambahkan atau mengurangi hari pada instance tanggal saat ini.
     *
     * @param int $hari Jumlah hari yang ditambahkan (positif) atau dikurangi (negatif).
     * @return void
     */
    public function tambahHari($hari)
    {
        $interval = new \DateInterval('P' . abs($hari) . 'D');
        if ($hari < 0) {
            $this->tanggalJam->sub($interval);
        } else {
            $this->tanggalJam->add($interval);
        }
    }

    /**
     * Menghitung selisih hari dengan tanggal lain.
     *
     * @param string $tanggalJamLain Tanggal pembanding.
     * @return string Selisih jumlah hari.
     */
    public function selisihHari($tanggalJamLain)
    {
        $tanggalJam = new \DateTime($tanggalJamLain);
        $interval = $this->tanggalJam->diff($tanggalJam);
        return $interval->format('%r%a');
    }

    /**
     * Menghitung selisih bulan dengan tanggal lain.
     *
     * @param string $tanggalJamLain Tanggal pembanding.
     * @return string Selisih jumlah bulan.
     */
    public function selisihBulan($tanggalJamLain)
    {
        $tanggalJam = new \DateTime($tanggalJamLain);
        $interval = $this->tanggalJam->diff($tanggalJam);
        return $interval->format('%r%m');
    }

    /**
     * Menghitung selisih tahun dengan tanggal lain.
     *
     * @param string $tanggalJamLain Tanggal pembanding.
     * @return string Selisih jumlah tahun.
     */
    public function selisihTahun($tanggalJamLain)
    {
        $tanggalJam = new \DateTime($tanggalJamLain);
        $interval = $this->tanggalJam->diff($tanggalJam);
        return $interval->format('%r%y');
    }

    /**
     * Menghitung selisih jam dengan tanggal/jam lain.
     *
     * @param string $tanggalJamLain Tanggal/jam pembanding.
     * @return string Selisih jumlah jam.
     */
    public function selisihJam($tanggalJamLain)
    {
        $tanggalJam = new \DateTime($tanggalJamLain);
        $interval = $this->tanggalJam->diff($tanggalJam);
        return $interval->format('%r%H');
    }

    /**
     * Menghitung selisih menit dengan tanggal/jam lain.
     *
     * @param string $tanggalJamLain Tanggal/jam pembanding.
     * @return string Selisih jumlah menit.
     */
    public function selisihMenit($tanggalJamLain)
    {
        $tanggalJam = new \DateTime($tanggalJamLain);
        $interval = $this->tanggalJam->diff($tanggalJam);
        return $interval->format('%r%i');
    }

    /**
     * Menghitung selisih detik dengan tanggal/jam lain.
     *
     * @param string $tanggalJamLain Tanggal/jam pembanding.
     * @return string Selisih jumlah detik.
     */
    public function selisihDetik($tanggalJamLain)
    {
        $tanggalJam = new \DateTime($tanggalJamLain);
        $interval = $this->tanggalJam->diff($tanggalJam);
        return $interval->format('%r%S');
    }
}
