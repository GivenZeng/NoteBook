```
type hchan struct {
    qcount   uint           // 队列中数据个数
    dataqsiz uint           // channel 大小
    buf      unsafe.Pointer // 存放数据的环形数组
    elemsize uint16         // channel 中数据类型的大小
    closed   uint32         // 表示 channel 是否关闭
    elemtype *_type // 元素数据类型
    sendx    uint   // send 的数组索引
    recvx    uint   // recv 的数组索引
    recvq    waitq  // 由 recv 行为（也就是 <-ch）阻塞在 channel 上的 goroutine 队列
    sendq    waitq  // 由 send 行为 (也就是 ch<-) 阻塞在 channel 上的 goroutine 队列

    // lock protects all fields in hchan, as well as several
    // fields in sudogs blocked on this channel.
    //
    // Do not change another G's status while holding this lock
    // (in particular, do not ready a G), as this can deadlock
    // with stack shrinking.
    lock mutex
}
type waitq struct {
    first *sudog
    last  *sudog
}
type sudog struct {
    // The following fields are protected by the hchan.lock of the
    // channel this sudog is blocking on. shrinkstack depends on
    // this for sudogs involved in channel ops.

    g          *g
    selectdone *uint32 // CAS to 1 to win select race (may point to stack)
    next       *sudog
    prev       *sudog
    elem       unsafe.Pointer // data element (may point to stack)

    // The following fields are never accessed concurrently.
    // For channels, waitlink is only accessed by g.
    // For semaphores, all fields (including the ones above)
    // are only accessed when holding a semaRoot lock.

    acquiretime int64
    releasetime int64
    ticket      uint32
    parent      *sudog // semaRoot binary tree
    waitlink    *sudog // g.waiting list or semaRoot
    waittail    *sudog // semaRoot
    c           *hchan // channel
}
```


我们可以看到 channel 其实就是一个队列加一个锁，只不过这个锁是一个轻量级锁。其中 recvq 是读操作阻塞在 channel 的 goroutine 列表，sendq 是写操作阻塞在 channel 的 goroutine 列表。列表的实现是 sudog，其实就是一个对 g 的结构的封装。

gopark 会将当前 goroutine 休眠，然后通过 unlockf 来唤醒，注意我们上面传入的 unlockf 是 nil，也就是向 nil channel 发送数据的 goroutine 会一直休眠。同理，从 nil channel 读数据也是一样的处理。

发送数据分三种情况：
- 有 goroutine 阻塞在 channel 上，此时 hchan.buf 为空：直接将数据发送给该 goroutine。
- 当前 hchan.buf 还有可用空间：将数据放到 buffer 里面。
- 当前 hchan.buf 已满：阻塞当前 goroutine。
