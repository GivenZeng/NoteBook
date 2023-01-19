基础概念：
```make
<target> : <prerequisites> 
[tab]  <commands>
```

其中target是自定义的，当执行“make $target”的时候，就会先检查依赖文件prerequisites，依赖满足，则执行命令$command。

比如我们文件 a.txt 的生成依赖于 b.txt 和 c.txt ，是后面两个文件连接（cat命令）的产物。那么，Makefile就如下：
```
build: b.txt c.txt
    cat b.txt c.txt > a.txt
```


像这样的规则，都写在一个叫做Makefile的文件中，Make命令依赖这个文件进行构建。Makefile文件也可以写为makefile， 或者用命令行参数指定为其他文件名。


```
clean:
    rm *.o
```
如果当前目录中，正好有一个文件叫做clean，那么这个命令不会执行。因为Make发现clean文件已经存在，就认为没有必要重新构建了，就不会执行指定的rm命令。为了避免这种情况，可以明确声明clean是"伪目标"，写法如下。声明clean是"伪目标"之后，make就不会去检查是否存在一个叫做clean的文件，而是每次运行都执行对应的命令。像.PHONY这样的内置目标名还有不少，可以查看手册。
```
.PHONY: clean
clean:
        rm *.o temp
```

## prerequisites 
前置条件prerequisites通常是一组文件名，之间用空格分隔。它指定了"目标"是否重新构建的判断标准：只要有一个前置文件不存在，或者有过更新（前置文件的last-modification时间戳比目标的时间戳新），"目标"就需要重新构建。
```
result.txt: source.txt
    cp source.txt result.txt
```

上面代码中，构建 result.txt 的前置条件是 source.txt 。如果当前目录中，source.txt 已经存在，那么make result.txt可以正常运行，否则必须再写一条规则，来生成 source.txt 。
```
source.txt:
    echo "this is the source" > source.txt
```

## 命令
命令（commands）表示如何更新目标文件，由一行或多行的Shell命令组成。它是构建"目标"的具体指令，它的运行结果通常就是生成目标文件。需要注意的是，每行命令在一个单独的shell中执行。这些Shell之间没有继承关系。
```
var-lost:
    export foo=bar
    echo "foo=[$$foo]"
```
上面代码执行后（make var-lost），取不到foo的值。因为两行命令在两个不同的进程执行。一个解决办法是将两行命令写在一行，中间用分号分隔;另一个解决办法是在换行符前加反斜杠转义;最后一个方法是加上.ONESHELL:命令。
```
var-kept:
    export foo=bar; echo "foo=[$$foo]"
```

## 变量
Makefile 允许使用等号自定义变量。调用时，变量需要放在 $() 之中。
```
txt = Hello World
test:
    @echo $(txt)
    @echo $$HOME
```
调用Shell变量，需要在美元符号前，再加一个美元符号，这是因为Make命令会对美元符号转义。

也可以传入参数到"make test OPT=xx"
```
test:
        @echo $(OPT)
```


Make命令还提供一些自动变量，它们的值与当前规则有关。主要有以下几个。
- $@指代当前目标，就是Make命令当前构建的那个目标。比如，make foo的 $@ 就指代foo。
```make
a.txt b.txt: 
    touch $@

# 同一下等价
a.txt:
    touch a.txt
b.txt:
    touch b.txt
```
- $< 指代第一个前置条件。比如，规则为 t: p1 p2，那么$< 就指代p1。
```
a.txt: b.txt c.txt
    cp $< $@ 
```
- $? 指代比目标更新的所有前置条件，之间以空格分隔。比如，规则为 t: p1 p2，其中 p2 的时间戳比 t 新，$?就指代p2。
- $^ 指代所有前置条件，之间以空格分隔。比如，规则为 t: p1 p2，那么 $^ 就指代 p1 p2 。

## 循环
见：https://www.w3cschool.cn/mexvtg/dsiguozt.html
## 函数
Makefile 还可以使用函数，格式如下。
```
$(function arguments)
# 或者
${function arguments}
```


## 实践
```
.PHONY: cleanall cleanobj cleandiff

cleanall : cleanobj cleandiff
        rm program

cleanobj :
        rm *.o

cleandiff :
        rm *.diff
```


```
edit : main.o kbd.o command.o display.o 
    cc -o edit main.o kbd.o command.o display.o

main.o : main.c defs.h
    cc -c main.c
kbd.o : kbd.c defs.h command.h
    cc -c kbd.c
command.o : command.c defs.h command.h
    cc -c command.c
display.o : display.c defs.h
    cc -c display.c

clean :
     rm edit main.o kbd.o command.o display.o

.PHONY: edit clean
```
# refer
[Make 教程](https://www.w3cschool.cn/mexvtg/sriygozt.html)