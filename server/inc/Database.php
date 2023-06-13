<?php

namespace Inc;

use Inc\Config;

class Database
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = new \PDO('mysql:host=localhost;dbname=PMS', 'root', '');
    }
    public function save($tableName, $data)
    {

        $columns = array_keys($data);
        $placeholders = array_map(function ($column) {
            return ':' . $column;
        }, $columns);

        $query = 'INSERT INTO ' . $tableName . ' (' . implode(', ', $columns) . ') VALUES (' . implode(', ', $placeholders) . ')';

        try {
            $stmt = $this->pdo->prepare($query);
            $stmt->execute($data);

            return $this->pdo->lastInsertId();
        } catch (\PDOException $e) {
            return $e->getMessage();
        }
    }

    public function select($tableName, $columns = '*', $where = '', $params = [])
    {
        $query = 'SELECT ' . $columns . ' FROM ' . $tableName;
        if (!empty($where)) {
            $query .= ' WHERE ' . $where;
        }

        try {
            $stmt = $this->pdo->prepare($query);
            $stmt->execute($params);
            //echo $statement->queryString;
            // $stmt->debugDumpParams();
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            return $e->getMessage();
        }
    }

    public function delete($tableName, $where = '', $params = [])
    {
        $query = 'DELETE FROM ' . $tableName;
        if (!empty($where)) {
            $query .= ' WHERE ' . $where;
        }

        try {
            $placeholders = implode(',', array_fill(0, count($params), '?'));
            $query = "DELETE FROM $tableName WHERE $where ($placeholders)";
            $stmt = $this->pdo->prepare($query);
            $stmt->execute($params);
            return $stmt->rowCount();
        } catch (\PDOException $e) {
            return 'Error: ' . $e->getMessage();
        }
    }
}
