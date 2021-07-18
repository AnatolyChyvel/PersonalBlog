<?php


namespace MyProject\Services;

use MyProject\Exceptions\DbException;

class Db
{
    /** @var \PDO */
    private $pdo;

    /** @var self */
    private static $instance;

    private function __construct()
    {
        $dbOptions = $_ENV;

        try {
            $this->pdo = new \PDO(
                'mysql:host=' . $dbOptions['DB_HOST'] . ';dbname=' . $dbOptions['DB_NAME'],
                $dbOptions['DB_USER'],
                $dbOptions['DB_PASSWORD']
            );
            $this->pdo->exec('SET NAMES UTF8');
        } catch (\PDOException $e) {
            throw new DbException('Ошибка при подключении к базе данных: ' . $e->getMessage());
        }
    }

    public static function getInstance(): self
    {
        if (self::$instance === null)
        {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function query(string $sql, array $params = [], string $className = 'stdClass'): ?array
    {
        $sth = $this->pdo->prepare($sql);
        $result = $sth->execute($params);

        if (false === $result)
        {
            return null;
        }

        return $sth->fetchAll(\PDO::FETCH_CLASS, $className);
    }

    public function getLastInsertId(): int
    {
        return (int) $this->pdo->lastInsertId();
    }
}