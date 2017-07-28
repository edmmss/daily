<?php
/**
 * 输出菱形形状
 *
 * $max       菱形的行数
 * $num       空格的数量
 * $starNum   星星的数量
 * $flag      上面还是下面
 */
$str = '';
$max = 7;
$num = floor($max / 2);
$flag = 1;

for ($i = 1; $i <= $max; $i++)
{
    $tmp = $num;
    $starNum = $max - ($num * 2);
    for ($j = 1; $j <= $max; $j++)
    {
        if ($num > 0)
        {
            $str .= '&nbsp';
            $num -=1;
        }
        else
        {
            if ($starNum > 0)
            {
                $str .= '*';
                $starNum -=1;
            }
            else
            {
                $str .= '&nbsp';
                $num = $tmp;
            }
        }
    }

    $num = $tmp;
    if ($num > 0 && $flag == 1)
    {
        $num -=1;
    }
    else
    {
        $num +=1;
        $flag = 2;
    }
    $str .= '<br>';
}

echo $str;
var_dump($str);
