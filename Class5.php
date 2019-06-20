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
// array_reduce ( array $array , callable $callback [, mixed $initial = NULL ] ) 用法
// array
//
//    输入的 array。
//callback
//    callback ( mixed $carry , mixed $item ) : mixed
//
//    carry
//
//        携带上次迭代里的值； 如果本次迭代是第一次，那么这个值是 initial。
//    item
//
//        携带了本次迭代的值。
//
//initial
//
//    如果指定了可选参数 initial，该参数将在处理开始前使用，或者当处理结束，数组为空时的最后一个结果
$callback = array_reduce($pipe_arr,function($stack,$pipe) {
    echo '<pre>';
    print_r($stack);
    echo $pipe;
    echo "<br/>";
    echo '</pre>';
    // 此处的输出结果
    // 第一遍
//    Closure Object
//    (
//    )

//    第二遍
//    上一个闭包函数被单做参数出入 handle（）中
//    Closure Object
//    (
//        [static] => Array
//    (
//        [stack] => Closure Object
//    (
//    )
//
//    [pipe] => VerfiyCsrfToekn
//        )
//
//    )

//    第三遍
//    同理将第二遍的闭包传入 handle（）中
//    Closure Object
//    (
//        [static] => Array
//    (
//        [stack] => Closure Object
//    (
//        [static] => Array
//    (
//        [stack] => Closure Object
//    (
//    )
//
//    [pipe] => VerfiyCsrfToekn
//                        )
//
//                )
//
//            [pipe] => VerfiyAuth
//        )
//
//    )
    return function() use($stack,$pipe){
        return $pipe::handle($stack);
    };
},$handle);

echo '<pre>';
print_r($callback);
echo '</pre>';
call_user_func($callback);