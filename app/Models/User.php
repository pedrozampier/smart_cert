<?php

namespace App\Models;

use Lib\Validations;
use Core\Database\ActiveRecord\Model;
use Core\Database\ActiveRecord\BelongsToMany;
use Core\Database\ActiveRecord\HasMany;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $encrypted_password
 * @property string|null $avatar_name
 * @property string|null $phone
 * @property string $cpf
 * @property bool $is_active
 * @property bool $is_admin
 * @property string $created_at
 * @property string $updated_at
 * @property Event[] $created_events
 * @property Event[] $participating_events
 */
class User extends Model
{
    protected static string $table = 'users';
    protected static array $columns = [
        'created_at',
        'updated_at',
        'name',
        'email',
        'encrypted_password',
        'phone',
        'cpf',
        'is_active',
        'is_admin'
    ];

    protected ?string $password = null;
    protected ?string $password_confirmation = null;

    public function createdEvents(): HasMany
    {
        return $this->hasMany(Event::class, 'creator_id');
    }

    public function participatingEvents(): BelongsToMany
    {
        return $this->belongsToMany(Event::class, 'event_participant', 'participant_id', 'event_id');
    }

    public function validates(): void
    {
        Validations::notEmpty('name', $this);
        Validations::notEmpty('email', $this);
        Validations::notEmpty('cpf', $this);

        Validations::emailFormat('email', $this);
        Validations::cpfFormat('cpf', $this);
        Validations::phoneFormat('phone', $this);

        Validations::uniqueness('email', $this);

        if ($this->newRecord() || $this->password !== null) {
            Validations::passwordConfirmation($this);
        }
    }

    public function authenticate(string $password): bool
    {
        if ($this->encrypted_password == null) {
            return false;
        }

        return password_verify($password, $this->encrypted_password);
    }

    public static function findByEmail(string $email): User | null
    {
        return User::findBy(['email' => $email]);
    }

    public function __set(string $property, mixed $value): void
    {
        parent::__set($property, $value);

        if (
            $property === 'password' &&
            $value !== null && $value !== ''
        ) {
            $this->encrypted_password = password_hash($value, PASSWORD_DEFAULT);
        }
    }
}
