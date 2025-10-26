<?php

namespace Tests\Unit\Models;

use App\Models\User;
use Tests\TestCase;

class UserTest extends TestCase
{
    // 3.1 - Testes de métodos do model User

    public function test_authenticate_with_correct_password(): void
    {
        $user = new User([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => '123456',
            'password_confirmation' => '123456',
            'cpf' => '12345678901'
        ]);
        $user->save();

        $this->assertTrue($user->authenticate('123456'));
    }

    public function test_authenticate_with_incorrect_password(): void
    {
        $user = new User([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => '123456',
            'password_confirmation' => '123456',
            'cpf' => '12345678901'
        ]);
        $user->save();

        $this->assertFalse($user->authenticate('wrong_password'));
    }

    public function test_find_by_email(): void
    {
        $user = new User([
            'name' => 'Find By Email',
            'email' => 'findemail@example.com',
            'password' => '123456',
            'password_confirmation' => '123456',
            'cpf' => '12345678901'
        ]);
        $user->save();

        $foundUser = User::findByEmail('findemail@example.com');

        $this->assertNotNull($foundUser);
        $this->assertEquals('Find By Email', $foundUser->name);
    }

    public function test_password_is_hashed(): void
    {
        $user = new User([
            'name' => 'Password Hash Test',
            'email' => 'hash@example.com',
            'password' => '123456',
            'password_confirmation' => '123456',
            'cpf' => '12345678901'
        ]);
        $user->save();

        $this->assertNotEquals('123456', $user->encrypted_password);
        $this->assertTrue(password_verify('123456', $user->encrypted_password));
    }

    // Validações

    public function test_validates_name_not_empty(): void
    {
        $user = new User([
            'email' => 'test@example.com',
            'password' => '123456',
            'password_confirmation' => '123456',
            'cpf' => '12345678901'
        ]);

        $this->assertFalse($user->save());
    }

    public function test_validates_email_not_empty(): void
    {
        $user = new User([
            'name' => 'Test User',
            'password' => '123456',
            'password_confirmation' => '123456',
            'cpf' => '12345678901'
        ]);

        $this->assertFalse($user->save());
    }

    public function test_validates_cpf_not_empty(): void
    {
        $user = new User([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => '123456',
            'password_confirmation' => '123456'
        ]);

        $this->assertFalse($user->save());
    }

    public function test_validates_email_format(): void
    {
        $user = new User([
            'name' => 'Test User',
            'email' => 'invalid_email',
            'password' => '123456',
            'password_confirmation' => '123456',
            'cpf' => '12345678901'
        ]);

        $this->assertFalse($user->save());
    }

    public function test_validates_cpf_format(): void
    {
        $user = new User([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => '123456',
            'password_confirmation' => '123456',
            'cpf' => '123'
        ]);

        $this->assertFalse($user->save());
    }

    public function test_validates_phone_format(): void
    {
        $user = new User([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => '123456',
            'password_confirmation' => '123456',
            'cpf' => '12345678901',
            'phone' => '123'
        ]);

        $this->assertFalse($user->save());
    }

    public function test_validates_email_uniqueness(): void
    {
        $user1 = new User([
            'name' => 'User 1',
            'email' => 'duplicate@example.com',
            'password' => '123456',
            'password_confirmation' => '123456',
            'cpf' => '12345678901'
        ]);
        $user1->save();

        $user2 = new User([
            'name' => 'User 2',
            'email' => 'duplicate@example.com',
            'password' => '123456',
            'password_confirmation' => '123456',
            'cpf' => '12345678902'
        ]);

        $this->assertFalse($user2->save());
    }

    public function test_validates_password_confirmation(): void
    {
        $user = new User([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => '123456',
            'password_confirmation' => 'different',
            'cpf' => '12345678901'
        ]);

        $this->assertFalse($user->save());
    }
}
