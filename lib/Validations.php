<?php

namespace Lib;

use Core\Database\Database;

class Validations
{
    public static function notEmpty($attribute, $obj)
    {
        if ($obj->$attribute === null || $obj->$attribute === '') {
            $obj->addError($attribute, 'não pode ser vazio!');
            return false;
        }

        return true;
    }

    public static function passwordConfirmation($obj)
    {
        if ($obj->password !== $obj->password_confirmation) {
            $obj->addError('password', 'as senhas devem ser idênticas!');
            return false;
        }

        return true;
    }

    public static function uniqueness($fields, $object)
    {
        $dbFieldsValues = [];
        $objFieldValues = [];

        if (!is_array($fields)) {
            $fields = [$fields];
        }

        if (!$object->newRecord()) {
            $dbObject = $object::findById($object->id);

            foreach ($fields as $field) {
                $dbFieldsValues[] = $dbObject->$field;
                $objFieldValues[] = $object->$field;
            }

            if (
                !empty($dbFieldsValues) &&
                !empty($objFieldValues) &&
                $dbFieldsValues === $objFieldValues
            ) {
                return true;
            }
        }

        $table = $object::table();
        $conditions = implode(' AND ', array_map(fn($field) => "{$field} = :{$field}", $fields));

        $sql = <<<SQL
            SELECT id FROM {$table} WHERE {$conditions};
        SQL;

        $pdo = Database::getDatabaseConn();
        $stmt = $pdo->prepare($sql);

        foreach ($fields as $field) {
            $stmt->bindValue($field, $object->$field);
        }

        $stmt->execute();

        if ($stmt->rowCount() !== 0) {
            foreach ($fields as $field) {
                $object->addError($field, 'já existe um registro com esse dado');
            }
            return false;
        }

        return true;
    }

    public static function emailFormat($attribute, $obj)
    {
        $email = $obj->$attribute;

        if ($email === null || $email === '') {
            return true; // notEmpty já valida isso
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $obj->addError($attribute, 'deve ter um formato válido (exemplo@dominio.com)');
            return false;
        }

        return true;
    }

    public static function cpfFormat($attribute, $obj)
    {
        $cpf = $obj->$attribute;

        if ($cpf === null || $cpf === '') {
            return true; // notEmpty já valida isso
        }

        // Remove caracteres não numéricos
        $cpf = preg_replace('/[^0-9]/', '', $cpf);

        if (strlen($cpf) !== 11) {
            $obj->addError($attribute, 'deve conter exatamente 11 dígitos');
            return false;
        }

        // Verifica se todos os dígitos são iguais (CPF inválido)
        if (preg_match('/^(\d)\1{10}$/', $cpf)) {
            $obj->addError($attribute, 'não pode ter todos os dígitos iguais');
            return false;
        }

        return true;
    }

    public static function phoneFormat($attribute, $obj)
    {
        $phone = $obj->$attribute;

        if ($phone === null || $phone === '') {
            return true; // Telefone é opcional
        }

        // Remove caracteres não numéricos
        $phone = preg_replace('/[^0-9]/', '', $phone);

        // Aceita telefones com 10 ou 11 dígitos (fixo ou celular)
        if (strlen($phone) < 10 || strlen($phone) > 11) {
            $obj->addError($attribute, 'deve conter 10 ou 11 dígitos');
            return false;
        }

        return true;
    }
}
