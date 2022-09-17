## keyBy和grouyBy的区别
- keyBy用于StreamData，groupBy用于Dataset


## 如何使用复杂类型或者联合字段作为key
使用使用KeySelector

## 流式和dataset execute的区别
- dataset 的输出算子已经包含了对execute的调用，因此无需显示调用execute

## flink有哪些数据类型
- TypeInfomation：主要用于数据结构类。是Flink类型系统的核心，是生成序列化/反序列化工具和Comparator的工具类。在java api中，Flink无法准确的获取到数据类型。因此，在使用Java API的时候，我们需要手工指定类型。使用Scala的时候无需指定。
- BasicTypeInfo：原生数据类型，Integer、String等
- BaseArrayTypeInfo：原生数组类型
- TubleTypeInfo
- CaseClassTypeInfo



## source有哪些
flink自带了一些基础source：
- 文件，比如普通文本、csv文本等等。
  - 可以指定文件读取类型watchType，PROCESS_CONTINOUSLY 一旦文件内容发生变化，就重新读取整个文件；PROCESS_ONCE 表示当文件内容发生变化，仅将变化的内容读取到flink
  - 可以指定interval，每隔interval检查一遍文件是否被修改
- socket
- 集合
- 外部数据源，其本质是实现了自定义的SourceFunction
```java
// read by line
val stream = env.readTextFile("your file path")

// read csv
val stream = env.readCsvFile[(String,Long,Integer)]("file path")

// read socket, you can use: nc -lk 90000
val stream = env.socketTextStream("your host", 9000)

// collection
val stream = env.fromElements(1,2,3,4)
val stream = env.fromCollection(Arrays.asList(new String[]{"hello", "flink"}))

// external, kafka for example. 不同版本的kafka需要的参数不同，请参考flink文档
Propertities pro = new Protities()
pro.setProperty("bootstrap.servers", "host:9092")
pro.setProperty("zookeeper.connect", "host:2181")
pro.setProperty("group.id", "your consumer group")

val schema = new SimpleStreamSchema() // 指定schema，如果需要个性化，可以自行实现DeserializationSchema接口
val stream = env.addSource(new FlinkKafkaConsumer010<>("your topic", schema, pro))
```



## 转换操作
- map
- flatmap
- keyBy, 比如根据数据的某个基础类型字段来keyBy，如果非基础类型，那么需要复写hashCode；不支持根据数组等进行keyBy。运算之后得到KeyedStream
- reduce，对KeyedStream使用传入的ReduceFunction进行处理。ReduceFunction必须满足结合律和交换律。
```java
// 根据id进行key by，随后计算同一个id的value之和
// 比如计算每个用户的消费总额
val keyed = stream1.keyBy(v => v.user_id)
val reduced = keyed.reduce((v1, v2) => (v1.user_id, v1.cost + v2.cost) )
```
- Aggregations：是聚合算子，其实是将Reduct算子中的函数进行封装，有sum、min、minBy、max、maxBy
  - min会根据指定的字段取最小值，并且把这个值保存在对应的位置上，对于其他的字段取了最先获取的值，不能保证每个元素的数值正确，max同理。
  - minBy会返回指定字段取最小值的元素，并且会覆盖指定字段小于当前已找到的最小值元素。maxBy同理。
```java
// 根据id进行key by，随后计算同一个id的value之和
// 比如计算每个用户的消费总额
val stream1 = env.fromElements((1, 100), (1, 200), (2, 500))
val keyed = stream1.keyBy(0)
val agged = keyed.sum(1) // agged的类型是DataStream[(Int, Int)]
```

## union 和connect的区别
- union是将两个类型一样的流组合起来，得到一个新的DataStream
- connect是将两个类型不一样的流水根据某条件组合起来，得到一个ConnectedStream。
  - 如果需要按某个条件join，可以将两个流分别keyBy之后，再进行connect
```java
// stream1/2都是DataStream[T]的，即元素类型相同
val unioned = stream1.union(stream2)
```

## connect 如何指定key
使用第一个stream的第二个字段和第二个stream的第一个字段进行connect
```java
// 亦或者keyBy(keySelector1, keySelector2)
stream1.connect(stream2).keyBy(1, 0)
```


## 如果和拆分datat stream？split、select。和fiter的区别？
- 使用split进行标记数据，一个数据可以有多个标记
- 使用select选择目标标记的数据

```java
val splited = stream.split(t => if (t._2%2 == 0) Seq("event") else Seq("odd"))
val odd  = splited.select("odd")
```

## iterate的作用
迭代操作数据直到满足某一个条件才发送给下游

```java
val iteratedStream = someDataStream.iterate(
  iteration => {
    val iterationBody = iteration.map(/* this is executed many times */)
    // 返回一个tuple，tuple.0是进入下一个迭代的数据，tuple.1为进入下游的数据
    (iterationBody.filter(/* one part of the stream */), iterationBody.filter(/* some other part of the stream */))
}, 1000)
```

样例，比如我们需要将每个数据减至小于2才允许进入下游
```java
val stream = env.fromElements(1, 3, 4, 6, 7)
val mapped = stream.map(t => t) // 这个map作用是为了进行reblance
val iterated = mapped.iterate(
    iteration => {
        val iterationBody = iteration.map(t => if (t < 2) t else t -1)
        // tuple.0是进入下一个迭代的数据，tuple.1为进入下游的数据
        (iterationBody.filter(t >= 2), iterationBody.filter(t < 2))
    }
)
```

## flink有那些分区器/partition
- 随机分区，相对均衡，但是容易失去原有数据的分区结构
```java
val shuffled = stream1.shuffle()
```
- Roundrobin: 对全局循环分区，尽可能平衡
```java
val shuffled = stream1.rebalance()
```
- rescaling partition：针对上游循环分区。比如上游有两个并发度，其中一个分区有4k条记录，另一个分区有8k条记录；下游有四个分区。上游第一个分区的4k条记录会循环分到下游的四个分区；第二个同理。相对于Roundrobin，这种方式可以减少上游分区间的通信
```java
val shuffled = stream1.rescale()
```
- broadcasting：将数据全部复制到下游算子的并行的tasks实力中，下游算子可以直接从本地内存获取数据，不再依赖网络传输。这种策略适合于小数据集，比如当大数据集join小数据集，通过这种方式可以减少网络消耗
```java
val shuffled = stream1.boradcast()
val joined = stream2.join(shuffled).where(keySelector1).equalTo(keySelector2)
```
- 自定义分区，实现自定义的Partitioner接口。比如某个用户的数据量比较大，那么我们可以指定分区0和1来处理该用户的数据
```java
object customPartitioner extends Partitioner[String]{
    val r = scala.util.Random
    override def partition(key: String, numPartitions: Int): Int = {
        if (key=="1" && numPartitions > 2) r.nextInt(2) else r.nextInt(numPartitions)
    }
}

// 指定使用user_id 进行分区
stream1.partitionCustom(customPartitioner, "user_id")
```



## sink哪些
flink自带了一些基础的sink：
```java
stream.writeAsCsv("/tmp/result.csv")
stream.writeAsCsv("/tmp/result.csv", WriteMode.OverWrite)
stream.writeAsText("/tmp/result.txt)
stream.writeToSocket(host, port, new SimpleStreamSchema()) // 指定schema
```

比如写入kafka
```java
val producer = new FlinkKafkaProducer011[String](
    "hostx:port", "topic", new SimpleStreamSchema()   
)
stream.addSink(producer)
```


但有时候上述的基础输出无法满足实际需求，可以自行基于SinkFunction进行实现。


## 时间类型有几种
- event time：事件时间，一般为事件的某个字段，比如广告点击时间、用户支付时间等等
- ingestion time：接入时间，数据经过data source 接入的时候生成的时间
- process time：算子处理数据时的时间

比如一个广告点击在早上10:00（event time）产生，在10:05（ingestion）被消费进flink，在10:10（process time）被某个算子（比如map）处理。

flink 默认情况下使用的process time时间概念，但是在业务中，我们一般是用event time，比如统计每个小时的广告点击量、用户登录量等等，这个时候就要指定时间类型
```java
evn.setStreamTimeCharacteristic(TimeCharacteristic.EventTime)
```

## watermark
得到时间后，我们可以根据时间生成watermark。flink会将最新的时间减去固定间隔作为water mark，这个间隔表示支持最大延迟到达时间，也就是说理论上认为不会有时间超过该间隔到达，否则就被认为是迟到事件或者异常事件。迟到事件或者异常事件会被丢弃或者进行旁路处理。

比如flink消费的最新事件为10:10，间隔为10min，那么就认为10:00前的数据都已经到达了。假如在之后，还消费出现了10:00之前的数据，那将被认为迟到事件或者异常事件。

在顺序事件中（事件时间是递增的），那就一定不会出现迟到或者异常事件，此时watermark其实不能发挥太刀的价值。

对于并行数据流中的watermark，每个source operator都会生成独立的watermark，当有新事件到达，就会自动更新watermark。

当window的end小于watermark，就会触发窗口的计算。

### 在source function中设定watermark
对于自定义source，我们可以指定event timestamp（ms），用户需要自行实现SourceFunction，并在SourceFunction的run中调用SourceContext的collectWithTimestamp方法生成时间戳，调用emitWatermark方法生成新的watermark。

```java
// 时间类型(String, Int, Long)，表示用户名、订单金额、订单支付时间ms
val source = new SourceFunction[(String, Int, Long)](
    override def run(ctx: SourceContext[(String, Int, Long)]: Unit ={
        input.foreach(e => {
            ctx.collectWithTimestamp(e._3)
            ctx.emitWatermark(e._3 - 60*1000) // 延迟时间为60s
        })
        // 设置默认watermark
        ext.emitWatermark(new Watermark(Long.MaxValue))
    })

    override def cancel(): Unit={}
)

val stream1 = env.addSource(source)
```

### 通过timestamp assigner指定时间和生成watermark
timestamp assigner一般在data source 算子后指定，也可以在后续算子中指定，只要保证在第一个和时间相关的operator 之前即可。timestamp assigner会负载source function中的默认逻辑。

flink将watermark 根据生成形式分为两种类型：Periodic watermark和Punctuated watermark：
- Punctuated watermark： 根据数据数量生成watermark，比如每10条数据就进行一次聚合/reduce
- Periodic watermark: 根据时间间隔周期性生成watermark。分为升序和乱序两种
  - 升序：事件时间有序
  - 乱序：事件事件无序，可以指定容忍延迟间隔

```java
// 时间类型(String, Int, Long)，表示用户名、订单金额、订单支付时间ms
val stream: DataStream[(String, Int, Long)] = ...

// 升序
val asce: DataStream[(String, Int, Long)] = stream.assignAscendingTimestamps(e => e._3)
// 乱序
val outOfOder: DataStream[(String, Int, Long)] = stream.assignTimestampAndWatermarks(
    new BoundedOutOfOrderTimestampExtractor[(String, Int, Long)](Time.seconds(60))
        override def extraeTimestamp(e: (String, Int, Long)): Long = {
            e._3
        }
    )
```

上例中的两种方式已经能基本满足常见需求了，如果需要自定义Periodic watermark生成器，那可以自行实现AssignerWithPeriodicWatermark或者AssignerWithPunctuatedWatermarks，亦或者自行实现WatermarkGenerator
```java
ExecutionConfig.setAutoWatermarkInteraval(1000) // 每1s生成watermark，即每1s调用onPeriodicEmit

val wg =  new WatermarkGenerator[(String, Int, Long)]{
    val maxOutOfOrderness = 3500L // 3.5 seconds
    var currentMaxTimestamp: Long = _
    override def onEvent(element: (String, Int, Long), eventTimestamp: Long, output: WatermarkOutput): Unit = {
        currentMaxTimestamp = max(eventTimestamp, currentMaxTimestamp)
    }

    override def onPeriodicEmit(output: WatermarkOutput): Unit = {
        // emit the watermark as current highest timestamp minus the out-of-orderness bound
        output.emitWatermark(new Watermark(currentMaxTimestamp - maxOutOfOrderness - 1))
    }
}
```

## window/窗口
有了watermark后，我们就可以指定时间窗口，当watermark到达window.getEnd就触发计算。比如统计每小时的每个用户支付金额。

常见的操作如下：
```java
stream.keyBy(...)
.window(...)
[.trigger(...)]
[.evitor(...)]
[.allowedLateness(...)]
[.sideOutputLateDate(...)]
.reduce/aggregate/fold/apply()
[.getSideOutput(...)]
```
以上的trigger、evictor、allowedLateness、sideOutputLateDate、getSideOutput都是可选操作
- window：指定窗口类型，定义如何将数据流分到一个窗口，比如tumbling window、sliding window等
- trigger：何时触发窗口计算。如果不指定，则当watermark到达window.getEnd时触发
- evictor：数据剔除
- lateness：标记是否处理迟到数据，当迟到数据到达窗口是否触发窗口计算
- sideOutputLateDate：如何处理迟到的数据
- reduce/aggregate/fold/apply/process：指定窗口计算方式
- getSideOutput：获取迟到的数据

### window assigner
对于keyedStream和dataStream，需要使用不同的assigner来指定窗口
```java
val w = keyed.window(windowAssigner)

val w = dataStream.windowAll(windowAllAssigner)
```

常见的windown类型在flink中都已经有实现：
- tumbling window：滚动窗口，根据固定时间/大小划分，窗口之间不重叠
- sliding window：滑动窗口，根据大小和
- session window：会话窗口
- global window：全局窗口

```java
// 滚动，1 min
// 根据不同时间类型有TumblingEventTimeWindows和TumblingProcessTimeWindows
// 以下两种窗口指定方式是等价的
val tumble = keyed.timeWindow(Time.seconds(60))
val tumble = keyed.window(TumblingEventTimeWindows.of(Time.seconds(60)))

// 滑动，窗口大小1 min，滑动步伐为10s
// 根据不同时间类型有SlidingEventTimeWindows和SlidingProcessTimeWindows
// 以下两种窗口指定方式是等价的
val slide = keyed.timeWindow(Time.seconds(60), Time.seconds(10))
val slide = keyed.window(SlidingEventTimeWindows.of(Time.seconds(60), Time.seconds(10)))

// 会话，session gap为1 min
// 根据不同时间类型有EventTimeSessionWindows和ProcessTimeSessionWindows
val session = keyed.window(EventTimeSessionTimeWindows.withGap(Time.seconds(60)))
// 也可设置动态gap，比如对于不同的用户设置不同的session gap，比如对于用户1的gap为60s，其他用户10s
// 只需要实现SeesionWindowTimeGapExtractor接口
// 然后传入EventTimeSessionWindows或ProcessTimeSessionWindows的withDynamicGap方法
val session = keyed.window(EventTimeSessionWindows.withDynamicGap(
    new SeesionWindowTimeGapExtractor[(String, Int, Long)]{
        override def extract(e: (String, Int, Long)): Long = {
            if (e._1 == "user 1"){
                60 * 1000L
            }else{
                10 * 1000L
            }
        }
    }
))

// 全局窗口将所有相同key的数据分配到单个窗口计算结果，这种方式对内存占用会比较大
// 窗口没有开始/结束时间，必须借助trigger来触发计算
val w = keyed.window(GlobalWindows.create()).tigger(...)
```

针对none keyed，我们用的是全局窗口：
```java
```

### window function 窗口计算
常见的窗口计算有reduce、aggregate、process、fold（已废弃）
```java
val result = w.reduce((e1, e2) => {...})

// reduce 是对aggregate的封装，适用于逻辑较为简单的计算
// 如果计算较为复杂，可以使用aggregate
val result = w.aggregate(
    new AggregateFunction[Event, Acc, Result]{
        // 初始化计数器
        override def createAccumulator(): Acc = {...}
        // 将事件累加到计数器
        override def add(e: Event, acc: Acc): Unit = {...}
        // 合并不同的计数器
        override def merge(acc1: Acc, acc2: Acc) = {}
        // 获取计数器最终结果
        override def getResult(acc: Acc): Result = {...}
    }
)

// 前面的reduce和aggregate已经能满足大部分场景了
// 但是部分场景需要处理窗口的每一个元素或者依赖所有元素进行计算
// 计算每个省份的工资中位数
// 这种情况可以自定义窗口处理函数，只需要实现ProcessWindowFunction[In, Out, Key, W extends Window]，然后传入process即可
// 附：ProcessWindowFunction是对AbstractRrichFunction的继承
// 返回每个用户的支付金额中位数，(String, Int, Long)表示用户id、金额、支付时间：
val processed = keyed.porcess(
    new ProcessWindowFunction[(String, Int, Long), (String, Int, Long), String, TimeWindow]{
        override def process(key: String, ctx: Context, 
            eventIter: Iterable[(String, Int, Long)], out: Collector[(String, Int, Long)]): Unit = {
            val mid = findMiddle(eventIter)
            out.collect((key, mid, ctx.window.getEnd))
        }
    }
)
```

相较于process需要缓存所有元素，reduce/aggregate这些增量聚合函数能在一定程度上提升窗口计算性能。对于部分情形，我们可以结合二者的优势，比如计算窗口的最大值和窗口的结束时间:
```java
val keyed: KeyedStream[Event] = source.keyBy(...)
val largest = keyed.reduce(
    (e1, e2 => myMax(e1, e2)), // myMax 实现自定义的reduce
    (key: String, window: TimeWindow, maxIter: Iterator[Event], out: Collector[(Event, Log)]) => {
        val min = maxIter.iterator.next()
        out.collect(min, window.getEnd)
    }
)
```

## keyby的影响范围
## trigger的影响范围
