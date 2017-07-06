<?php
/**
 * Created by PhpStorm.
 * User: chenbin
 * Date: 2017/6/12
 * Time: 10:45
 */

/**
 * Cache类
 *
 * FRT_PREFIX为键值前缀, 主要处理一机多服
 *
 * @author chenbin
 * @encoding UTF-8
 */
class Cache
{
    /**
     * 队列前缀
     * @var string
     */
    protected $prefix = 'Cache_';

    /**
     * 操作句柄
     * @var string
     */
    protected $handler ;

    /**
     * 缓存连接参数
     * @var array
     */
    protected $options = [
    	'host'       => 'localhost',
        'port'       => 6379,
        'timeOut'    => 86400,
    ];

    /**
     * 构造函数
     */
    public function __construct( $options=array() )
    {
        if ($options)
        {
            $this->options = &$options;
        }

        $this->handler = new Redis();
        if (empty($this->handler))
        {
            echo '缓存类扩展没装好吧';
        }
        else
        {
            $this->handler->connect( $this->options['host'],$this->options['port'],$this->options['timeOut'] ) ;
        }
    }

    /**
     * 读取缓存
     *
     * @param string $name 缓存变量名
     * @return mixed
     */
    public function get( $name , $isSerialize = true)
    {
        return $isSerialize ? unserialize($this->handler->get( $this->prefix.$name )) : ($this->handler->get( $this->prefix.$name ));
    }

    /**
     * 写入缓存
     *
     * @param string $name 缓存变量名
     * @param mixed $value 存储数据
     * @param integer $expire 有效时间（秒）
     * @return Boolean
     */
    public function set($name, $value, $expire = null)
    {
        is_null($expire) && ($expire = $this->options['timeOut']);

        if ( $this->handler->setex( $this->prefix.$name, $expire, serialize($value) ) )
        {
            return true;
        }

        return false;
    }

    /**
     * add写入缓存
     *
     * @param string $name 缓存变量名
     * @param mixed $value 存储数据
     * @param integer $expire 有效时间（秒）
     * @return Boolean
     */
    public function add($name, $value, $expire = null)
    {
        is_null($expire) && ($expire = $this->options['timeOut']);

        // setnx 就相当于memcache的add,但不能直接设置有效期
        if ( $this->handler->setnx( $this->prefix.$name,serialize($value) ) )
        {
            $this->handler->expire( $this->prefix.$name,$expire ); //设置有效期
            return true;
        }

        return false;
    }

    /**
     * 删除缓存
     *
     * @param string $name 缓存变量名
     * @return void
     */
    public function delete($name)
    {
        return $this->handler->delete($this->prefix.$name);
    }

    /**
     * 清除缓存
     *
     * @return Boolean
     */
    public function clear()
    {
        return $this->handler->flushAll();
    }

    /**
     * 判断指定的键是否存在
     *
     * @param string $key 键
     * @return boolean true 存在，false 不存在
     */
    public function keyExists( $key )
    {
        return $this->handler->exists( $this->prefix.$key );
    }

    /**
     * 获取hash表里指定键的值
     *
     * @param string $key hash表名
     * @param string $hashKey hash表的键
     * @return string|boolean 如果成功获取则返回值的内容；hash表不存在，或者hash键不存在，则返回false
     */
    public function hashGet( $key, $hashKey )
    {
        if ($hashData = $this->handler->hget( $this->prefix.$key, $hashKey ))
        {
            return unserialize($hashData);
        }
        else
        {
            return false;
        }
    }

    /**
     * 添加一个值到hash表的指定的键储存
     *
     * @param string $key hash表名
     * @param string $hashKey hash表的键
     * @param string $value 要储存的值
     * @return integer|boolean 1 如果值不存在并且成功添加；0 值已经存在并且成功替换；false 出错
     */
    public function hashSet( $key, $hashKey, $value )
    {
        return $this->handler->hSet( $this->prefix.$key, $hashKey, serialize($value) );
    }

    /**
     * 设置整个hash表内容
     *
     * @param string $key
     * @param array $members
     * @param integer $expire
     * @return boolean 设置hash表内容和设置有效时间都成功返回true，否则返回false
     */
    public function hashMSet( $key, $members, $expire )
    {
        foreach ($members as $k => $v)
        {
            $members[$k] = serialize($v);
        }
        return $this->handler->hMset( $this->prefix.$key, $members) && $this->handler->expire( $this->prefix.$key, $expire );
    }

    /**
     * 获取hash表的所有内容，并且以数组的方式返回
     *
     * @param string $key hash表名
     * @return array hash表的内容
     * @return array 以数组的方式返回整个hash表的内容
     */
    public function hashGetAll( $key )
    {
        if ($hashData = $this->handler->hGetAll( $this->prefix.$key ))
        {
            foreach ($hashData as $key => $value)
            {
                $hashData[$key] = unserialize($value);
            }
            return $hashData;
        }
        else
        {
            return false;
        }
    }

    /**
     * 判断hash表里面的某个键是否存在
     *
     * @param string $key hash表名
     * @param string $memberKey hash的键
     * @return boolean 存在则返回true，不存在则返回false
     */
    public function hashExists( $key, $memberKey )
    {
        return $this->handler->hExists( $this->prefix.$key, $memberKey );
    }

    /**
     * 查找所有KEY，支持模糊查找
     *
     * @param string $key 缓存的KEY值,*表示所有
     * @return array
     */
    public function keys( $key )
    {
        return $this->handler->keys( $this->prefix.$key );
    }

    /**
     * 删除hash表里面的某个域
     *
     * @param string $key hash表名
     * @param string $memberKey hash的域(在Redis2.4及以上的版本，可以删除多个域，用空格隔开)
     * @return boolean 存在则返回true，不存在则返回false
     */
    public function hashDel( $key,$memberKey )
    {
        return $this->handler->HDEL( $this->prefix.$key, $memberKey);
    }

    /**
     * 获取生存时间
     *
     * @param string $name 缓存变量名
     * @return int 剩余时间秒(如果已过期则返回-1)
     */
    public function ttl( $name )
    {
    	return $this->handler->ttl( $this->prefix.$name );
    }
    
     /**
     * 入队列
     *
     * @param $queueName
     * @param $data
     * @return mixed
     */
    public function push( $queueName, $data )
    {
        return $this->handler->lPush( $this->prefix.$queueName, $data );
    }

    /**
     * 出队列
     *
     * @param $queueName
     * @return mixed
     */
    public function pop( $queueName )
    {
        return $this->handler->rPop( $this->prefix.$queueName );
    }

    /**
     * 队列当前数据总量
     *
     * @param $queueName
     * @return mixed
     */
    public function size( $queueName )
    {
        return $this->handler->lSize( $this->prefix.$queueName );
    }
}
