https://www.cnblogs.com/zhangboyu/p/7456606.html

### 介绍
context包的核心就是Context接口
```
type Context interface {
    Deadline() (deadline time.Time, ok bool)
    Done() <-chan struct{}
    Err() error
    Value(key interface{}) interface{}
}
```
* Deadline会返回一个超时时间，Goroutine获得了超时时间后，例如可以对某些io操作设定超时时间。
* Done方法返回一个信道（channel），当Context被撤销或过期时，该信道是关闭的，即它是一个表示Context是否已关闭的信号。
* 当Done信道关闭后，Err方法表明Context被撤的原因。
* Value可以让Goroutine共享一些数据，当然获得数据是协程安全的。但使用这些数据的时候要注意同步，比如返回了一个map，而这个map的读写则要加锁。


无论是Goroutine，他们的创建和调用关系总是像层层调用进行的，就像人的辈分一样，而更靠顶部的Goroutine应有办法主动关闭其下属的Goroutine的执行（不然程序可能就失控了）。为了实现这种关系，Context结构也应该像一棵树，叶子节点须总是由根节点衍生出来的。

要创建Context树，第一步就是要得到根节点，context.Background函数的返回值就是根节点：
```
func Background() Context
```
该函数返回空的Context，该Context一般由接收请求的第一个Goroutine创建，是与进入请求对应的Context根节点，它不能被取消、没有值、也没有过期时间。它常常作为处理Request的顶层context存在。

有了根节点，又该怎么创建其它的子节点，孙节点呢？context包为我们提供了多个函数来创建他们：
```
func WithCancel(parent Context) (ctx Context, cancel CancelFunc)
func WithDeadline(parent Context, deadline time.Time) (Context, CancelFunc)
func WithTimeout(parent Context, timeout time.Duration) (Context, CancelFunc)
func WithValue(parent Context, key interface{}, val interface{}) Context
```

### 超时限制
WithCancel ，手动取消
```
func longRunningCalculation(timeCost int)chan string{

    result:=make(chan string)
    go func (){
        time.Sleep(time.Second*(time.Duration(timeCost)))
        result<-"Done"
    }()
    return result
}
func jobWithCancelHandler(w http.ResponseWriter, r * http.Request){
    var ctx context.Context
    var cancel context.CancelFunc
    //获取一个可以手动取消的上下文
    ctx,cancel = context.WithCancel(r.Context())
    defer cancel()
    //5s 后取消该上下文
    go func(){
        time.Sleep(5*time.Second)
        cancel()
    }()
    //等待完成信号或者超时信号
    select{
    case <-ctx.Done():
        log.Println(ctx.Err())
        return
    case result:=<-longRunningCalculation(timecost):
        io.WriteString(w,result)
    }
    return
}
```

输出：
```
doing a long job
get a over time signal
finish the job
terminate the main function
```


### WithTimeOut
```
package main

import (
	"context"
	"fmt"
	"time"
)

func main() {
	// 创建一个子context，并且五秒后超时，五秒后回自动cancel
	ctx, _ := context.WithTimeout(context.Background(), 5*time.Second)
	// 将子routine的结果通过result返回给父routine
	result := make(chan string)
	// 使用子routine开始一个耗时工作
	go func() {
		fmt.Println("doing a long job")
		time.Sleep(time.Second * 10)
		fmt.Println("finish the job")
		result <- "done"
	}()
	// 等待结果或者超时信号
	select {
	case <-ctx.Done():
		fmt.Println("get a over time signal")
	case <-result:
		fmt.Println("finished a long job")
	}
	// 父routine再继续其他操作
	time.Sleep(time.Second * 10)
	// 父routine运行结束
	fmt.Println("terminate the main function")
}       
```

输出：
```
doing a long job
get a over time signal
finish the job
terminate the main function
```
可见即使任务超时或者cancel被调用，子routine也会执行直至结束，只是父routine不再等待和接收子routine的执行结果
