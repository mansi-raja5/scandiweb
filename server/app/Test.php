<?php
namespace App;

class Test{
    
    function mansi(){
        /*header('Access-Control-Allow-Origin: *');
        header('X-Test-Header: Test');
        header('Content-type: application/json');
        header('Access-Control-Allow-Methods: GET');
        header('Access-Control-Allow-Headers: Origin, Content-type, Auth_Key, Accept');    */
        
        
        header('Access-Control-Allow-Origin: *'); 
    header("Access-Control-Allow-Credentials: true");
    header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
    header('Access-Control-Max-Age: 1000');
    header('Access-Control-Allow-Headers: Origin, Content-Type, Auth_key , Authorization');
    
        echo json_encode(array('message' => 'Hello World'));    
    }

}

?>