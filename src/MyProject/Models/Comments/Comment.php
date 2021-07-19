<?php

namespace MyProject\Models\Comments;

use MyProject\Exceptions\InvalidArgumentException;
use MyProject\Models\ActiveRecordEntity;
use MyProject\Models\Profiles\Profile;
use MyProject\Models\Users\User;

class Comment extends ActiveRecordEntity
{
    protected $userId;

    protected $articleId;

    protected $text;

    protected $created_at;

    public function setUserId(int $userId): void
    {
        $this->userId = $userId;
    }

    public function setArticleId(int $articleId): void
    {
        $this->articleId = $articleId;
    }

    public function setText(string $text): void
    {
        $this->text = $text;
    }

    public function getText(): string
    {
        return htmlentities($this->text);
    }

    public function getArticleId()
    {
        return $this->articleId;
    }
    public function getAuthor(): User
    {
        return User::getById($this->userId);
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getAuthorProfile(): Profile
    {
        return Profile::getProfileByUserId($this->userId);
    }

    public function getCreatedAt(): string
    {
        return $this->created_at;
    }

    public static function create(array $fields, $articleId, User $user): Comment{
        if (empty($fields['comment'])){
            throw new InvalidArgumentException('Не заполнен текст комментария.');
        }

        $comment = new Comment();

        $comment->setUserId($user->getId());
        $comment->setArticleId($articleId);
        $comment->setText($fields['comment']);
        $comment->save();

        return $comment;
    }

    public function updateFromArray(array $source): Comment
    {
        if(empty($source['comment'])){
            throw new InvalidArgumentException('Не заполнен текст комментария.');
        }
        $this->setText($source['comment']);

        $this->save();

        return $this;
    }

    public static function findAllCommentsForArticle(int $articleId): array
    {
        return Comment::findAllByColumns(['article_id' => $articleId]);
    }

    protected static function getTableName(): string{
        return 'comments';
    }
}