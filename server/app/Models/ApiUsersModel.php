<?php

namespace App\Models;

use Inc\Database;

class ApiUsersModel extends Database
{
    private $table = 'api_users';
    private $authKey;

    public function __construct()
    {
        parent::__construct();
    }
    //function to verify user authKey
    public function verifyAuthKey()
    {
        $this->setAuthKey(trim(htmlspecialchars(strip_tags($this->getAuthKey()))));
        $columns = 'auth_key';
        $where = 'auth_key = :auth_key';
        $params = [':auth_key' => $this->getAuthKey()];
        $apiUserInfo = $this->select($this->table, $columns, $where, $params);

        if (empty($apiUserInfo)) {
            return false;
        }
        return true;
    }

    public function getAuthKey()
    {
        return $this->authKey;
    }

    public function setAuthKey($authKey): self
    {
        $this->authKey = $authKey;
        return $this;
    }
}
