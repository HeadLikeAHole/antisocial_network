<?php


namespace app\models;

use core\FormModel;


class Post extends FormModel
{
    public static function tableName(): string
    {
        return 'post';
    }

    public function tableColumns(): array
    {
        return ['user_id', 'file', 'title', 'text'];
    }

    public function formInputs(): array
    {
        return ['user_id', 'file', 'title', 'text'];
    }

    public function validationRules(): array
    {
        return [
            'user_id' => [self::REQUIRED],
            'file' => [self::REQUIRED],
            'title' => [self::REQUIRED, [self::MAX_LENGTH => 250]],
            'text' => [self::REQUIRED, [self::MAX_LENGTH => 2000]]
        ];
    }
}
