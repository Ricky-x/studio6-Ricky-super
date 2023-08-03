<?php
// Namespace: purpose to solve the problem of duplicate names

// load base class
include_once 'Base.php';

// Login class
class LoginAdmin extends \Base
{

    // Login
    public function login()
    {
        // Receive parameter email passwd
        $username = $this->REQUEST['username'] ?? '';
        $passwd = $this->REQUEST['password'] ?? '';

        // The parameter is empty prompt error message
        if ($username == '' || $passwd == '') {
            return sendSuccess('password account not written', [], 400);
        }

        if ($username == 'admin' && $passwd == '123123') {
            return sendSuccess('login successful', ['id' => 1, 'name' => $username]);
        }
        return sendSuccess('password account not written', [], 400);
    }

    /**
     * Obtain user list keyword search
     * @return false|string
     */
    public function seacher()
    {
        // Receive keyword arguments
        $word = $_GET['word'] ?? '';


        $whereW = '';  // where search criteria: keyword search
        $orderStr = ' order by id desc'; // sqlsort
        // $word != ''，Add search criteria.
        if ($word != '') {
            $whereW = "name like '%{$word}%'";
        }

        if ($whereW != '') {
            // Only keyword search and price sorting
            $sqlStr = "select * from users where {$whereW} {$orderStr}";
        } else {
            // Get all items sorted by price
            $sqlStr = "select * from users {$orderStr}";
        }

        $page = $_REQUEST['page'] ?? 1;
        $limit = $_REQUEST['limit'] ?? 10;
        $m = ($page - 1) * $limit;

        $listDATA = $this->DB->getAll($sqlStr . ' limit ' . $m . ',' . $limit);
        $count = $this->DB->getAll($sqlStr);

        // return product data
        return json_encode(['code' => 0, 'msg' => '', 'count' => count($count), 'data' => $listDATA]);
    }


    /**
     * del user
     * @return false|string
     */
    public function del()
    {
        $id = $this->REQUEST['id'] ?? '';
        $a = $this->DB->query("delete from users  where id = {$id}");
        return sendSuccess('success');
    }

    // Obtain information about a certain user
    public function getOne()
    {
        $id = $_REQUEST['id'] ?? 1;
        $data1 = $this->DB->getOne('select * from users where id = ' . $id);
        return sendSuccess('success', ['data' => $data1]);
    }

    public function update()
    {
        $id = $_REQUEST['id'] ?? '';
        $a = $this->DB->query("update users set  
            name =  '{$this->REQUEST['name']}',
            password =  '{$this->REQUEST['password'] }',
            phone = {$this->REQUEST['phone'] },
            email = '{$this->REQUEST['email'] }' where id = {$id}");
        return sendSuccess('success');
    }
}


// Get the action parameter. Determine which method is called.
$action = $_REQUEST['action'] ?? '';
// Parameter does not exist Output error message
if ($action == '') {
    echo sendSuccess('Can not be empty', [], 400);
    die;
}
// Instantiate the shopping cart class
$login = new LoginAdmin();
// Call the corresponding method in the shopping cart class
echo $login->$action();
