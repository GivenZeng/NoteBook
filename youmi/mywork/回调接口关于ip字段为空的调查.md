awsjp-ma 是跳板机器

ssh log_0

/data1/data/log/api/v2/ac226


crontab -l|grep 'sync'

/data1/data/log/api/v2/ac226
日志目录中，实时保持3min的日志在xxxxxx.kafka中

/data1/bak/api/v2/ac226 中记录了最近三天的日志

工作：实时记录中存在ip字段，但在redash中不存在


log transformer:将config.LOG_BAK_PATH(/data2/bak)下的小时日志转换成avro上传到S3
检测结果：log_transformer中，ip在转化后并没有丢失

数据存储
adn_avro.gateway_ocean_api_avro 存储最近两天的数据
adn_avro.gateway_ocean_api   存储以前的数据

select ip from adn_avro.gateway_ocean_api_avro limit 200  //结果，ip存在
select ip from adn.gateway_ocean_api  limit 200 //结果：ip存在
select ip from adn_avro.gateway_ocean_api_avro limit 200000   // ip存在

select ip,acn from adn.gateway_ocean_api  where acn=226 limit 200//ip存在，acn=226
select ip,acn from adn_avro.gateway_ocean_api_avro  where acn=226 limit 200000//ip存在，acn=226


select ip,acn from adn.gateway_ocean_api where acn=226 order by rand() limit 100//基本存在ip


select ip,acn from adn_avro.gateway_ocean_api_avro where acn=226 and `date`='2017-12-22' and ip=''//共425条数据
select ip,acn from adn_avro.gateway_ocean_api_avro where acn=226 and `date`='2017-12-22'  //共20407条数据
select ip,code,acn from adn_avro.gateway_ocean_api_avro where acn=226 and `date`='2017-12-22' and ip='' //共425条ip
为空的数据，大部分的code为-4001、-4002、-4003


select code,count(code) from adn_avro.gateway_ocean_api_avro where acn=226 and `date`='2017-12-22' and ip='' group by code //ip为空的425条数据中，code=-4003的有21条，占5%；code=-4002 的有48条，占11%；code=-4001的有356条，占84%


select ip,acn from adn.gateway_ocean_api where acn=226 and `date`='2017-12-22' and ip='' //共336条数据
select ip,acn from adn.gateway_ocean_api where acn=226 and `date`='2017-12-22'  //共17727条数据
问题：

* 数据都是使用parquet存储？ 条件查询慢


总结：数据基本正常
