<?php

namespace App\Models;

use Database\Factories\GuestUserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

/**
 * App\Models\GuestUser
 *
 * @property int $id
 * @property string $name
 * @property string $date_of_birth
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @method static Builder|GuestUser newModelQuery()
 * @method static Builder|GuestUser newQuery()
 * @method static Builder|GuestUser query()
 * @method static GuestUserFactory factory(...$parameters)
 * @method static create(array $validated)
 * @method static orderBy(string $sortField, array|string|null $sortOrder)
 */
class GuestUser extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'date_of_birth',
    ];
}
