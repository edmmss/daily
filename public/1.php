<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/6/10
 * Time: 14:34
 */

include './Function.class.php';
function __autoload($className)
{
    $path = $className.'.class.php';
    require_once($path);
}


$mysql = new MyPDO(['dbname'=>'meal']);

$sql = "SELECT id, total, foodname FROM meal_orders LIMIT 10";
$row = $mysql->db_getAll($sql);


