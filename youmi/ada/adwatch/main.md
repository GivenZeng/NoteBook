???????
```
if conf.Differ != nil {
  if adaWriter == nil {
    panic("DifferNotifier should initialize with ada.Writer")
  }
  dn := conf.Differ.New(adaWriter)
  qp.AddWork(dn, handler)
  migo.Panic(dn.Start())
  defer dn.Close()
}

// 全量同步
if conf.Fsync != nil {
  fsync, err := conf.Fsync.New()
  migo.Panic(err)
  qp.AddWork(fsync, handler)
  migo.Panic(fsync.Start())
  defer fsync.Close()
}

// 增量同步
if conf.Async != nil {
  async, err := conf.Async.New()
  migo.Panic(err)
  qp.AddWork(async, handler)
  migo.Panic(async.Start())
  defer async.Close()
}
```


为何增量同步具有多个实现
