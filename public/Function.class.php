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
     * @author chenbin
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

    /**
     * 读取excel文件数据
     *
     * @author chenbin
     * @param $file // excel文件路径
     * @param int $startRow // 数据开始行数(非header数据开始行数)
     * @param array $colKey // 自定义列key名(不传入则使用header(第一行)col 字段作key)
     * @return array|bool
     */
    public static function getDataFromExcelFile($file, $startRow = 2, $colKey = [])
    {
        // 检查excel文件并尝试打开文件读入数据到 $data 并返回
        do { // if 出错 break
            $phpReader = new \PHPExcel_Reader_Excel2007();
            if (!$phpReader->canRead($file)) {
                // excel2007打不开, 尝试用excel5打开
                $phpReader = new \PHPExcel_Reader_Excel5();
                if (!$phpReader->canRead($file)) {
                    break; // 打开excel文件失败
                }
            }

            // 允许打开
            $phpExcel = $phpReader->load($file);
            // 获取表中的第一个工作表，如果要获取第二个，把0改为1，依次类推
            $sheet = $phpExcel->getSheet(0);
            // excel总行数
            $allRow = $sheet->getHighestRow();
            // excel总列数(A B C....)
            $allColumn = $sheet->getHighestColumn();

            $data = []; // 要返回的数据, 二维数组
            if (!$colKey) { // 没有自定义列key
                for ($currentColumn = 'A'; $currentColumn <= $allColumn; $currentColumn++) { // 列
                    $colKey[] = $phpExcel->getActiveSheet()->getCell($currentColumn . '1')->getValue();
                }
            }

            $goalAllColumn = chr(ord('A') + count($colKey) - 1); // 目标总列数(rowKey的总条数)(A B C....)
            if ($allColumn != $goalAllColumn) {
                break; // 校验列数
            }
            // 获取数据
            for ($currentRow = $startRow; $currentRow <= $allRow; $currentRow++) { //  行
                $dataOneDimensionalIndex = $currentRow - $startRow; // data数组一维下标
                $emptyLine = true; // 是否空行
                for ($currentColumn = 'A'; $currentColumn <= $allColumn; $currentColumn++) { // 列
                    $dataTwoDimensionalIndex = $colKey[ord($currentColumn) - ord('A')]; // data数组二维下标

                    $data[$dataOneDimensionalIndex][$dataTwoDimensionalIndex] = $phpExcel->getActiveSheet()->getCell($currentColumn . $currentRow)->getValue();
                    // 用''填充null
                    if (!$data[$dataOneDimensionalIndex][$dataTwoDimensionalIndex]) {
                        $data[$dataOneDimensionalIndex][$dataTwoDimensionalIndex] = '';
                    }

                    if ($emptyLine && $data[$dataOneDimensionalIndex][$dataTwoDimensionalIndex]) {
                        $emptyLine = false;
                    }
                }

                if ($emptyLine) {
                    unset($data[$dataOneDimensionalIndex]);
                }
            }

            return $data;
        } while (0);

        return false;
    }
}



