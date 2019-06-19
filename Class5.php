<?php
/**
 * Laravel中间件,管道之面向切面编程
 * User: zrt
 * Date: 2019/6/19
 * Time: 17:38
 */

// 使用 call_user_func()、array_reduce()实现切面编程

interface Milldeware {
    public static function handle(Closure $next);
}
class VerfiyCsrfToekn implements Milldeware {
    public static function handle(Closure $next)
    {
        echo '验证csrf Token <br>';
        $next();
    }
}
class VerfiyAuth implements Milldeware {
    public static function handle(Closure $next)
    {
        echo '验证是否登录 <br>';
        $next();
    }
}
class SetCookie implements Milldeware {
    public static function handle(Closure $next)
    {
        $next();
        echo '设置cookie信息！';
    }
}
$handle = function() {
    echo '当前要执行的程序!';
};
$pipe_arr = [
    'VerfiyCsrfToekn',
    'VerfiyAuth',
    'SetCookie'
];
$callback = array_reduce($pipe_arr,function($stack,$pipe) {
    echo '<pre>';
    print_r($stack);
    echo $pipe;
    echo "<br/>";
    echo '</pre>';
    return function() use($stack,$pipe){
        return $pipe::handle($stack);
    };
},$handle);

echo '<pre>';
print_r($callback);
echo '</pre>';
call_user_func($callback);