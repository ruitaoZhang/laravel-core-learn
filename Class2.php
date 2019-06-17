<?php
/**
 * 如何实现Ioc容器和服务提供者是什么概念
 * User: zrt
 * Date: 2019/6/17
 * Time: 15:44
 */
//实现思路
//1、Ioc 容器维护 binding 数组记录 bind 方法传入的键值对如:log=>FileLog, user=>User
//2、在 ioc->make ('user') 的时候，通过反射拿到 User 的构造函数，拿到构造函数的参数，发现参数是 User 的构造函数参数 log, 然后根据 log 得到 FileLog。
//3、这时候我们只需要通过反射机制创建 $filelog = new FileLog ();
//4、通过 newInstanceArgs 然后再去创建 new User ($filelog);

//这里的容器就是指 Ioc 容器，服务提供者就是 User。
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
        //这里为什么要返回一个closure呢？因为bind的时候还不需要创建User对象，所以采用closure等make的时候再创建FileLog;
        $this->binding[$abstract]['concrete'] = $concrete;
    }
    public function make($abstract)
    {
        // 根据key获取binding的值
        $concrete = $this->binding[$abstract]['concrete'];
        return $concrete($this);
    }
    // 创建对象
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
    // 获取参数的依赖
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

//实例化IoC容器
$ioc = new Ioc();
//绑定提供者
$ioc->bind('log','FileLog');
$ioc->bind('user','User');
$user = $ioc->make('user');
$user->login();
exit;

//教程地址：https://learnku.com/docs/laravel-core-concept/5.5/Ioc%E5%AE%B9%E5%99%A8,%E6%9C%8D%E5%8A%A1%E6%8F%90%E4%BE%9B%E8%80%85/3019