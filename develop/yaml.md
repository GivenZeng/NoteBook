* [资料](http://www.ruanyifeng.com/blog/2016/07/yaml.html)

## 简介
主要可以作为配置文件使用，如数据库的连接、缓存的配置等等

* 基本语法规则如下：
```
大小写敏感
使用缩进表示层级关系
缩进时不允许使用Tab键，只允许使用空格。
缩进的空格数目不重要，只要相同层级的元素左侧对齐即可
# 表示注释，从这个字符一直到行尾，都会被解析器忽略。
```

## 数据结构
* 支持的数据结构
  * 对象：即键值对
  * 数组
  * 纯量


### 对象
```
animal: pets
hash: { name: Steve, foo: bar }
```
### 数组
```
myarr:
  - Cat
  - Dog
  - Goldfish  
myarr2: [Cat, Dog]
```

### 纯量
```
number: 12.30
```

### 复合结构
```
languages:
 - Ruby
 - Perl
 - Python
websites:
 YAML: yaml.org
 Ruby: ruby-lang.org
 Python: python.org
 Perl: use.perl.org
```

## golang 解析库[go-yaml](https://github.com/go-yaml/yaml)

### 安装
```
go get gopkg.in/yaml.v2
```
### [文档](https://godoc.org/gopkg.in/yaml.v2)

### example
```
package main

import (
        "fmt"
        "log"

        "gopkg.in/yaml.v2"
)

var data = `
a: Easy!
b:
  c: 2
  d: [3, 4]
`

type T struct {
        A string
        B struct {
                RenamedC int   `yaml:"c"`
                D        []int `yaml:",flow"`
        }
}

func main() {
        t := T{}
        // 从字节slice反序列化到对象
        // 反序列化方法在我门实际中经常使用
        err := yaml.Unmarshal([]byte(data), &t)
        if err != nil {
                log.Fatalf("error: %v", err)
        }
        fmt.Printf("--- t:\n%v\n\n", t)
        //从对象到字节序列
        d, err := yaml.Marshal(&t)
        if err != nil {
                log.Fatalf("error: %v", err)
        }
        fmt.Printf("--- t dump:\n%s\n\n", string(d))

        //从字节序列到map
        m := make(map[interface{}]interface{})
        err = yaml.Unmarshal([]byte(data), &m)
        if err != nil {
                log.Fatalf("error: %v", err)
        }
        fmt.Printf("--- m:\n%v\n\n", m)
        //从map到字节序列
        d, err = yaml.Marshal(&m)
        if err != nil {
                log.Fatalf("error: %v", err)
        }
        fmt.Printf("--- m dump:\n%s\n\n", string(d))
}
```
