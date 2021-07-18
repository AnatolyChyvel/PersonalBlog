<?php

namespace MyProject\Controllers;

use MyProject\Exceptions\ForbiddenException;
use MyProject\Exceptions\NotFoundException;
use MyProject\Exceptions\UserActivationException;
use MyProject\Exceptions\InvalidArgumentException;
use MyProject\Models\Articles\Article;
use MyProject\Models\Profiles\Profile;
use MyProject\Models\Users\UsersAuthService;
use MyProject\Services\EmailSender;
use MyProject\View\View;
use MyProject\Models\Users\User;
use MyProject\Models\Users\UserActivationService;

class UsersController extends AbstractController
{
    public function signUp()
    {
        if (!empty($_POST)) {
            try {
                $user = User::signUp($_POST);
            } catch (InvalidArgumentException $e) {
                $this->view->renderHtml('users/signUp.php', ['error' => $e->getMessage()]);
                return;
            }

            if ($user instanceof User) {
                $code = UserActivationService::createActivationCode($user);

                EmailSender::send($user, 'Активация', 'userActivation.php', [
                    'userId' => $user->getId(),
                    'code' => $code
                ]);

                Profile::create($user);

                $this->view->renderHtml('users/signUpSuccessful.php');
                return;
            }
        }
        $this->view->renderHtml('users/signUp.php');
    }

    public function activate(int $userId, string $activationCode)
    {
        try {
            $user = User::getById($userId);

            if ($user === null) {
                throw new UserActivationException('Данный пользователь не найден в базе данныхю');
            }

            if ($user->isConfirmed()) {
                throw new UserActivationException('Пользователь уже актирован.');
            }

            $isCodeValid = UserActivationService::checkActivationCode($user, $activationCode);

            if (!$isCodeValid) {
                throw new UserActivationException('Неверный код активации.');
            }

            $user->activate();
            UserActivationService::deleteActivationCode($userId);
            $this->view->renderHtml('users/activationSuccessful.php');
        } catch (UserActivationException $e) {
            $this->view->renderHtml('users/activationError.php', ['error' => $e->getMessage()]);
        }
    }

    public function login()
    {
        if (!empty($_POST)) {
            try {
                $user = User::login($_POST);
                UsersAuthService::createToken($user);

                $profile = Profile::getProfileByUserId($user->getId());
                $profile->updateLastActivity();

                header('Location: /');
                exit();
            } catch (InvalidArgumentException $e) {
                $this->view->renderHtml('users/login.php', ['error' => $e->getMessage()]);
                return;
            }
        }
        $this->view->renderHtml('users/login.php');
    }

    public function logout()
    {
        setcookie('token', '', 0, '/');
        header('Location: /');
    }

    public function viewProfile(int $userId)
    {
        $user = User::getById($userId);
        if (empty($user)) {
            throw new NotFoundException();
        }

        $profile = Profile::getProfileByUserId($userId);
        if (empty($profile)) {
            throw new NotFoundException();
        }

        $articles = Article::findArticlesByUserId($userId);
        if($articles === null){
            $articles = 'У данного пользователя нет статей.';
        }

        $this->view->renderHtml('profiles/view.php', [
            'profile' => $profile,
            'owner' => $user,
            'articles' => $articles
        ]);
    }

    public function editProfile(int $userId)
    {
        $profile = Profile::getProfileByUserId($userId);
        if (empty($profile)) {
            throw new NotFoundException();
        }

        if ($profile->getUserId() !== $this->user->getId()) { // whether the user is the owner of the profile
            throw new ForbiddenException();
        }

        if (!empty($_POST)) {
            try {
                $profile->updateFromArray($_POST);

                if (!empty($_FILES['image']['name'])) {
                    $profile->uploadImage($_FILES['image']);
                }

                header('Location: /users/' . $this->user->getId() . '/profile');
                exit();
            } catch (InvalidArgumentException $e) {
                $this->view->renderHtml('profiles/edit.php', [
                    'profile' => $profile,
                    'error' => $e->getMessage()
                ]);
                return;
            }
        }

        $this->view->renderHtml('profiles/edit.php', ['profile' => $profile]);
    }
}