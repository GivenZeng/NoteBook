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

