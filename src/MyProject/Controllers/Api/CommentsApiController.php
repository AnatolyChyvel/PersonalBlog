<?php
namespace MyProject\Controllers\Api;

use MyProject\Controllers\AbstractController;
use MyProject\Exceptions\NotFoundException;
use MyProject\Models\Articles\Article;
use MyProject\Models\Comments\Comment;
use MyProject\Models\Users\User;
use MyProject\Models\Profiles\Profile;

class CommentsApiController extends AbstractController
{
	public function view(int $articleId){
		$comments = Comment::findAllCommentsForArticle($articleId);
		if($comments === null){
			throw new NotFoundException();
		}

		$countComments = $_POST['countComments'];
		if($countComments == count($comments)){
				$this->view->sendJson([
				'comments' => null,
			], 204);
			return;
		}

		$dataOfComments = [];
		foreach ($comments as $id => $comment) {
			$author = $comment->getAuthor();

			$dataOfComments[$id]['image'] = $comment->getAuthorProfile()->getSquareImage();
			$dataOfComments[$id]['user_id'] = $comment->getUserId();
			$dataOfComments[$id]['user_nickname'] = $author->getNickname();
			$dataOfComments[$id]['created_at'] = $comment->getCreatedAt();
			$dataOfComments[$id]['text'] = $comment->getText();
			$dataOfComments[$id]['id'] = $comment->getId();

			$dataOfComments[$id]['isAdminOrOwner'] = false;
			if(isset($this->user))
				$dataOfComments[$id]['isAdminOrOwner'] = $this->user->isAdmin() || $this->user->getId() === $author->getId();
		}

		$this->view->sendJson([
			'comments' => [$dataOfComments]
		]);
	}
}