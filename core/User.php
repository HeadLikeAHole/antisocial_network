<?php


namespace core;


class User extends FormModel
{
    public static function tableName(): string
    {
        return 'user';
    }

    public function tableColumns(): array
    {
        return ['username', 'email', 'password_hash', 'activation_token_hash'];
    }

    public function formInputs(): array
    {
        return ['username', 'email', 'password'];
    }

    public function validationRules(): array
    {
        return [
            'username' => [self::REQUIRED, [self::UNIQUE, 'username'], [self::MIN_LENGTH, 3], [self::MAX_LENGTH, 150]],
            'email' => [self::REQUIRED, [self::UNIQUE, 'email'], [self::MAX_LENGTH, 150]],
            'password' => [self::REQUIRED, self::PASSWORD, [self::MIN_LENGTH, 8]]
        ];
    }

    public function createUser()
    {
        $this->password_hash = password_hash($this->password, PASSWORD_DEFAULT);

        $token = new Token();
        $this->activation_token = $token->getToken();
        $this->activation_token_hash = $token->getHashedToken();

        return $this->create();
    }

    // activate account by setting "is_active" to true
    public static function activate($token)
    {
        $token = new Token($token);
        $activation_token_hash = $token->getHashedToken();

        return self::update([
            'set' => ['activation_token_hash' => null, 'is_active' => true],
            'where' => ['activation_token_hash' => $activation_token_hash]
        ]);
    }

    public function authenticate()
    {
        $user = $this->get('username', $this->username);
        if (!$user || !$user->is_active) {
            $this->addError('username', 'User doesn\'t exists');
            return false;
        }
        if (!password_verify($this->password, $user->password_hash)) {
            $this->addError('password', 'Password is incorrect');
            return false;
        }
        return $user;
    }

    public static function createPasswordResetToken($email)
    {
        $user = self::get('email', $email);

        if ($user) {
            $token = new Token();
            $user->token = $token->getToken();
            $password_reset_hash = $token->getHashedToken();
            $password_reset_expiration = date('Y-m-d H:i:s', time() + 60 * 60 * 2);  // 2 hours from now
            self::update([
                'set' => [
                    'password_reset_hash' => $password_reset_hash,
                    'password_reset_expiration' => $password_reset_expiration
                ],
                'where' => ['id' => $user->id]
            ]);
            return $user;
        }

        return false;
    }
}
