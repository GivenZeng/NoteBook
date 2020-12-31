一般来说，我们的配置（如数据库、缓存）等会使用yaml进行配置。关于yaml的goalng解析库使用的是github上的

```
ymmq:
  db:
    db_driver: mysql
    db_dsn: "root:244143261@tcp(127.0.0.1:3306)/youmi_ad?strict=true"
  redis:
    - redis://127.0.0.1:6379/
watcher:
  pool:   //注意，git上面的是没有pool这元素的，直接将pool的子元素作为watcher的子元素
    db:
      db_driver: mysql
      db_dsn: "root:244143261@tcp(127.0.0.1:3306)/youmi_ad?strict=true"
    updateinterval: 2s
    syncinterval: 10s
    nthread: 8

```
