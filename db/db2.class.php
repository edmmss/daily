<?php
/*
对数据库的增删改查
 */

//加载配置文件
include './config.php';

class DB
{
    protected $link,               // 用于保存数据库连接信息
        $limit,              // 用于保存limit条件
        $order,              // 用于保存order条件
        $where,              // 用于保存where查询条件
        $allFields = [],     // 用于保存所有字段
        $tabName,            // 用于保存数据表名
        $lastSql;            // 最后一条sql语句

    public function __construct($tabName)
    {
        //连接数据库
        $this->link = new PDO("mysql:host=" . HOST . ";dbname=" . DB . ";charset=" . CHAR . "", USER, PWD, [PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING]);
        //保存表名
        $this->tabName = FIX . $tabName;

        //缓存数据表中的所有字段
        $this->getFields();
    }

    /**
     * 查询数据表中所有数据
     * @return array 返回一个二维数组
     */
    public function select()
    {
        $sql = "SELECT * FROM {$this->tabName} {$this->where} {$this->order} {$this->limit}";

        return $this->send($sql);
    }

    /**
     * 根据ID查询一条数据
     * @param  int $id 要查询数据的ID号
     * @return array     返回一个一维数组
     */
    public function find($id)
    {
        $sql = "SELECT * FROM {$this->tabName} WHERE id={$id}";

        return $this->send($sql)[0];
    }


    /**
     * 统计总条目数
     * @return int 返回总共有多少条数据
     */
    public function count()
    {
        $sql = "SELECT COUNT(*) AS total FROM {$this->tabName} {$this->where}";

        return $this->send($sql)[0]['total'];
    }

    /**
     * 查询所需字段
     * @param  int $id 要查询数据的ID号
     * @return array     返回一个一维数组
     */

    /**
     * 添加数据
     * @param array $arr 要添加的数据数组($_POST)
     */
    public function add($arr)
    {
        //过滤非法字段
        foreach ($arr as $k => $v) {
            if (!in_array($k, $this->allFields)) unset($arr[$k]);
        }

        //判断是不是手贱用户传的全是不符合要求的字段名
        if (empty($arr)) die('您传的数据不合格~~');

        $keys = join(',', array_keys($arr));
        $vals = join("','", array_values($arr));
        $sql = "INSERT INTO {$this->tabName}({$keys}) VALUE('{$vals}')";

        return $this->execute($sql);
    }

    /**
     * 删除数据
     * @param  int $id 要删除的ID
     * @return int     返回受影响行数
     */
    public function delete($arr)
    {
        if ($arr == 'all') {
            $sql = " DELETE FROM {$this->tabName} ";
        } else {
            if (empty($arr)) die('您传的数据不合格~~');
            //过滤非法字段
            foreach ($arr as $k => $v) {
                if (!in_array($k, $this->allFields)) unset($arr[$k]);
            }
            //判断是不是手贱用户传的全是不符合要求的字段名
            $str = '';
            foreach ($arr as $k => $v) {
                $str .= $k . " = '" . $v . "' AND ";
            }
            $str .= " 1 = 1 ";
            $sql = "DELETE FROM {$this->tabName} {$this->where} $str";
        }

        return $this->execute($sql);
    }


    /**
     * 修改数据
     * @param  arr     需要修改的数据
     * @return int     返回受影响行数
     */
    public function update($arr)
    {
        //过滤非法字段
        foreach ($arr as $k => $v) {
            if (!in_array($k, $this->allFields)) unset($arr[$k]);
        }
        //这是利用数组进行查询
        if (!empty($arr['id'])) {
            $id = $arr['id'];
            unset($arr['id']);
            $where = "WHERE id = $id ";
        } else {
            //这是利用where 方法进行查询
            $where = $this->where;
        }
        $str = '';
        foreach ($arr as $k => $v) {
            $str .= $k . " = '" . $v . "',";
        }
        //处理最后的逗号
        $str = strrev(substr(strrev($str), 1));
        $sql = "UPDATE {$this->tabName} SET " . $str . $where;

        return $this->execute($sql);
    }

    /**
     * 返回最后一条sql语句
     * @return string      返回最后一条sql语句
     */
    public function _sql()
    {
        return $this->lastSql;
    }

    /*************************** 连贯操作 ***************************/
    public function limit($limit)
    {
        $this->limit = 'LIMIT ' . $limit;

        return $this;   //保证连贯操作
    }

    public function order($order)
    {
        $this->order = 'ORDER BY ' . $order;

        return $this;
    }

    public function where($map)
    {
        //判断传的是否是数组
        if (empty($map) || !is_array($map)) {
            $this->where = " WHERE " . $map;

            return $this;
        }
        //判断是用and还是or连接
        if (isset($map['_logic'])) {
            $logic = ' OR ';
            unset($map['_logic']);
        } else {
            $logic = ' AND ';
        }

        $tmp = [];
        foreach ($map as $k => $v) {
            if (is_array($v)) {
                $type = $v[0];

                switch ($type) {
                    case 'gt':
                        $tmp[] = "`$k` > '{$v[1]}'";
                        break;
                    case 'lt':
                        $tmp[] = "`$k` < '{$v[1]}'";
                        break;
                    case 'like':
                        $tmp[] = "`$k` LIKE '{$v[1]}'";
                        break;
                    default:
                        die('手贱用户');
                }
            } else {
                $tmp[] = "`$k` = '$v'";
            }
        }

        $where = 'WHERE ' . join($logic, $tmp);
        $this->where = $where;

        return $this;
    }


    /*************************** 辅助方法 ***************************/
    /**
     * 获取数据表中所有字段，给allFields赋值
     */
    protected function getFields()
    {
        //查看表结构
        $sql = "DESC {$this->tabName}";
        $res = $this->send($sql);
        $arr = [];
        foreach ($res as $v) {
            $arr[] = $v['Field'];
        }
        $this->allFields = $arr;
    }

    /**
     * 用于执行查询
     * @param  string $sql 要执行的sql语句
     * @return array      返回一个二维数组
     */
    protected function send($sql)
    {
        $tmp = $this->link->query($sql);
        $this->lastSql = $sql;
        $arr = [];
        if ($tmp && $tmp->rowCount() > 0) {
            $arr = $tmp->fetchAll(PDO::FETCH_ASSOC);
        }

        return $arr;
    }

    /**
     * 用于执行增删改
     * @param  string $sql 要执行的sql语句
     * @return int      添加返回最后插入ID，删除或者修改返回受影响行数
     */
    protected function execute($sql)
    {
        $res = $this->link->exec($sql);
        $this->lastSql = $sql;
        if ($res && $res > 0) {
            return $this->link->lastInsertId() ? $this->link->lastInsertId() : $res;
        }
    }

    public function __destruct()
    {

    }
}
