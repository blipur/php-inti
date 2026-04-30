<?php

declare(strict_types=1);

namespace Imadepurnamayasa\PhpInti\Authentication;

use Imadepurnamayasa\PhpInti\Database\ORM;

/**
 * Class BaseRole
 * Kelas abstrak dasar untuk entitas peran (role) pengguna di basis data.
 */
abstract class BaseRole extends ORM
{
    /** @var string Nama tabel basis data untuk peran. */
    protected $table = 'roles';

    /** @var string Nama primary key tabel peran. */
    protected $primaryKey = 'id';
}