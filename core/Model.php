<?php


namespace core;

use PDO;


abstract class Model
{
    abstract static public function tableName(): string;

    // column names used for creation of a table row
    abstract public function tableColumns(): array;

    static function bindValues(object $statement, array $arr)
    {
        $dataTypes = [
            'string' => PDO::PARAM_STR,
            'integer' => PDO::PARAM_INT,
            'boolean' => PDO::PARAM_BOOL,
            'NULL' => PDO::PARAM_NULL
        ];

        $num = 1;  // placeholder's number in the sql query
        foreach ($arr as $values) {
            foreach ($values as $key => $value) {
                $statement->bindValue($num, $value, $dataTypes[gettype($value)]);
                $num++;
            }
        }
    }

    public function create()
    {
        $tableName = static::tableName();

        $columns = [];
        $placeholders = [];
        $params = [];

        foreach ($this->tableColumns() as $column) {
            $columns[] = $column;
            $placeholders[] = ":$column";
            $params[":$column"] = $this->$column;
        }

        $sql = "INSERT INTO $tableName ("
                . implode(',', $columns)
                . ") VALUES("
                . implode(',', $placeholders)
                . ")";

        $statement = Application::$app->db->pdo->prepare($sql);

        return $statement->execute($params);
    }

    static public function get(string $column, string $value)
    {
        $tableName = static::tableName();

        $sql = "SELECT * FROM $tableName WHERE $column = :$column";

        $statement = Application::$app->db->pdo->prepare($sql);
        $statement->bindValue(":$column", $value);
        $statement->execute();
        $statement->setFetchMode(PDO::FETCH_CLASS, get_called_class());

        return $statement->fetch();
    }

    static public function all()
    {
        $tableName = static::tableName();

        $sql = "SELECT * FROM $tableName";

        $statement = Application::$app->db->pdo->prepare($sql);
        $statement->execute();
        $statement->setFetchMode(PDO::FETCH_CLASS, get_called_class());

        return $statement->fetchAll();
    }

    // todo validate updated input
    static public function update(array $arr) {
        $tableName = static::tableName();

        $setClause = [];
        $whereClause = [];

        foreach ($arr['set'] as $key => $value) {
            $setClause[] = "$key=?";
        }

        foreach ($arr['where'] as $key => $value) {
            $whereClause[] = "$key=?";
        }

        $sql = "UPDATE $tableName SET "
            . implode(',', $setClause)
            . " WHERE "
            . implode(',', $whereClause);

        $statement = Application::$app->db->pdo->prepare($sql);
        self::bindValues($statement, $arr);

        return $statement->execute();
    }

    static public function delete(string $column, string $value)
    {
        $tableName = static::tableName();

        $sql = "DELETE FROM $tableName WHERE $column = :$column";

        $statement = Application::$app->db->pdo->prepare($sql);
        $statement->bindValue(":$column", $value);
        $statement->execute();

        return $statement->execute();
    }
}

// todo change fetchObject to class instance