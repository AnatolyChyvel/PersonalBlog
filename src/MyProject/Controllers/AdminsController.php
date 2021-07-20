<?php

namespace MyProject\Controllers;

use MyProject\Exceptions\ForbiddenException;
use MyProject\Exceptions\InvalidArgumentException;
use MyProject\Exceptions\UnauthorizedException;
use MyProject\Models\Articles\Article;
use MyProject\Models\Comments\Comment;
use MyProject\Models\Users\User;
use MyProject\Models\Users\UsersAuthService;

class AdminsController extends AbstractController
{

    public function viewNewComments()
    {
        if(empty($this->user)){
            throw new UnauthorizedException();
        }

        if(!$this->user->isAdmin()){
            throw new ForbiddenException('Только для администраторов');
        }

        $comments = Comment::findAll();
        $comments = $this->sort($comments, 'DESC');
        $this->view->renderHtml('admins/comments.php', ['comments' => $comments]);
    }
}