<?php

namespace MyProject\Controllers\Api;

use MyProject\Controllers\AbstractController;
use MyProject\Exceptions\NotFoundException;
use MyProject\Models\Articles\Article;

class ArticlesApiController extends AbstractController
{
    public function view()
    {
        $articles = Article::findAllShortReferences();

        if ($articles === null) {
            throw new NotFoundException();
        }

        $countArticles = $_POST['countArticles'];
        if($countArticles == count($articles)){
                $this->view->sendJson([
                'articles' => null,
            ], 204);
            return;
        }

        $this->view->sendJson([
            'articles' => [$articles]
        ]);
    }
}