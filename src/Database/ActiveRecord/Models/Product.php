<?php

declare(strict_types=1);

namespace Imadepurnamayasa\PhpInti\Database\ActiveRecord\Models;

use Imadepurnamayasa\PhpInti\Database\ActiveRecord\ActiveRecord;

/**
 * Class Product
 * Example Active Record model representing a Product.
 * 
 * @property int|string $id
 * @property string $name
 * @property float $price
 * @property int $stock
 */
class Product extends ActiveRecord
{
    /** @var string The table associated with the model. */
    protected string $table = 'products';
}
