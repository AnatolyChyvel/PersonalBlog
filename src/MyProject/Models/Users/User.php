<?php

namespace MyProject\Models\Users;

use MyProject\Exceptions\InvalidArgumentException;
use MyProject\Models\ActiveRecordEntity;
use MyProject\Models\Profiles\Profile;

class User extends ActiveRecordEntity{
    /** @var string */
    protected $nickname;

    /** @var string */
    protected $email;

    /** @var int */
    protected $isConfirmed;

    /** @var string */
    protected $role;

    /** @var string */
    protected $passwordHash;

    /** @var string */
    protected $authToken;

    /** @var string */
    protected $createdAt;

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @return string
     */
    public function getNickname(): string
    {
        return $this->nickname;
    }

    /**
     * @return int
     */
    public function isConfirmed(): int
    {
        return $this->isConfirmed;
    }

    /**
     * @return string
     */
    public function getPasswordHash(): string
    {
        return $this->passwordHash;
    }

    /**
     * @return string
     */
    public function getAuthToken(): string
    {
        return $this->authToken;
    }

    /** @return string */
    public function getRole(): string
    {
        return $this->role;
    }

    public function isAdmin(): bool
    {
        if($this->role === 'admin'){
            return true;
        }
        return false;
    }

    protected static function getTableName(): string
    {
        return 'users';
    }

    public static function signUp(array $userData): User
    {
        if(empty($userData['nickname'])){
            throw new InvalidArgumentException('Не передан nickname');
        }

        if(!preg_match('/^[a-zA-Z0-9]+$/', $userData['nickname'])){
            throw new InvalidArgumentException('nickname должен состоять только из букв латинского алфавита и цифр');
        }

        if (static::findOneByColumn('nickname', $userData['nickname']) !== null){
            throw new InvalidArgumentException('Пользователь с таким ником уже существует');
        }
        if(empty($userData['email'])){
            throw new InvalidArgumentException('Не передан email');
        }

        if(!filter_var($userData['email'],FILTER_VALIDATE_EMAIL)){
            throw new InvalidArgumentException('email некорректен');
        }

        if(static::findOneByColumn('email', $userData['email']) !== null){
            throw new InvalidArgumentException('Данный email-адрес уже занят');
        }

            if(empty($userData['password'])){
            throw new InvalidArgumentException('Не передан password');
        }

        if(mb_strlen($userData['password']) < 8) {
            throw new InvalidArgumentException('Пароль должен быть более 8-ми символов');
        }

        $user = new User();
        $user->nickname = $userData['nickname'];
        $user->email = $userData['email'];
        $user->passwordHash = password_hash($userData['password'], PASSWORD_DEFAULT);
        $user->isConfirmed = false; // email address not verified
        $user->role = 'user';
        $user->authToken = sha1(random_bytes(100)) . sha1(random_bytes(100));
        $user->save();

        return $user;
    }

    public function activate(): void
    {
        $this->isConfirmed = true; // email address verified
        $this->save();
    }

    public static function login(array $loginData): User{
        if(empty($loginData['email'])){
            throw new InvalidArgumentException('Не передан email.');
        }

        if(empty($loginData['password'])){
            throw new InvalidArgumentException('Не передан пароль.');
        }

        $user = User::findOneByColumn('email', $loginData['email']);
        if($user === null){
            throw new InvalidArgumentException('Пользователь с указанным email адресом не найден.');
        }

        if(!password_verify($loginData['password'], $user->getPasswordHash())){
            throw new InvalidArgumentException('Неверный пароль.');
        }

        if(!$user->isConfirmed()){
            throw new InvalidArgumentException('Пользователь не прошел активацию');
        }

        $user->refreshAuthToken();
        $user->save();

        return $user;
    }

    private function refreshAuthToken()
    {
        $this->authToken = sha1(random_bytes(100)) . sha1(random_bytes(100));
    }
}