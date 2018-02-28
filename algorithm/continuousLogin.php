<?php
/**
 * 在某时间段内  有连续登陆N天的数据  思路1:
 * 查询每个玩家的登陆日期，判断每个登陆日期是否有连续登陆N天的，如果有就记录起来并跳出该玩家的循环到下一位玩家
 * 如果该玩家这一日期没有连续登陆，就判断该玩家下个日期是否有连续登陆
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
    $dataAry[$v['player_id']][$v['day']] = strtotime($v['day']);
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
        foreach ($dataAry as $k => $v) {
            $flag = 0;
            foreach ($v as $kDay => $vDay) {
                for ($i = 0; $i < $vNumber; $i++) {
                    if (isset($v[date('Ymd', $vDay + 86400 * $i)])) {
                        $flag += 1;
                        if ($flag == $vNumber) {
                            $dataAryBak[$vNumber][] = $k;
                            $str .= $vNumber . ': 天' . $k . PHP_EOL;
                            // 跳到下一个玩家
                            break 2;
                        }
                    } else {
                        // 跳到下一个日期中
                        $flag = 0;
                        break;
                    }
                }
            }
        }
    }
}
