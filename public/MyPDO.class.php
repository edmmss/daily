<?php

class MyPDO
{
    private $type;
    private $host;
    private $port;
    private $user;
    private $pass;
    private $dbname;
    private $charset;
    private $logPath;

    private $pdo;	//保存PDO类对象

    public function __construct($arr = array())
    {
        $this->type    = isset($arr['type']) ? $arr['type'] : 'mysql';
        $this->host    = isset($arr['host']) ? $arr['host'] : '127.0.0.1';
        $this->port    = isset($arr['port']) ? $arr['port'] : '3306';
        $this->user    = isset($arr['user']) ? $arr['user'] : 'root';
        $this->pass    = isset($arr['pass']) ? $arr['pass'] : '';
        $this->dbname  = isset($arr['dbname']) ? $arr['dbname'] : '';
        $this->charset = isset($arr['charset']) ? $arr['charset'] : 'utf8';
        $this->logPath = isset($arr['logPath']) ? $arr['logPath'] : '/tmp/';

        $this->db_connect();

        $this->db_exception();
    }

    private function db_connect()
    {
        if(!is_resource($this->pdo))
        {
            if(empty($this->dbname))
            {
                $this->pdo = new PDO("{$this->type}:host={$this->host};port={$this->port};charset={$this->charset}",$this->user,$this->pass);
            }
            else
            {
                $this->pdo = new PDO("{$this->type}:host={$this->host};port={$this->port};dbname={$this->dbname};charset={$this->charset}",$this->user,$this->pass);
            }
        }
    }

    private function db_exception()
    {
        //开启异常模式
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
    }

    private function db_exec($sql)
    {
        try
        {
            return $this->pdo->exec($sql);
        }
        catch(PDOException $e)
        {
            $this->write( 'SQL语句错误'.PHP_EOL.'错误代码是：'.$e->getCode().PHP_EOL.'错误信息是：'.$e->getMessage().PHP_EOL.'错误的脚本是：'.$e->getFile().PHP_EOL.'错误的行是：'.$e->getLine(), 1);
            exit;
        }
    }

    private function db_query($sql)
    {
        try
        {
            return $this->pdo->query($sql);
        }
        catch(PDOException $e)
        {
            $this->write( 'SQL语句错误'.PHP_EOL.'错误代码是：'.$e->getCode().PHP_EOL.'错误信息是：'.$e->getMessage().PHP_EOL.'错误的脚本是：'.$e->getFile().PHP_EOL.'错误的行是：'.$e->getLine(), 1);
            exit;
        }
    }

    private function query_transaction($sql = array())
    {
        try
        {
            if(!is_array($sql) || empty($sql))
            {
                $this->write('The parameter is incorrect', 1);
                return false;
            }

            $this->pdo->beginTransaction();

            $ret = array();
            foreach ($sql as $val)
            {
                if(preg_match('/^select.*/i', trim($val)))
                {
                    $ret[trim($val)] = $this->db_getAll($val);
                }
                else
                {
                    $ret[trim($val)] = (preg_match('/^insert.*/i', trim($val)) ? $this->db_insert($val) : $this->db_exec($val));
                }
            }

            $this->pdo->commit();

            return $ret;
        }
        catch(PDOException $e)
        {
            $this->pdo->rollBack();
            $this->write( 'SQL语句错误'.PHP_EOL.'错误代码是：'.$e->getCode().PHP_EOL.'错误信息是：'.$e->getMessage().PHP_EOL.'错误的脚本是：'.$e->getFile().PHP_EOL.'错误的行是：'.$e->getLine(), 1);
            exit;
        }
    }

    public function db_insert($sql)
    {
        $this->db_exec($sql);

        return $this->pdo->lastInsertId();
    }

    public function db_delete($sql)
    {
        return $this->db_exec($sql);
    }

    public function db_update($sql)
    {
        return $this->db_exec($sql);
    }

    public function db_getOne($sql)
    {
        return $this->db_query($sql)->fetch(PDO::FETCH_ASSOC);
    }

    public function db_getAll($sql)
    {
        return $this->db_query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function db_transaction($sql = array())
    {
        return $this->query_transaction($sql);
    }

    private function write( $msg, $isEcho = 0, $file = '', $isAdd = 1)
    {
        $time = date( 'Ymd', time() );
        $nowTime = date( 'Y-m-d H:i:s', time() );

        if( $isEcho != 0 )
        {
            echo str_replace(PHP_EOL, '<br/>', '[' . $nowTime . '] : ' . $msg . PHP_EOL);
        }

        if( empty($file) )
        {
            $file = $this->logPath . 'pdo_error_' . $time . '.log';
        }
        else
        {
            $file = $this->logPath . $file . '_' . $time . '.log';
        }

        if ( 1 == $isAdd )
        {
            return file_put_contents( $file,"[{$nowTime}] : {$msg}" . PHP_EOL, FILE_APPEND );
        }
        else
        {
            return file_put_contents( $file,"[{$nowTime}] : {$msg}" . PHP_EOL );
        }
    }

}
