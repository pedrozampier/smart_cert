<?php

namespace App\Models;

use Lib\Validations;
use Core\Database\ActiveRecord\Model;
use Core\Database\ActiveRecord\BelongsTo;

/**
 * @property int $id
 * @property string $name
 * @property string $description
 * @property string $start_date
 * @property string $end_date
 * @property string $event_location
 * @property int $workload_hours
 * @property string $event_type
 * @property int $logo_id
 * @property int $creator_id
 * @property int $owner_id
 * @property string $created_at
 * @property string $updated_at
 * @property bool $is_active
 * @property Logo $logo
 * @property User $creator
 * @property User $owner
 */
class Event extends Model
{
    protected static string $table = 'events';
    protected static array $columns = [
        'name',
        'description',
        'start_date',
        'end_date',
        'event_location',
        'workload_hours',
        'event_type',
        'logo_id',
        'creator_id',
        'owner_id',
        'created_at',
        'updated_at',
        'is_active'
    ];

    public function validates(): void
    {
        Validations::notEmpty('name', $this);
        Validations::notEmpty('start_date', $this);
        Validations::notEmpty('creator_id', $this);
    }

    public function logo(): BelongsTo
    {
        return $this->belongsTo(Logo::class, 'logo_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }
}
