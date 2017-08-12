<?php

$arr = range(1, 20, 1);

$tmpAry = [];
$count = count($arr);
for ($i = 0; $i < $count; $i++)
{
    // 这里减一是因为数组从键从0开始
    $num = mt_rand(0, $count - $i - 1);
    $tmpAry[] = $arr[$num];
    unset($arr[$num]);
    $arr = array_values($arr);
}
