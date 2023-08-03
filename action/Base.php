<?php

// Loading bulletin method
include_once 'Common.php';
// Load the database link class
include_once 'OPDBS.php';

/***
 * basic class
 * 1. Link database
 * 2. Determine whether to login
 */
class Base
{


    //Storage database resource pool
    public $DB = null;
    //store request parameters
    public $REQUEST;

    /**
     *1. Link to the database
     * 2. Receive request parameters
     */
    public function __construct()
    {
        $this->DB = new \OPDBS();
        $this->REQUEST = json_decode(file_get_contents( "php://input"),1);
    }


    /**
     * Determine whether a member is logged in
     * Determine whether to log in by whether the received parameter user_id exists in the database clock
     * @return false|mixed|string
     */
    function isLogin()
    {
        // 1. Receive parameter user_id
        if($_SERVER['REQUEST_METHOD'] == "POST"){
            $userId = $this->REQUEST['user_id'] ?? 0;
        } else {
            $userId = $_GET['user_id'] ?? 0;
        }

        // 2. Find member data from table users by userid
        return $this->DB->getOne('select * from users where id = ' . $userId);
    }
}