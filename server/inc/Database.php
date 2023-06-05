<?php

namespace Inc;

class Database
{
    protected $connection = null;
    public function __construct()
    {
        try {
            $this->connection = new \mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_DATABASE_NAME);

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
            $stmt->close();
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


    private function executeStatement($query = "", $params = [])
    {
        try {
            $stmt = $this->connection->prepare($query);
            if ($stmt === false) {
                throw new \Exception("Unable to do prepared statement: " . $query);
            }
            if ($params) {
                $stmt->bind_param($params[0], $params[1]);
            }
            $stmt->execute();
            return $stmt;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
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
