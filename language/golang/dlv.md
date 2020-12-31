- https://juejin.im/entry/5aa1f98d6fb9a028c522c84b
- http://lday.me/2017/02/27/0005_gdb-vs-dlv/

```
// -- 后面为需要传到project的参数
// exec用于运行bin文件
cd project_pwd
dlv exec output/bin/ad.game.open_platform -- -psm=ad.game.open_platform -conf-dir=output/conf/ -log-dir=output/log
```
