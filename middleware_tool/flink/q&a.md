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

## union 和connect的区别

## connect 如何指定key
使用第一个stream的第二个字段和第二个stream的第一个字段进行connect
```java
stream1.connect(stream2).keyBy(1, 0)
```

亦或者keyBy(keySelector1, keySelector2)

## 如果和拆分datat stream？split、select。和fiter的区别？
- 使用split进行标记数据，一个数据可以有多个标记
- 使用select选择目标标记的数据

```java
val splited = stream.split(t => if (t._2%2 == 0) Seq("event") else Seq("odd"))
val odd  = splited.select("odd")
```

## iterate的作用
迭代操作数据知道满足某一个条件才发送给下游

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
val mapped = stream.map(t => t) // 这个map作妖是为了进行reblance
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

## sink
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
    "host:port", "topic", new SimpleStreamSchema()   
)
stream.addSink(producer)
```


但有时候上述的基础输出无法满足实际需求，可以自行基于SinkFunction进行实现。

## 时间类型有几种

## keyby的影响范围
## trigger的影响范围
