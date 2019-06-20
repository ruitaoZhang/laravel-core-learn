<?php
/**
 * User: zrt
 * Date: 2019/6/20
 * Time: 10:51
 */

// 观察者描述
//观察者模式 (Observer), 当一个对象的状态发生改变时，依赖他的对象会全部收到通知，并自动更新。

//场景：一个事件发生后，要执行一连串更新操作。传统的编程方式，就是在事件的代码之后直接加入处理逻辑，当更新得逻辑增多之后，
//代码会变得难以维护。这种方式是耦合的，侵入式的，增加新的逻辑需要改变事件主题的代码

//观察者模式实现了低耦合，非侵入式的通知与更新机制

/**
 * 观察者接口类
 * Interface ObServer
 */
interface ObServer
{
    public function update($event_info = null);
}

/**
 * 观察者1
 */
class ObServer1 implements ObServer
{
    public function update($event_info = null)
    {
        // TODO: Implement update() method.
        echo "观察者1 收到消息，执行完毕！";
    }
}

/**
 * 观察者2
 */
class ObServer2 implements ObServer
{
    //此处的 $event_info 为事件的对象，如果事件有一些属性的话，可以通过它来访问
    public function update($event_info = null)
    {
        // TODO: Implement update() method.
        echo "观察者2 收到消息，执行完毕！";
    }
}

class Event
{
    public $ObServer;
    // 增加观察者（也就是监听器）
    public function add($obServer)
    {
        $this->ObServer[] = $obServer;
    }

    // 事件通知
    public function notify()
    {
        foreach ($this->ObServer as $ObServer){
            $ObServer->update();
        }
    }
    /**
     * 触发事件
     */
    public function trigger()
    {
        //通知观察者（监听器）
        $this->notify();
    }

}
// 创建一个事件
$event = new Event();
// 添加观察者
$event->add(new ObServer1());
$event->add(new ObServer2());
// 执行事件，通知观察者
$event->trigger();
