<?php

/**
 * className
 *
 * @author chenbin
 * @param  $str       // 需要处理的字符串
 * @return string
 */
function strReverse($str)
{
    $tmpStr = '';
    $len = mb_strlen($str);

    for ($i = $len; $i >= 0; $i--)
    {
        $tmpStr .= mb_substr($str, $i, 1);
    }

    return $tmpStr;
}

$str = strReverse('abcde');
var_dump($str);
