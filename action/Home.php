<?php
// Namespace: purpose to solve the problem of duplicate names
namespace action;
// load base class
include_once 'Base.php';

// Home display class
class Home extends \Base
{
    // Homepage Recommended Products
    public function Boutique()
    {

        // Find the data whose hot field is equal to 1 in the products table
        $productsArr = $this->DB->getAll('select * from products where hot = 1');
        // Back Boutique Data
        return sendSuccess('get success', $productsArr);
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
$login  = new Home();
// Call the corresponding method in the shopping cart class
echo $login->$action();