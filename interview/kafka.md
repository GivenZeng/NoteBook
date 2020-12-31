https://blog.csdn.net/qq_28900249/article/details/90346599


kafka实现精确一次：atleast once, exactly once。
- 从生产者的角度看：
    - atleast once：先处理完再commit，需要生产者的逻辑
    - exactly once：
        - 生产者可能重复写入，需要考虑生产者幂等性
            - 每个producer都会被分配一个pid，每个生产者对于每个topic、partition都有一个自动的seq，其生产的数据的seq必须是递增的，否则broker会拒绝写入
            - ack 机制
            - 原子性和事务：
                - 使用了事务协调器，原子性标记事务状态
                - 使用transaction log（start - prepare- mark -commit/abort ）做事务提交，类似于二阶段提交
            - 得益于offset这一概念，允许从某个offset重新开始
        - 消费者可能重复消费，需要考虑消费者唯一
        - 部分如实现kafka connector接口的下游，可以实现幂等