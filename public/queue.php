<?php
/**
 * Created by PhpStorm.
 * User: chenbin
 * Date: 2017/7/22
 * Time: 16:45
 */

/**
 *  Redis队列在流量削峰是的应用，queue.php是模拟用户的请求，但是这里只能模拟数量，并发量则需要专门的测压工具
 *  storage.php是将缓存到Redis的数据异步插入MySQL数据库，达到流量平均
 *  还需要结合Linux中的计划任务定时调用storage.php文件
 *  如：
 *    */1 * * * * /usr / local / php / bin / php / usr / local / apache2 / htdocs / equip /public/storage . php
*/

function __autoload($className)
{
    $path = $className . '.class.php';
    require_once($path);
}

$cache = new Cache();

for ($i = 0; $i <= 1000; $i++) {
    if ($cache->size('queue') < 200) {
        $uid = rand(100000, 999999) . '_' . microtime(true);
        $cache->push('queue', $uid);
        echo '入列成功' . $uid . '<br>';
    } else {
        echo '入列失败' . '<br>';
    }
}
