<?php
/**
 * Created by PhpStorm.
 * User: zrt
 * Date: 2019/6/17
 * Time: 10:13
 */

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
    public function __construct(FileLog $log)
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
function make($concrete){
    echo '1-';
    var_dump($concrete);
    echo "<br/>";
    // 输出：1-string(4) "User"
    $reflector = new ReflectionClass($concrete);

    echo '2-';
    var_dump($reflector);
    echo "<br/>";
    // 输出：2-object(ReflectionClass)#1 (1) { ["name"]=> string(4) "User" }
    $constructor = $reflector->getConstructor();

    echo '3-';
    var_dump($constructor);
    echo "<br/>";
    // 3-object(ReflectionMethod)#2 (2) { ["name"]=> string(11) "__construct" ["class"]=> string(4) "User" }

    // 如果没有构造函数，则直接创建对象
    if(is_null($constructor)) {
        return $reflector->newInstance();
    }else {
        // 构造函数依赖的参数
        $dependencies = $constructor->getParameters();
        echo '4-';
        var_dump($dependencies);
        echo "<br/>";
        // 4-array(1) { [0]=> object(ReflectionParameter)#3 (1) { ["name"]=> string(3) "log" } }

        //进入递归调用 - 即先调用到 步骤6
        // 根据参数返回实例，如FileLog
        $instances = getDependencies($dependencies);
        echo '5-';
        var_dump($instances);
        echo "<br/>";
        return $reflector->newInstanceArgs($instances);
    }
}
function getDependencies($paramters) {
    echo '6-';
    var_dump($paramters);
    echo "<br/>";
    // 6-array(1) { [0]=> object(ReflectionParameter)#3 (1) { ["name"]=> string(3) "log" } }
    $dependencies = [];
    foreach ($paramters as $key => $paramter) {
        echo '7-'.$key;
        var_dump($paramter);
        echo "<br/>";
        echo $paramter->getClass()->name;
        echo "<br/>";
        // 7-0object(ReflectionParameter)#3 (1) { ["name"]=> string(3) "log" }
        // FileLog
        $dependencies[] = make($paramter->getClass()->name);
    }
    return $dependencies;
}
//1、通过反射创建即将调用的类的实例
//2、获得即将创建类的构造函数，如果创建构造函数，取得需传入的参数，再次通过反射创建对应的实例
//3、完成调用
$user = make('User');
$user->login();
// 调用顺序
//1-string(4) "User"
//2-object(ReflectionClass)#1 (1) { ["name"]=> string(4) "User" }
//3-object(ReflectionMethod)#2 (2) { ["name"]=> string(11) "__construct" ["class"]=> string(4) "User" }
//4-array(1) { [0]=> object(ReflectionParameter)#3 (1) { ["name"]=> string(3) "log" } }
//6-array(1) { [0]=> object(ReflectionParameter)#3 (1) { ["name"]=> string(3) "log" } }
//7-0object(ReflectionParameter)#3 (1) { ["name"]=> string(3) "log" }
//FileLog
//1-string(7) "FileLog"
//2-object(ReflectionClass)#6 (1) { ["name"]=> string(7) "FileLog" }
//3-NULL
//5-array(1) { [0]=> object(FileLog)#7 (0) { } }
exit;