<?php

/**
 * 在某时间段内 有连续登陆N天的数据  思路2:
 * 查询每个玩家的登陆日期，组装出N天为步进的二维数组，再用改组装的时间数组与玩家登陆日期数组进行交集，
 * 计算交集的数组的数量如果为N，证明该玩家连续登陆可以记录下来
 */

$access = [
    'localhost' => [
        ['host' => '192.168.0.118', 'pass' => ''],
    ],
];

$plat = 'localhost';
$timeAry['sTime'] = '20170502';
$timeAry['eTime'] = '20170505';

// 连续登陆日期数组
$numberAry = [2, 4];

$number = ceil((strtotime($timeAry['eTime']) - strtotime($timeAry['sTime'])) / 86400) + 1;

$mypdo = new MyPDO($access[$plat][0]);
$sql = "SELECT player_id, day
        FROM t_log_login 
        WHERE day BETWEEN '{$timeAry['sTime']}' AND '{$timeAry['eTime']}'";

$row = $mypdo->db_getAll($sql);
$dataAry = $dataAryBak = [];
foreach ($row as $v) {
    $dataAry[$v['player_id']][$v['day']] = $v['day'];
}

$str = '';
foreach ($numberAry as $vNumber) {
    if ($vNumber == $number) {
        foreach ($dataAry as $k => $v) {
            if (count($v) == $number) {
                $dataAryBak[$vNumber][] = $k;
                $str .= $vNumber . ': 天' . $k . PHP_EOL;
            }

        }
    } else {
        $tmpAryBak = range(strtotime($timeAry['sTime']), strtotime($timeAry['eTime']), 86400);
        // 组装日期数组
        $dateAry = [];
        foreach ($tmpAryBak as $k => $vDay) {
            for ($i = 0; $i < $vNumber; $i++) {
                if ($vDay + 86400 * $i > $tmpAryBak[count($tmpAryBak) - 1]) {
                    unset($dateAry[$k]);
                    break;
                }
                $dateAry[$k][] = date('Ymd', $vDay + 86400 * $i);
            }
        }

        foreach ($dataAry as $k => $v) {
            foreach ($dateAry as $kDate => $vDate) {
                if (count(array_intersect($v, $vDate)) == $vNumber) {
                    $dataAryBak[$vNumber][] = $k;
                    $str .= $vNumber . ': 天' . $k . PHP_EOL;
                    break;
                }
            }
        }
    }
}

var_dump($dataAryBak);
