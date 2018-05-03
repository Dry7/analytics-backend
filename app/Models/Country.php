<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Country
 * @package App\Models
 *
 * @param int $id
 * @param string $title
 *
 * @param Carbon $created_at
 * @param Carbon $updated_at
 */
class Country extends Model
{
    protected $fillable = ['id', 'title'];
}
