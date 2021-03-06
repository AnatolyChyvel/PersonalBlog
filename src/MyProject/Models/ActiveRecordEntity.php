<?php

namespace MyProject\Models;

use MyProject\Services\Db;

abstract class ActiveRecordEntity implements \JsonSerializable
{
    /** @var int */
    protected $id;

    abstract protected static function getTableName(): string;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    public function __set($name, $value)
    {
        $camelCaseName = $this->underscoreToCamelCase($name);
        $this->$camelCaseName = $value;
    }

    private function underscoreToCamelCase(string $source): string
    {
        return lcfirst(str_replace('_', '', ucwords($source, '_')));
    }

    /** @return static[] */
    public static function findAll(): array
    {
        $db = Db::getInstance(); // create connection to database
        return $db->query('SELECT * FROM `' . static::getTableName() . '`;', [], static::class); // sends sql query to the database and returns the query result
    }

    public static function getById(int $id): ?self
    {
        $db = Db::getInstance();
        $entities = $db->query(
            'SELECT * FROM `' . static::getTableName() . '` WHERE id=:id',
            [':id' => $id],
            static::class
        );
        return $entities ? $entities[0] : null;
    }

    public function save(): void
    {
        $mappedProperties = $this->mapPropertiesToDbFormat();
        if ($this->id !== null){
            $this->update($mappedProperties);
        }else{
            $this->insert($mappedProperties);
        }
    }

    private function insert(array $mappedProperties): void
    {
        $mappedPropertiesNotNull = array_filter($mappedProperties);
        $columns = [];
        $paramsNames = [];
        $params2value = [];
        foreach ($mappedPropertiesNotNull as $columnName => $value){
            $columns[] = '`' . $columnName . '`';
            $paramName = ':' . $columnName;
            $paramsNames[] = $paramName;
            $params2value[$paramName] = $value;
        }

        $columnsViaSemicolon = implode(', ', $columns);
        $paramsNamesViaSemicolon = implode(', ', $paramsNames);
        $sql = 'INSERT INTO ' . static::getTableName() . ' (' . $columnsViaSemicolon . ')' . ' VALUES (' . $paramsNamesViaSemicolon . ')';
        $db = Db::getInstance();
        $db->query($sql, $params2value, static::class);
        $this->id = $db->getLastInsertId();
        $this->refresh();
    }

    private function update(array $mappedProperties): void
    {
        $columns2params = [];
        $params2value = [];
        $index = 1;
        foreach ($mappedProperties as $column => $value){
            $param = ':param' . $index;
            $columns2params[] = $column . ' = ' . $param;
            $params2value[$param] = $value;
            $index++;
        }

        $sql = 'UPDATE ' . static::getTableName() . ' SET ' . implode(', ', $columns2params) . ' WHERE id=' . $this->id;
        $db = Db::getInstance();
        $db->query($sql,$params2value,static::class);
        $this->refresh();
    }

    public function delete(int $id): void{
        $sql = 'DELETE FROM `' . static::getTableName() .'` WHERE id=:id';
        $db = Db::getInstance();
        $db->query($sql,[':id' => $id]);
        $this->id = null;
    }

    public static function findOneByColumn(string $columnName, $value): ?self
    {
        $db = Db::getInstance();
        $result = $db->query(
            'SELECT * FROM ' . static::getTableName() . ' WHERE ' . $columnName . '=:value LIMIT 1;',
            [':value' => $value],
            static::class
        );
        if($result === []){
            return null;
        }

        return $result[0];
    }

    public function jsonSerialize()
    {
        return $this->mapPropertiesToDbFormat();
    }

    protected static function findAllByColumns(array $columns2values): array
    {
        $columns2params = [];
        $params2value = [];
        foreach ($columns2values as $column => $value) {
            $paramName = ':' . $column;
            $params2value[$paramName] = $value;
            $columns2params[] = $column . ' = ' . $paramName;
        }
        $sql = 'SELECT * FROM ' . static::getTableName() . ' WHERE ' . implode(', ', $columns2params);

        $db = Db::getInstance();
        $result = $db->query($sql, $params2value, static::class);
        return $result;
    }

    private function refresh(): void
    {
        $objectFromDb = static::getById($this->id);
        $reflector = new \ReflectionObject($objectFromDb);
        $properties = $reflector->getProperties();

        foreach ($properties as $property) {
            $property->setAccessible(true);
            $propertyName = $property->getName();
            $this->$propertyName = $property->getValue($objectFromDb);
        }
    }

    private function mapPropertiesToDbFormat(): array
    {
        $reflector = new \ReflectionObject($this);
        $properties = $reflector->getProperties();

        $mappedProperties = [];
        foreach ($properties as $property) {
            $propertyName = $property->getName();
            $propertyNameAsUnderscore = $this->camelCaseToUnderscore($propertyName);
            $mappedProperties[$propertyNameAsUnderscore] = $this->$propertyName;
        }

        return $mappedProperties;
    }

    private function camelCaseToUnderscore(string $source): string
    {
        return strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $source));
    }
}