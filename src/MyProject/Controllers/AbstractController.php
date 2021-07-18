<?php

namespace MyProject\Controllers;

use MyProject\Models\Users\User;
use MyProject\Models\Users\UsersAuthService;
use MyProject\View\View;

class AbstractController
{
    /** @var View */
    protected $view;

    /** @var User */
    protected $user;

    public function __construct()
    {
        $this->user = UsersAuthService::getUserByToken();
        $this->view = new View(__DIR__ . '/../../../templates');
        $this->view->setVar('user', $this->user); // passing the current user to the view
    }

    public function sort(array $arrayObjects, $sortParameter = 'ASC'): array
    {
        if ($sortParameter === 'ASC') {
            uasort($arrayObjects, function ($a, $b) {
                return $a->getId() - $b->getId();
            });
        } else {
            uasort($arrayObjects, function ($a, $b) {
                return $b->getId() - $a->getId();
            });
        }
        return $arrayObjects;
    }
}