<?php
// Namespace: purpose to solve the problem of duplicate names
namespace action;
// load base class
include_once 'Base.php';

// product list class
class Lists extends \Base
{
    // Get all product data
    public function getAll()
    {
        $listDATA = $this->DB->getAll('select * from products');
        return sendSuccess('get success', $listDATA);
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
        $orderStr = ''; // sqlsort
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

        // Get multiple items of product data according to sql
        $listDATA = $this->DB->getAll($sqlStr);

        // return product data
        return sendSuccess('get success', $listDATA);


    }
}



// 获取action参数。判断调用的是哪个方法。
$action = $_REQUEST['action'] ?? '';
// 参数不存在 输出错误信息
if ($action == '') {
    echo sendSuccess('Can not be empty', [], 400);
    die;
}
// 实例化购物车类
$login  = new Lists();
// 调用购物车类中对应的方法
echo $login->$action();
