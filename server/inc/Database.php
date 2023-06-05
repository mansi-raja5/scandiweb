<?php

namespace Inc;

use Inc\Config;

class Database
{
    protected $connection = null;
    public function __construct()
    {
        try {
            $this->connection = new \mysqli(
                Config::DB_HOST,
                Config::DB_USERNAME,
                Config::DB_PASSWORD,
                Config::DB_DATABASE_NAME
            );

            if (mysqli_connect_errno()) {
                throw new \Exception("Could not connect to database.");
            }
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * Summary of getConnection
     * @return \mysqli
     */
    public function getConnection()
    {
        return $this->connection;
    }
    public function select($query = "", $params = [])
    {
        try {
            $stmt = $this->executeStatement($query, $params);
            $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
            $stmt->close();
            return $result;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function query($query = "", $params = [])
    {
        try {
            $stmt = $this->executeStatement($query, $params);
            return $this->getConnection()->affected_rows;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function insert($table, $data)
    {
        $columns = implode(", ", array_keys($data));
        $values = "'" . implode("', '", array_values($data)) . "'";
        $query = "INSERT INTO $table ($columns) VALUES ($values)";

        if ($this->connection->query($query) === true) {
            return $this->connection->insert_id;
        } else {
            throw new \Exception("Error: " . $query . "<br>" . $this->connection->error);
        }
    }

    public function update($table, $data, $condition)
    {
        $set = '';
        foreach ($data as $key => $value) {
            $set .= "$key = '$value', ";
        }
        $set = rtrim($set, ", ");
        $query = "UPDATE $table SET $set WHERE $condition";

        if ($this->connection->query($query) === true) {
            return true;
        } else {
            throw new \Exception("Error: " . $query . "<br>" . $this->connection->error);
        }
    }

    public function delete($table, $condition)
    {
        $query = "DELETE FROM $table WHERE $condition";

        if ($this->connection->query($query) === true) {
            return true;
        } else {
            throw new \Exception("Error: " . $query . "<br>" . $this->connection->error);
        }
    }

    /**
     * Execute a prepared statement with parameters
     * @param string $sql The SQL query string
     * @param array $params The array of parameters to bind to the prepared statement
     * @return bool True on success, false on failure
     */
    private function executeStatement($query = "", $params = [])
    {
        try {
            $stmt = $this->connection->prepare($query);
            if ($stmt === false) {
                throw new \Exception("Unable to do prepared statement: " . $query);
            }
            if (!empty($params)) {
                $types = '';
                $values = [];
                foreach ($params as $param) {
                    $types .= $this->getBindType($param);
                    $values[] = $param;
                }
                $stmt->bind_param($types, ...$values);
            }
            $stmt->execute();
            return $stmt;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * Get the bind type based on the parameter value
     * @param mixed $param The parameter value
     * @return string The bind type character
     */
    private function getBindType($param)
    {
        if (is_int($param)) {
            return 'i'; // Integer
        } elseif (is_float($param)) {
            return 'd'; // Double
        } elseif (is_string($param)) {
            return 's'; // String
        } else {
            return 'b'; // Blob or unknown
        }
    }

    //method to filter input and output
    public function escapeValue($value)
    {
        $value = $this->getConnection()->real_escape_string($value);
        return $value;
    }

    //close sql connection
    public function closeConnection()
    {
        $this->connection->close();
    }
}
