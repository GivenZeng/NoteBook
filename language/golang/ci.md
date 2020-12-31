## 检查无用代码
```
staticcheck --unused.whole-program=true -- ./... |grep -v thrift_gen |grep -v clients |grep unused
```
