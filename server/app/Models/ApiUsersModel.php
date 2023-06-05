<?php

namespace App\Models;

use Inc\Bcrypt;
use Inc\Database;

session_start();
class ApiUsersModel extends Database
{

    private $table = 'api_users';

    //Apiuser properties
    private $apiuser_id;
    private $firstname;
    private $lastname;
    private $email;
    private $password;
    private $authKey;
    private $apiuser_status;

    /**
     * Summary of __construct
     */
    public function __construct()
    {
        parent::__construct();
    }

    //check unique email
    public function check_email()
    {
        global $database;
        $this->email = trim(htmlspecialchars(strip_tags($this->email)));
        $sql = "SELECT apiuser_id FROM " . $this->table . " WHERE
                email = '" . $database->escapeValue($this->email) . "' ";

        $result = $database->query($sql);
        $info = $database->fetch_row($result);

        if (empty($info)) {
            return true;
        }
        return false;
    }

    //create ApiUser
    public function create_ApiUser()
    {
        //clean data
        $this->firstname = trim(htmlspecialchars(strip_tags($this->firstname)));
        $this->lastname = trim(htmlspecialchars(strip_tags($this->lastname)));
        $this->email = trim(htmlspecialchars(strip_tags($this->email)));
        $this->password = trim(htmlspecialchars(strip_tags($this->password)));

        //hash the password => return hashed password
        $hashed_password = Bcrypt::hashPassword($this->password);

        //create user Auth_key
        $normal_key = substr(md5(mt_rand()), 0, 7);

        //hash the key
        $auth_key = Bcrypt::hashPassword($normal_key);

        global $database;

        $sql = "INSERT INTO $this->table (firstname, lastname, email, password, auth_key)
                VALUES ('" . $database->escapeValue($this->firstname) . "',
                        '" . $database->escapeValue($this->lastname) . "',
                        '" . $database->escapeValue($this->email) . "',
                        '" . $database->escapeValue($hashed_password) . "',
                        '" . $database->escapeValue($auth_key) . "')";

        $user_saved = $database->query($sql);

        if ($user_saved) {
            die('Registration Successful Login here!');
        } else {
            die('Cannot save the user try again later...!');
        }
    }

    // public function ApiUserDetails
    public function get_ApiUserDetails()
    {

        global $database;

        $this->apiuser_id = intval($this->apiuser_id);

        $sql = "SELECT apiuser_id, firstname, lastname, email, auth_key FROM " . $this->table . "
                WHERE apiuser_id = '$this->apiuser_id'";

        $result = $database->query($sql);
        return $database->fetch_row($result);
    }

    // function to check users credentials
    public function check_user_credentials()
    {

        $this->email = trim(htmlspecialchars(strip_tags($this->email)));

        global $database;

        $sql = "SELECT apiuser_id, firstname, lastname, email, password FROM " . $this->table . "
        WHERE email = '" . $database->escapeValue($this->email) . "'";

        $result = $database->query($sql);
        $userInfo = $database->fetch_row($result);

        if (!empty($userInfo)) {
            //match the password
            $hashedPassword = $userInfo['password'];

            $password = trim(htmlspecialchars(strip_tags($this->password)));

            //match password using bcrypt
            $matchPassword = Bcrypt::checkPassword($password, $hashedPassword);

            if ($matchPassword) {
                return $userInfo;
            } else {
                return false;
            }
        }
    }

    //function to verify user authKey
    public function verifyAuthKey()
    {
        $this->setAuthKey(trim(htmlspecialchars(strip_tags($this->getAuthKey()))));

        $ApiUserInfo = $this->select("SELECT apiuser_id, firstname, lastname, email, auth_key FROM " . $this->table . " WHERE auth_key = '" . $this->escapeValue($this->getAuthKey()) . "'");

        if (empty($ApiUserInfo)) {
            return false;
        }
        return true;
    }

    /**
     * @return mixed
     */
    public function getAuthKey()
    {
        return $this->authKey;
    }

    /**
     * @param mixed $authKey
     * @return self
     */
    public function setAuthKey($authKey): self
    {
        $this->authKey = $authKey;
        return $this;
    }
}
