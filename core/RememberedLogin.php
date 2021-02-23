<?php


namespace core;


class RememberedLogin extends Model
{
    public int $user_id;
    public string $remembered_token;
    public string $token_hash;
    public string $expires;
    public string $expiration_date;

    public static function tableName(): string
    {
        return 'remembered_login';
    }

    public function tableColumns(): array
    {
        return ['token_hash', 'user_id', 'expiration_date'];
    }

    public function rememberLogin($user_id)
    {
        $this->user_id = $user_id;
        $token = new Token();
        $this->remembered_token = $token->getToken();
        $this->token_hash = $token->getHashedToken();

        // cookie expiration time (30 days from now)
        $this->expires = time() + 60 * 60 * 24 * 30;
        // cookie expiration time converted to necessary date format to be saved to database
        $this->expiration_date = date('Y-m-d H:i:s', $this->expires);

        return $this->create();
    }

    public static function getRememberedLogin($token)
    {
        $token = new Token($token);
        $token_hash = $token->getHashedToken();
        return self::get('token_hash', $token_hash);
    }

    public static function forgetLogin($token)
    {
        $token = new Token($token);
        $token_hash = $token->getHashedToken();
        return self::delete('token_hash', $token_hash);
    }

    public function hasExpired()
    {
        return strtotime($this->expiration_date) < time();
    }
}