<?php
namespace MyProject\Models\Articles;

use MyProject\Exceptions\InvalidArgumentException;
use MyProject\Models\ActiveRecordEntity;
use MyProject\Models\Users\User;
use MyProject\Services\Db;

class Article extends ActiveRecordEntity {

    /** @var string */
    protected $name;

    /** @var string */
    protected $text;

    /** @var int */
    protected $authorId;

    /** @var string */
    protected $createdAt;

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getText(): string
    {
        return $this->text;
    }

    /**
     * @return int
     */
    public function getAuthorId(): int
    {
        return $this->authorId;
    }

    /**
     * @return User
     */
    public function getAuthor(): User
    {
        return User::getById($this->authorId);
    }

    /**
     * @return string
     */
    public function getCreatedAt(): string
    {
        return $this->createdAt;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @param string $text
     */
    public function setText(string $text): void
    {
        $this->text = $text;
    }

    /**
     * @param int $authorId
     */
    public function setAuthor(User $author): void
    {
        $this->authorId = $author->getId();
    }

    public function delete(int $id): void{
        $sql = 'DELETE FROM `' . static::getTableName() .'` WHERE id=:id';
        $db = Db::getInstance();
        $db->query($sql,[':id' => $id]);
        $sql = "DELETE FROM `comments` WHERE `comments`.`article_id`=:article_id";
        $db->query($sql,[':article_id' => $id]);
        $this->id = null;
    }

    public static function createFromArray(array $fields, User $author): Article
    {
        if(empty($fields['name'])){
            throw new InvalidArgumentException('Не переданно название статьи.');
        }

        if(empty($fields['text'])){
            throw new InvalidArgumentException('Не передан текст статьи');
        }

        $article = new Article();

        $article->setName($fields['name']);
        $article->setText($fields['text']);
        $article->setAuthor($author);

        $article->save();

        return $article;
    }

    public function updateFromArray(array $fields): Article
    {
        if(empty($fields['name'])){
            throw new InvalidArgumentException('Не указано название статьи');
        }

        if(empty($fields['text'])){
            throw new InvalidArgumentException('Не указан текст статьи');
        }

        $this->setName($fields['name']);
        $this->setText($fields['text']);

        $this->save();

        return $this;
    }

    public static function findAllShortReferences(): array
    {
        $sql = 'SELECT id, name, LEFT(text, 100) as text, author_id, created_at FROM ' . Article::getTableName();
        $db = Db::getInstance(); // create connection to database
        $result = $db->query($sql, [], Article::class); // sends sql query to the database and returns the query result
        return $result;
    }

    public static function findArticlesByUserId($userId): ?array
    {
        return Article::findAllByColumns(['author_id' => $userId]);
    }

    public function getParsedText(): string
    {
        $parser = new \Parsedown();
        return $parser->text($this->getText());
    }

    protected static function getTableName(): string
    {
        return 'articles';
    }
}