<?php
/**
 * Contracts契约之面向接口编程
 * 契约就是所谓的面向接口编程
 * User: zrt
 * Date: 2019/6/18
 * Time: 14:55
 */

// 定义日志的接口规范
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
// 场景：数据记录的方式有 文件记录和数据库记录
// 当调没有使用锲约时，你想要使用 文件记录 方式或者是使用 数据库记录 方式都得手动去改 User 中的构造方法，这样不够灵活
// 所以此处就引用了 面向接口编程 的方法来解决该问题
$user = new User(new DatabaseLog());
$user->login();