<?php
/**
 * Facades外观模式背后实现原理
 *
 * User: zrt
 * Date: 2019/6/18
 * Time: 17:55
 */

// Facade 工作原理
// 1、Facede 核心实现原理就是在 UserFacade 提前注入 Ioc 容器。
// 2、定义一个服务提供者的外观类，在该类定义一个类的变量，跟 ioc 容器绑定的 key 一样，
// 3、通过静态魔术方法__callStatic 可以得到当前想要调用的 login
// 4、使用 static::$ioc->make ('user');

class Ioc
{
    public $binding = [];
    public function bind($abstract, $concrete)
    {
        if (!$concrete instanceof Closure) {
            $concrete = function ($ioc) use ($concrete) {
                return $ioc->build($concrete);
            };
        }
        $this->binding[$abstract]['concrete'] = $concrete;
    }
    public function make($abstract)
    {
        $concrete = $this->binding[$abstract]['concrete'];
        return $concrete($this);
    }
    public function build($concrete) {
        $reflector = new ReflectionClass($concrete);
        $constructor = $reflector->getConstructor();
        if(is_null($constructor)) {
            return $reflector->newInstance();
        }else {
            $dependencies = $constructor->getParameters();
            $instances = $this->getDependencies($dependencies);
            return $reflector->newInstanceArgs($instances);
        }
    }
    protected function getDependencies($paramters) {
        $dependencies = [];
        foreach ($paramters as $paramter) {
            $dependencies[] = $this->make($paramter->getClass()->name);
        }
        return $dependencies;
    }
}
interface log
{
    public function write();
}
// 文件记录日志
class FileLog implements Log
{
    public function write(){
        echo 'file log write...';
    }
}
// 数据库记录日志
class DatabaseLog implements Log
{
    public function write(){
        echo 'database log write...';
    }
}
class User
{
    protected $log;
    public function __construct(Log $log)
    {
        $this->log = $log;
    }
    public function login()
    {
        // 登录成功，记录登录日志
        echo 'login success...';
        $this->log->write();
    }
}

//以下为该章节主要代码
class UserFacade
{
    protected static $ioc;
    public static function setFacadeIoc($ioc)
    {
        static::$ioc = $ioc;
    }
    protected static function getFacadeAccessor()
    {
        return 'user';
    }
    public static function __callStatic($method, $args)
    {
        $instance = static::$ioc->make(static::getFacadeAccessor());
        return $instance->$method(...$args);
    }
}
//实例化IoC容器
$ioc = new Ioc();
$ioc->bind('log','FileLog');
$ioc->bind('user','User');
UserFacade::setFacadeIoc($ioc);
UserFacade::login();
exit;

// 教程地址：https://learnku.com/docs/laravel-core-concept/5.5/Facades/3020