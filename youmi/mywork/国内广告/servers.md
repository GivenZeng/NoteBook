ssh -p 36000 ymserver@awscn-ma-01.umlife.net     jump#$%d00dleFAT  name code  

### adn机器
```
adn_ba_00       adn-cache_00    adn-cache_02    adn_gw_00       adn_gw_02       adn_gw_04       adn_gw_06       adn_log_00                                                                                         
adn_ba_01       adn-cache_01    adn-cache_03    adn_gw_01       adn_gw_03       adn_gw_05       adn_gw_test_00  adn_log_01 
```

- ba：代理，将外来请求分发到gw
- cache：主要是redis缓存
- gw：跑具体的服务，当前在线上提供服务的是00和01，test_00
- test：作为测试使用，但是并没有实际流量，不过当需要依赖其他服务的时候，可以在test连接依赖的服务

### gw机器
```
adn-adnow                        RUNNING   pid 84562, uptime 125 days, 2:07:47                           
alarm                            RUNNING   pid 1868, uptime 407 days, 6:48:41                            
carbon-aggregation               RUNNING   pid 1878, uptime 407 days, 6:48:41                            
cidproxy                         RUNNING   pid 40279, uptime 135 days, 2:19:45                           
clickcache                       RUNNING   pid 54549, uptime 5 days, 1:13:01                             
cpamedia                         RUNNING   pid 45647, uptime 27 days, 3:00:25                            
cpasrv                           RUNNING   pid 63375, uptime 12:15:07                                    
cpasrv-pure                      RUNNING   pid 118208, uptime 0:24:42                                    
ddbproxy                         RUNNING   pid 107766, uptime 371 days, 6:49:40                          
gogw-native                      RUNNING   pid 73378, uptime 84 days, 16:50:48                           
ipip-server                      RUNNING   pid 75527, uptime 133 days, 7:55:31                           
kafkaproxy                       RUNNING   pid 59822, uptime 244 days, 20:29:30                          
twemproxy                        RUNNING   pid 35541, uptime 296 days, 0:56:36                           
ymcaptcha                        RUNNING   pid 72280, uptime 84 days, 16:53:05                           
zbus-client                      RUNNING   pid 1874, uptime 407 days, 6:48:41                 
```


### log
```
adcpaupdater                     RUNNING   pid 28303, uptime 16:12:48                                                                                                          alarm                            RUNNING   pid 30224, uptime 3 days, 5:15:38                                                                                                                                       
check_sendcpc_task               RUNNING   pid 20611, uptime 112 days, 1:38:45                                                                                                                                     
cpasrv                           RUNNING   pid 11587, uptime 12:12:48                                                                                                                                              
ddbproxy                         RUNNING   pid 2274, uptime 477 days, 0:41:21                                                                                                                                      
dsp_source                       RUNNING   pid 6277, uptime 49 days, 2:30:34                                                                                                                                       
effect_device:00                 RUNNING   pid 21884, uptime 486 days, 6:29:44                                                                                                                                     
effect_device:01                 RUNNING   pid 21885, uptime 486 days, 6:29:44                                                                                                                                     
effect_device:02                 RUNNING   pid 21882, uptime 486 days, 6:29:44                                                                                                                                     
effect_device:03                 RUNNING   pid 21883, uptime 486 days, 6:29:44                                                                                                                                     
effect_device:04                 RUNNING   pid 21886, uptime 486 days, 6:29:44                                                                                                                                     
effect_device:05                 RUNNING   pid 21887, uptime 486 days, 6:29:44                                                                                                                                     
get_idfa                         RUNNING   pid 16626, uptime 233 days, 23:45:27                                                                                                                                    
limit_task                       RUNNING   pid 3788, uptime 283 days, 5:21:25                                                                                                                                      
mysql_notifier                   RUNNING   pid 1772, uptime 100 days, 5:16:00                                                                                                                                      
precharge2els                    STOPPED   May 22 11:43 AM                                                                                                                                                         
send_cpc                         RUNNING   pid 28445, uptime 16:12:46                                                                                                                                              
twemproxy                        RUNNING   pid 26487, uptime 538 days, 23:17:11                                                                                                                                    
ym_app_income_deduction          STOPPED   Jan 04 11:25 AM                                                                                                                                                         
youmi_tid                        RUNNING   pid 23944, uptime 328 days, 5:52:54                                                                                                                                     
zk_tasks:00                      RUNNING   pid 3349, uptime 170 days, 23:59:52                                                                                                                                     
zk_tasks:01                      RUNNING   pid 5433, uptime 170 days, 23:59:47 
```
