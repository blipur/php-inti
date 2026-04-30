<?php

declare(strict_types=1);

namespace Imadepurnamayasa\PhpInti\Authentication;

use Imadepurnamayasa\PhpInti\Database\ORM;

/**
 * Class BasePermission
 * Kelas abstrak dasar untuk entitas izin (permission) pengguna di basis data.
 */
abstract class BasePermission extends ORM
{
    /** @var string Nama tabel basis data untuk izin. */
    protected $table = 'permissions';

    /** @var string Nama primary key tabel izin. */
    protected $primaryKey = 'id';
}