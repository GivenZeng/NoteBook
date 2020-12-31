* https://git-scm.com/book/zh/v1/Git-%E5%9F%BA%E7%A1%80-%E6%89%93%E6%A0%87%E7%AD%BE

同大多数 VCS 一样，Git 也可以对某一时间点上的版本打上标签。人们在发布某个软件版本（比如 v1.0 等等）的时候，经常这么做。本节我们一起来学习如何列出所有可用的标签，如何新建标签，以及各种不同类型标签之间的差别。

### 列出已有标签
```
git tag //列出现有标签
git tag -l 'reg'  //列出现有符合某正则的标签
```

### 查看标签的信息
```
git show tag_name
```

### 新建标签
```
git tag tag_name
git tag -a v1.4 -m 'my version 1.4'   //-a 标签名  ；-m 注释
```

### 分享标签
```
git push origin tag_name
```

### 签署
