<?php

namespace App\Models;

use Lib\Validations;
use Core\Database\ActiveRecord\Model;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $encrypted_password
 * @property string $avatar_name
 */
class User extends Model
{
    protected static string $table = 'users';
    protected static array $columns = ['created_at', 'updated_at', 'name', 'email', 'encrypted_password', 'phone', 'cpf', 'is_active', 'is_admin'];

    protected ?string $password = null;
    protected ?string $password_confirmation = null;

    public function validates(): void
    {
        Validations::notEmpty('name', $this);
        Validations::notEmpty('email', $this);

        Validations::uniqueness('email', $this);

        if ($this->newRecord()) {
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
            $this->newRecord() &&
            $value !== null && $value !== ''
        ) {
            $this->encrypted_password = password_hash($value, PASSWORD_DEFAULT);
        }
    }

    public static function create(array $data): User
    {
        $user = new self();
        $user->created_at = date('Y-m-d H:i:s');
        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->encrypted_password = password_hash($data['encrypted_password'], PASSWORD_DEFAULT);
        $user->is_admin = $data['is_admin'] ?? false;
        $user->is_active = $data['is_active'] ?? true;
        $user->phone = $data['phone'] ?? null;
        $user->cpf = $data['cpf'] ?? null;

        $user->save();
        return $user;
    }
}
