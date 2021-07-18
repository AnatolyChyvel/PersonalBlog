<?php

namespace MyProject\Controllers;

use MyProject\Exceptions\ForbiddenException;
use MyProject\Exceptions\InvalidArgumentException;
use MyProject\Exceptions\NotFoundException;
use MyProject\Exceptions\UnauthorizedException;
use MyProject\Models\Articles\Article;
use MyProject\Models\Comments\Comment;
use MyProject\Models\Profiles\Profile;

class CommentsController extends AbstractController
{
    public function add(int $articleId)
    {
        $article = Article::getById($articleId);

        if ($article === null){
            throw new NotFoundException('Статья не найдена.');
        }

        if($this->user === null){
            throw new UnauthorizedException('Для добавления комментариев, необходимо авторизоваться.');
        }

        if(!empty($_POST)){
            try{
                $comment = Comment::create($_POST, $articleId, $this->user);
            } catch (InvalidArgumentException $e) {
                $this->view->renderHtml('articles/view.php',
                    [
                        'error' => $e->getMessage(),
                        'article' => $article
                    ]
                );
                return;
            }
            $profile = Profile::getProfileByUserId($this->user->getId());
            $profile->updateLastActivity();
            header('Location: /articles/' . $articleId . '#comment' . $comment->getId());
            exit();
        }

        $this->view->renderHtml('articles/view.php', ['article' => $article]);
    }

    public function edit(int $commentId)
    {
        $comment = Comment::getById($commentId);

        if (empty($comment)){
            throw new NotFoundException('Комментарий не найден.');
        }

        if($this->user === null){
            throw new ForbiddenException('Необходимо авторизоваться.');
        }
        if(!$this->user->isAdmin() && $this->user->getId() !== $comment->getAuthor()->getId()){
            throw new ForbiddenException('Нет доступа к данной функции.');
        }

        if(!empty($_POST)){
            try{
                $comment->updateFromArray($_POST);
            } catch (InvalidArgumentException $e){
                $this->view->renderHtml('comments/edit.php', ['error' => $e->getMessage(), 'comment' => $comment]);
                return;
            }

            $profile = Profile::getProfileByUserId($this->user->getId());
            $profile->updateLastActivity();

            header('Location: /articles/' . $comment->getArticleId() . '#comment' . $comment->getId());
            exit();
        }

        $this->view->renderHtml('comments/edit.php', ['comment' => $comment]);
    }

    public function delete(int $commentId)
    {
        $comment = Comment::getById($commentId);

        if ($comment === null){
            throw new NotFoundException();
        }

        if(!isset($this->user)){
            throw new UnauthorizedException();
        }

        if(!$this->user->isAdmin() && $this->user->getId() !== $comment->getAuthor()->getId()){
            throw new ForbiddenException();
        }

        $articleId = $comment->getArticleId();

        $comment->delete($commentId);
        
        $profile = Profile::getProfileByUserId($this->user->getId());
            $profile->updateLastActivity();

        header('Location: /articles/' . $articleId);
    }
}