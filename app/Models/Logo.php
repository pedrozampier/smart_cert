<?php

namespace App\Models;

use Lib\Validations;
use Core\Database\ActiveRecord\Model;
use Core\Database\ActiveRecord\BelongsTo;
use Core\Database\ActiveRecord\HasMany;

/**
 * @property int $id
 * @property string $name
 * @property string $original_name
 * @property string $file_path
 * @property int $file_size
 * @property string $file_extension
 * @property int $width
 * @property int $height
 * @property string $uploaded_at
 * @property int $user_id
 * @property bool $is_active
 * @property User $user
 * @property Event[] $events
 */
class Logo extends Model
{
    protected static string $table = 'logos';
    protected static array $columns = [
        'name',
        'original_name',
        'file_path',
        'file_size',
        'file_extension',
        'width',
        'height',
        'uploaded_at',
        'user_id',
        'is_active'
    ];

    public function validates(): void
    {
        Validations::notEmpty('name', $this);
        Validations::notEmpty('original_name', $this);
        Validations::notEmpty('file_path', $this);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function events(): HasMany
    {
        return $this->hasMany(Event::class, 'logo_id');
    }
}
