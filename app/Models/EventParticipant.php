<?php

namespace App\Models;

use Core\Database\ActiveRecord\BelongsTo;
use Lib\Validations;
use Core\Database\ActiveRecord\Model;

/**
 * @property int $id
 * @property int $event_id
 * @property int $participant_id
 * @property string $registration_date
 * @property string $participation_status
 * @property string $certificate_description
 * @property bool $certificate_generated
 * @property string $certificate_generation_date
 * @property string $certificate_code
 */
class EventParticipant extends Model
{
    protected static string $table = 'event_participant';
    protected static array $columns = [
        'event_id',
        'participant_id',
        'registration_date',
        'participation_status',
        'certificate_description',
        'certificate_generated',
        'certificate_generation_date',
        'certificate_code'
    ];

    public function participant(): BelongsTo
    {
        return $this->belongsTo(User::class, 'participant_id');
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class, 'event_id');
    }

    public function validates(): void
    {
        Validations::uniqueness(['event_id', 'participant_id'], $this);
    }
}
