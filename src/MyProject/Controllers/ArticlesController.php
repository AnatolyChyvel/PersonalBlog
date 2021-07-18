<?php

namespace MyProject\Controllers;

use MyProject\Exceptions\ForbiddenException;
use MyProject\Exceptions\InvalidArgumentException;
use MyProject\Exceptions\NotFoundException;
use MyProject\Exceptions\UnauthorizedException;
use MyProject\Models\Articles\Article;
use MyProject\Models\Comments\Comment;
use MyProject\Models\Users\User;
use MyProject\Models\Profiles\Profile;
use MyProject\Models\Users\UsersAuthService;
use MyProject\View\View;

class ArticlesController extends AbstractController
{
    public function view(int $articleId)
    {
        $article = Article::getById($articleId);

        if ($article === null){
            throw new NotFoundException('Статьи не существует.');
            return;
        }
        $comments = Comment::findAllCommentsForArticle($articleId);

        $this->view->renderHtml('articles/view.php', [
            'article' => $article,
            'comments' => $comments
        ]);
    }

    public function edit(int $articlesId)
    {
        $article = Article::getById($articlesId);

        if ($article === null){
            throw new NotFoundException('Статья не найдена');
        }

        if($this->user === null){
            throw new UnauthorizedException();
        }

        if (!$this->user->isAdmin()){
            throw new ForbiddenException('Для редактирования статьи необходимо обладать правами администратора.');
        }

        if(!empty($_POST)){
            try{
                $article->updateFromArray($_POST);
            } catch (InvalidArgumentException $e){
                $this->view->renderHtml('articles/edit.php', ['error' => $e->getMessage(), 'article' => $article]);
                return;
            }

            $profile = Profile::getProfileByUserId($this->user->getId());
            $profile->updateLastActivity();

            header('Location: /articles/' . $article->getId());
            exit();
        }
        $this->view->renderHtml('articles/edit.php', ['article' => $article]);
    }

    public function add(): void
    {
        if($this->user === null){
            throw new UnauthorizedException();
        }

        if(!$this->user->isAdmin()){
            throw new ForbiddenException('Необходимо быть администратором.');
        }

        if(!empty($_POST)){
            try{
                $article = Article::createFromArray($_POST, $this->user);
            } catch (InvalidArgumentException $e) {
                $this->view->renderHtml('articles/add.php', ['error' => $e->getMessage()]);
                return;
            }

            $profile = Profile::getProfileByUserId($this->user->getId());
            $profile->updateLastActivity();

            header('Location: /articles/' . $article->getId(), true, 302);
            exit();
        }

        $this->view->renderHtml('articles/add.php');
    }

    public function delete(int $articlesId)
    {
        $article = Article::getById($articlesId);

        if ($article === null){
            throw new NotFoundException();
        }

        if(!isset($this->user)){
            throw new UnauthorizedException();
        }

        if(!$this->user->isAdmin()){
            throw new ForbiddenException();
        }

        $article->delete($articlesId);

        $profile = Profile::getProfileByUserId($this->user->getId());
            $profile->updateLastActivity();

        header('Location: /');
        exit();
    }
}