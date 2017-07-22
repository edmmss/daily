<?php
/**
 * Created by PhpStorm.
 * User: chenbin
 * Date: 2017/7/22
 * Time: 16:45
 */

set_time_limit(0);
function __autoload($className)
{
    $path = $className.'.class.php';
    require_once($path);
}

$cache = new Cache();
$mypdo = new MyPDO(['dbname'=>'test', 'pass'=>'shazhu1314', 'host'=>'localhost']);

$len = $cache->size('queue');
if ($len > 0)
{
    for ($i = 0; $i < $len; $i++)
    {
        $res = $cache->pop('queue');
        if ($res)
        {
            $sql = "INSERT INTO `queue` (`uid`, `update_time`) 
                    VALUES ('{$res}', NOW())";
            $mypdo->db_insert($sql);
        }
        else
        {
            continue;
        }
        usleep(50000);
    }
}

