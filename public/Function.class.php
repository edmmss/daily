<?php

class Functions
{
    /**
     * 根据数组的指定字段进行排序 (使用插入算法原理)
     *
     * @author chenbin
     * @param  $arr // 处理数组
     * @param  $field // 根据排序的字段
     * @param  string $order // asc 为正序
     * @return array
     */
    public static function arraySort1($arr, $field, $order = 'asc')
    {
        $len = count($arr);
        for ($i = 1; $i < $len; $i++) {
            $index = $i - 1;
            $valueKey = $arr[$i];
            $value = $arr[$i][$field];

            if ($order == 'asc') {
                while ($index >= 0 && $arr[$index][$field] > $value) {
                    $arr[$index + 1] = $arr[$index];
                    $index--;
                }
            } else {
                while ($index >= 0 && $arr[$index][$field] < $value) {
                    $arr[$index + 1] = $arr[$index];
                    $index--;
                }
            }
            $arr[$index + 1] = $valueKey;
        }

        return $arr;
    }

    /**
     * 根据数组的指定字段进行排序
     *
     * @author chenbin
     * @param  $arr // 处理数组
     * @param  $field // 根据排序的字段
     * @param  string $order // asc 为正序
     * @return array
     */
    public static function arraySort2($arr, $field, $order = 'asc')
    {
        $tmpAry = $tmpAryBak = [];
        foreach ($arr as $k => $v) {
            $tmpAry[] = $v[$field];
        }

        if ($order == 'asc') {
            asort($tmpAry);
        } else {
            arsort($tmpAry);
        }

        foreach ($tmpAry as $k => $v) {
            $tmpAryBak[] = $arr[$k];
        }

        return $tmpAryBak;
    }

    /**
     * 根据数组的指定字段进行排序
     *
     * @author chenbin
     * @param  $arr // 处理数组
     * @param  $field // 根据排序的字段
     * @param  string $order // asc 为正序
     * @return array
     */
    public static function arraySort3($arr, $field, $order = 'asc')
    {
        $len = count($arr);
        for ($i = 1; $i < $len; $i++) {
            for ($j = 0; $j < $len - $i; $j++) {
                if ($order == 'asc') {

                    if ($arr[$j][$field] > $arr[$j + 1][$field]) {
                        $tmp = $arr[$j];
                        $arr[$j] = $arr[$j + 1];
                        $arr[$j + 1] = $tmp;
                    }
                } else {
                    if ($arr[$j][$field] < $arr[$j + 1][$field]) {
                        $tmp = $arr[$j];
                        $arr[$j] = $arr[$j + 1];
                        $arr[$j + 1] = $tmp;
                    }
                }
            }
        }

        return $arr;
    }

    /**
     * 生成uuid
     *
     * @author chenbin
     * @param  $prefix // 前缀
     * @return string
     */
    public static function uuid($prefix = '')
    {
        $chars = md5(uniqid(mt_rand(), true));
        $uuid = substr($chars, 0, 8) . '-';
        $uuid .= substr($chars, 8, 4) . '-';
        $uuid .= substr($chars, 12, 4) . '-';
        $uuid .= substr($chars, 16, 4) . '-';
        $uuid .= substr($chars, 20, 12);

        return $prefix . $uuid;
    }

    /**
     * 计算权重
     *
     * @author chenbin
     * @param  $weight // 权重数组
     * @return int
     */
    public static function countWeight($weight = [])
    {
        $roll = rand(1, array_sum($weight));
        $tmp = 0;
        $key = 0;
        foreach ($weight as $k => $v) {
            $min = $tmp;
            $tmp += $v;
            $max = $tmp;
            if ($roll > $min && $roll <= $max) {
                $key = $k;
                break;
            }
        }

        return $key;
    }

    /**
     * ssh2 执行脚本
     *
     * @author chenbin
     * @param  $cmd // 调动shell脚本的命令 如：/bin/bash -x /home/chenbin/sh/test.sh
     * @param  $type // 资源流是否阻塞
     * @return string
     */
    public static function ssh2Exec($cmd, $type = true)
    {
        $ip = '192.168.80.10';
        $port = '22';
        $user = 'root';
        $pass = '';

        $connection = ssh2_connect($ip, $port);
        ssh2_auth_password($connection, $user, $pass);

        $stream = ssh2_exec($connection, $cmd);
        stream_set_blocking($stream, $type);
        $result = stream_get_contents($stream);

        return $result;
    }

    /**
     * 字符串截取函数
     *
     * @autoho chenbin
     * @param string $str 需要被切割的字符串
     * @param int $length 需要保留的字符串长度(汉字的话,就是汉字个数),为0时:表示从$start截取到结尾
     * @param string $dot 超过时显示的符号
     * @return string
     */
    public static function strCut($str, $length = 0, $start = 0, $charset = "utf-8")
    {
        if (strlen($str) < 4) {
            return $str;
        }
        $array = [];
        $array['utf-8'] = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
        $array['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
        $array['gbk'] = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
        $array['big5'] = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
        preg_match_all($array[$charset], $str, $match);

        if ($length == 0) {
            $length = count($match[0]);
        }

        return join("", array_slice($match[0], $start, $length));
    }
}



