<?php
// Namespace: purpose to solve the problem of duplicate names
namespace action;
// Load the base class
include_once 'Base.php';

// Product Details
class Detail extends \Base
{
    // Get details about an item
    public function getOneDetail()
    {
        // get product id
        $id = $_GET['id'] ?? 0;
        $id = intval($id);
        // The parameter value is correct when the product id is greater than 0
        if ($id > 0) {
            // Associated query Obtain product information based on product id
            $oneDetail = $this->DB->getOne("select p.*,pp.*,p.id as id,p.price as price from products as p left join products_price as pp on pp.product_id = p.id
            where p.id =  {$id}");
            // Product Existence Returns the data of the product
            if ($oneDetail) {
                return sendSuccess('get success', $oneDetail);
            }
        }
        // Product does not exist Prompt error message
        return sendSuccess('Product does not exist', [], 400);

    }

}

// Get the action parameter. Determine which method is called.
$action = $_REQUEST['action'] ?? '';
// Parameter does not exist Output error message
if ($action == '') {
    echo sendSuccess('Can not be empty', [], 400);
    die;
}
//Instantiate the shopping cart class
$login  = new Detail();
// Call the corresponding method in the shopping cart class
echo $login->$action();