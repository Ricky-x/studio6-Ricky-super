<?php
// Namespace: purpose to solve the problem of duplicate names
namespace action;
// Load the base class
include_once 'Base.php';

// shopping cart
class
Cart extends \Base
{

    /**
     * remove item from cart
     */
    public function delCart()
    {
        //1. Determine whether to log in
        $userData = $this->isLogin();
        //2. No login returns error message
        if (!$userData) {
            // sendSuccess
            // The first parameter: return error message
            // The second parameter: return data
            //  The third parameter: return status code. Successful status code: 200. Failure status code: 400
            return sendSuccess('please log in first', [], 400);
        }

        // Get the data id in the shopping cart table to be deleted
        $id = $this->REQUEST['id'];
        // Execute delete command
        $this->DB->query('delete from cart where id = ' . $id);

        // return prompt data
        return sendSuccess('successfully deleted');

    }

    /**
     * Shopping cart page, get all product data
     */
    public function getAll()
    {
        //1. Determine whether to log in
        $userData = $this->isLogin();
        if (!$userData) {
            return sendSuccess('please log in first', [], 400);
        }
        // Get all the data of a user in the shopping table
        $cartData = $this->DB->getAll('select * from cart where user_id = ' . $userData['id']);

        // Get all supermarkets
        $aa = $this->DB->getAll('select * from supermarkets');

        // Supermarket data regrouping
        $superData = [];
        foreach ($aa as $item) {
            $superData[$item['id']] = $item['name'];
        }

        // All the data of a user in the shopping table can be associated with the query through the product id to obtain the product price and product information.
        $data = [];
        foreach ($cartData as $datum) {
            $data[$datum['id']]['data'] = $this->DB->getAll('select * from products as p left join products_price as pp 
        on pp.product_id = p.id where p.id = ' . $datum['product_id']);
            $data[$datum['id']]['count'] = $datum['count'];
        }

        // In order to facilitate the display of the page, one product corresponds to the price of 3 supermarkets. to assemble the data
        $dataa = [];
        foreach ($data as $key => $datums) {
            foreach ($datums['data'] as $datumdd) {
                $dataa[$key]['name'] = $datumdd['name'];
                $dataa[$key]['cover'] = $datumdd['cover'];
                $dataa[$key]['remark'] = $datumdd['remark'];
                $dataa[$key]['name'] = $datumdd['name'];

                $dataa[$key]['supermarket'][] = [
                    'name' => $superData[$datumdd['supermarket_id']],
                    'price' => $datumdd['price'],
                    'count' => $datums['count'],
                ];
            }
        }
        // return prompt data
        return sendSuccess('success', $dataa);
    }

    /**
     * Item added to cart
     */
    public function addCart()
    {
        //1. Determine whether to log in
        $userData = $this->isLogin();
        if (!$userData) {
            return sendSuccess('please log in first', [], 400);
        }
        // get product id
        $productId = $this->REQUEST['id'] ?? 0;
        // Query the products table products_price table through the product id association to determine whether the product exists
        $productData = $this->DB->getOne('select * from products as p left join products_price as pp 
        on pp.product_id = p.id where p.id = ' . $productId);
        // Product does not exist Prompt error message
        if (empty($productData)) {
            return sendSuccess('Product does not exist', [], 400);
        }
        // The product exists, add the product id to the cart table
        $this->DB->insert('cart', ['order_no' => uniqid(), 'product_id' => $productId,
            'user_id' => $userData['id'], 'create_at' => date('Y-m-d H:i:s')]);

        // return prompt data
        return sendSuccess('success');
    }


    /**
     * Item Quantity +1 -1
     */
    public function countU()
    {
        //1. Determine whether to log in
        $userData = $this->isLogin();
        if (!$userData) {
            return sendSuccess('please log in first', [], 400);
        }
        // get cart id
        $cartId = $this->REQUEST['id'] ?? 0;
        // Get act value. 1 is the quantity of goods plus 1
        $act = $_REQUEST['act'] ?? 1;
        // Get the shopping cart information according to the shopping cart id
        $productData = $this->DB->getOne('select * from cart where id = ' . $cartId);

        // act value. 1 is the quantity of goods plus 1
        if ($act == 1) {
            $count = $productData['count'] + 1;
        } else {
            // Product Quantity minus 1
            $count = $productData['count'] - 1;
        }
        // Update the number of items in the cart
        $this->DB->query('update cart set count = ' . $count . ' where id = ' . $cartId);

        // return prompt data
        return sendSuccess('success');
    }
}

// Get the action parameter. Determine which method is called.
$action = $_REQUEST['action'] ?? '';
// Parameter does not exist Output error message
if ($action == '') {
    echo sendSuccess('parameter cannot be empty', [], 400);
    die;
}

// Instantiate the shopping cart class
$login = new Cart();
// Call the corresponding method in the shopping cart class
echo $login->$action();