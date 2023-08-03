<?php
// Namespace: purpose to solve the problem of duplicate names
namespace action;
// load base class
include_once 'Base.php';

// product list class
class ListsAdmin extends \Base
{
    //Get product data
    public function getOne()
    {
        $id = $_REQUEST['id'] ?? 1;
        $data1 = $this->DB->getOne('select * from products where id = ' . $id);
        $data2 = $this->DB->getAll('select * from products_price where product_id = ' . $id);
        return sendSuccess('success', ['data' => $data1, 'data2' => $data2]);
    }

    public function setHot()
    {
        $hot = $this->REQUEST['hot'] ?? '';
        $id = $this->REQUEST['id'] ?? '';
        $this->DB->query('update products set hot = ' . $hot . ' where id = ' . $id);
        return sendSuccess('success');
    }

    public function update()
    {
        $id = $_REQUEST['id'] ?? '';
        $isC = $this->DB->getOne("select * from products_price where product_id = {$id}");
        if ($isC) {
            $a = $this->DB->query("update products_price set price = {$this->REQUEST['price1']} where product_id = {$id} and supermarket_id = 1");
            $a = $this->DB->query("update products_price set price = {$this->REQUEST['price2']} where product_id = {$id} and supermarket_id = 2");
            $a = $this->DB->query("update products_price set price = {$this->REQUEST['price3']} where product_id = {$id} and supermarket_id = 3");
        } else {
            $dataPP = [
                'product_id' => $id,
                'supermarket_id' => 1,
                'price' => $this->REQUEST['price1'],
            ];
            $this->DB->insert('products_price', $dataPP);
            $dataPP = [
                'product_id' => $id,
                'supermarket_id' => 2,
                'price' => $this->REQUEST['price2'],
            ];
            $this->DB->insert('products_price', $dataPP);
            $dataPP = [
                'product_id' => $id,
                'supermarket_id' => 3,
                'price' => $this->REQUEST['price3'],
            ];
            $this->DB->insert('products_price', $dataPP);
        }

        $a = $this->DB->query("update products set  
            name =  '{$this->REQUEST['name']}',
            types =  '{$this->REQUEST['type'] }',
            price = {$this->REQUEST['price'] },
            cover = '{$this->REQUEST['cover'] }',
            remark =  '{$this->REQUEST['remark']}'  where id = {$id}");

        return sendSuccess('success');
    }


    public function del()
    {
        $id = $this->REQUEST['id'] ?? '';
        $a = $this->DB->query("delete from products  where id = {$id}");
        $a = $this->DB->query("delete from products_price where product_id = {$id}");
        return sendSuccess('success');
    }

    public function add()
    {
        $dataP = [
            'name' => $this->REQUEST['name'],
            'types' => $this->REQUEST['type'],
            'price' => $this->REQUEST['price'],
            'cover' => $this->REQUEST['cover'],
            'remark' => $this->REQUEST['remark'],
        ];
        $this->DB->insert('products', $dataP);
        $pD = $this->DB->getOne('select * from products order by id desc');
        if (empty($pD['id'])) {
            return sendSuccess('network delay', [], 400);
        }
        $dataPP = [
            'product_id' => $pD['id'],
            'supermarket_id' => 1,
            'price' => $this->REQUEST['price1'],
        ];
        $this->DB->insert('products_price', $dataPP);
        $dataPP = [
            'product_id' => $pD['id'],
            'supermarket_id' => 2,
            'price' => $this->REQUEST['price2'],
        ];
        $this->DB->insert('products_price', $dataPP);
        $dataPP = [
            'product_id' => $pD['id'],
            'supermarket_id' => 3,
            'price' => $this->REQUEST['price3'],
        ];
        $this->DB->insert('products_price', $dataPP);
        return sendSuccess('success');
    }

    // Search products by key
    public function seacher()
    {
        // Receive keyword arguments
        $word = $_GET['word'] ?? '';
        // Receive the parameters of the product type, all: to get all the products
        $types = empty($_GET['types']) ? 'all' : $_GET['types'];
        // Receives parameters for sorting by price. none: sort by id
        $priceL = empty($_GET['priceL']) ? 'none' : $_GET['priceL'];

        $whereW = '';  // where search criteria: keyword search
        $whereT = '';  // where search criteria: type search
        $orderStr = '  order by id desc'; // sqlsort
        // $word != ''，Add search criteria.
        if ($word != '') {
            $whereW = "name like '%{$word}%'";
        }
        // $types != 'all'，Add search criteria.
        if ($types != 'all') {
            $whereT = "types = '{$types}'";
        }
        // $priceL != 'none'，Add price sorting
        if ($priceL != 'none') {
            $orderStr = "order by price " . $priceL;
        }
        // There are keyword search, type search and price sorting
        if ($whereT != '' && $whereW != '') {
            $sqlStr = "select * from products where {$whereT} and {$whereW} {$orderStr}";
        } else if ($whereT != '') {
            // Only type search and price sort
            $sqlStr = "select * from products where {$whereT} {$orderStr}";
        } else if ($whereW != '') {
            // Only keyword search and price sorting
            $sqlStr = "select * from products where {$whereW} {$orderStr}";
        } else {
            // Get all items sorted by price
            $sqlStr = "select * from products {$orderStr}";
        }

        $page = $_REQUEST['page'] ?? 1;
        $limit = $_REQUEST['limit'] ?? 10;
        $m = ($page - 1) * $limit;

        $listDATA = $this->DB->getAll($sqlStr . ' limit ' . $m . ',' . $limit);
        $count = $this->DB->getAll($sqlStr);

        // return product data
        return json_encode(['code' => 0, 'msg' => '', 'count' => count($count), 'data' => $listDATA]);


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
$login = new ListsAdmin();
// Call the corresponding method in the shopping cart class
echo $login->$action();
