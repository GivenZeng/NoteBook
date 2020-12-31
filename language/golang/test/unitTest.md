```
// 测试zjw_test，zjw_test依赖init.go中的函数
go test  -v db/zjw_test.go db/init.go
go test  -run Zjw -v db/zjw_test.go db/init.go // 只运行TestZjw
```

- https://github.com/astaxie/build-web-application-with-golang/edit/master/zh/11.3.md
- https://golang.org/pkg/testing/
# 11.3 Go怎么写测试用例
开发程序其中很重要的一点是测试，我们如何保证代码的质量，如何保证每个函数是可运行，运行结果是正确的，又如何保证写出来的代码性能是好的，我们知道单元测试的重点在于发现程序设计或实现的逻辑错误，使问题及早暴露，便于问题的定位解决，而性能测试的重点在于发现程序设计上的一些问题，让线上的程序能够在高并发的情况下还能保持稳定。本小节将带着这一连串的问题来讲解Go语言中如何来实现单元测试和性能测试。

Go语言中自带有一个轻量级的测试框架`testing`和自带的`go test`命令来实现单元测试和性能测试，`testing`框架和其他语言中的测试框架类似，你可以基于这个框架写针对相应函数的测试用例，也可以基于该框架写相应的压力测试用例，那么接下来让我们一一来看一下怎么写。

另外建议安装[gotests](https://github.com/cweill/gotests)插件自动生成测试代码:

```Go
go get -u -v github.com/cweill/gotests/...

```

## 如何编写测试用例
由于`go test`命令只能在一个相应的目录下执行所有文件，所以我们接下来新建一个项目目录`gotest`,这样我们所有的代码和测试代码都在这个目录下。

接下来我们在该目录下面创建两个文件：gotest.go和gotest_test.go

1. gotest.go:这个文件里面我们是创建了一个包，里面有一个函数实现了除法运算:

```Go

	package gotest
	
	import (
		"errors"
	)
	
	func Division(a, b float64) (float64, error) {
		if b == 0 {
			return 0, errors.New("除数不能为0")
		}
	
		return a / b, nil
	}

```

- gotest_test.go:这是我们的单元测试文件，但是记住下面的这些原则：
	- 文件名必须是`_test.go`结尾的，这样在执行`go test`的时候才会执行到相应的代码
	- 你必须import `testing`这个包
	- 所有的测试用例函数必须是`Test`开头
	- 测试用例会按照源代码中写的顺序依次执行
	- 测试函数`TestXxx()`的参数是`testing.T`，我们可以使用该类型来记录错误或者是测试状态
	- 测试格式：`func TestXxx (t *testing.T)`,`Xxx`部分可以为任意的字母数字的组合，但是首字母不能是小写字母[a-z]，例如`Testintdiv`是错误的函数名。
	- 函数中通过调用`testing.T`的`Error`, `Errorf`, `FailNow`, `Fatal`, `FatalIf`方法，说明测试不通过，调用`Log`方法用来记录测试的信息。
	
	下面是我们的测试用例的代码：
	
```Go

	package gotest
	
	import (
		"testing"
	)
	
	func Test_Division_1(t *testing.T) {
		if i, e := Division(6, 2); i != 3 || e != nil { //try a unit test on function
			t.Error("除法函数测试没通过") // 如果不是如预期的那么就报错
		} else {
			t.Log("第一个测试通过了") //记录一些你期望记录的信息
		}
	}
	
	func Test_Division_2(t *testing.T) {
		t.Error("就是不通过")
	}

```

	我们在项目目录下面执行`go test`,就会显示如下信息：

		--- FAIL: Test_Division_2 (0.00 seconds)
			gotest_test.go:16: 就是不通过
		FAIL
		exit status 1
		FAIL	gotest	0.013s
	从这个结果显示测试没有通过，因为在第二个测试函数中我们写死了测试不通过的代码`t.Error`，那么我们的第一个函数执行的情况怎么样呢？默认情况下执行`go test`是不会显示测试通过的信息的，我们需要带上参数`go test -v`，这样就会显示如下信息：
	
		=== RUN Test_Division_1
		--- PASS: Test_Division_1 (0.00 seconds)
			gotest_test.go:11: 第一个测试通过了
		=== RUN Test_Division_2
		--- FAIL: Test_Division_2 (0.00 seconds)
			gotest_test.go:16: 就是不通过
		FAIL
		exit status 1
		FAIL	gotest	0.012s
	上面的输出详细的展示了这个测试的过程，我们看到测试函数1`Test_Division_1`测试通过，而测试函数2`Test_Division_2`测试失败了，最后得出结论测试不通过。接下来我们把测试函数2修改成如下代码：

```Go

	func Test_Division_2(t *testing.T) {
		if _, e := Division(6, 0); e == nil { //try a unit test on function
			t.Error("Division did not work as expected.") // 如果不是如预期的那么就报错
		} else {
			t.Log("one test passed.", e) //记录一些你期望记录的信息
		}
	}	
```		
	然后我们执行`go test -v`，就显示如下信息，测试通过了：
	
		=== RUN Test_Division_1
		--- PASS: Test_Division_1 (0.00 seconds)
			gotest_test.go:11: 第一个测试通过了
		=== RUN Test_Division_2
		--- PASS: Test_Division_2 (0.00 seconds)
			gotest_test.go:20: one test passed. 除数不能为0
		PASS
		ok  	gotest	0.013s

## 如何编写压力测试
压力测试用来检测函数(方法）的性能，和编写单元功能测试的方法类似,此处不再赘述，但需要注意以下几点：

- 压力测试用例必须遵循如下格式，其中XXX可以是任意字母数字的组合，但是首字母不能是小写字母

```Go
	func BenchmarkXXX(b *testing.B) { ... }
```

- `go test`不会默认执行压力测试的函数，如果要执行压力测试需要带上参数`-test.bench`，语法:`-test.bench="test_name_regex"`,例如`go test -test.bench=".*"`表示测试全部的压力测试函数
- 在压力测试用例中,请记得在循环体内使用`testing.B.N`,以使测试可以正常的运行
- 文件名也必须以`_test.go`结尾

下面我们新建一个压力测试文件webbench_test.go，代码如下所示：

```Go

package gotest

import (
	"testing"
)

func Benchmark_Division(b *testing.B) {
	for i := 0; i < b.N; i++ { //use b.N for looping 
		Division(4, 5)
	}
}

func Benchmark_TimeConsumingFunction(b *testing.B) {
	b.StopTimer() //调用该函数停止压力测试的时间计数

	//做一些初始化的工作,例如读取文件数据,数据库连接之类的,
	//这样这些时间不影响我们测试函数本身的性能

	b.StartTimer() //重新开始时间
	for i := 0; i < b.N; i++ {
		Division(4, 5)
	}
}

```

我们执行命令`go test webbench_test.go -test.bench=".*"`，可以看到如下结果：
```
Benchmark_Division-4   	                     500000000	      7.76 ns/op	     456 B/op	      14 allocs/op
Benchmark_TimeConsumingFunction-4            500000000	      7.80 ns/op	     224 B/op	       4 allocs/op
PASS
ok  	gotest	9.364s
```

上面的结果显示我们没有执行任何`TestXXX`的单元测试函数，显示的结果只执行了压力测试函数，第一条显示了`Benchmark_Division`执行了500000000次，每次的执行平均时间是7.76纳秒，第二条显示了`Benchmark_TimeConsumingFunction`执行了500000000，每次的平均执行时间是7.80纳秒。最后一条显示总共的执行时间。


如果你的测试用例需要做一些耗时的初始化操作，但是又不想将这些初始化的时间耗费算在基准测试里面，那么你可以使用b.RestTimer 或者如上例般先Stop再Start

### 并行测试
以并行的方式执行给定的基准测试。 RunParallel 会创建出多个 goroutine ， 并将 b.N 个迭代分配给这些 goroutine 执行， 其中 goroutine 数量的默认值为 GOMAXPROCS 。 用户如果想要增加非CPU受限（non-CPU-bound）基准测试的并行性， 那么可以在 RunParallel 之前调用 SetParallelism 。 RunParallel 通常会与 -cpu 标志一同使用。

body 函数将在每个 goroutine 中执行， 这个函数需要设置所有 goroutine 本地的状态， 并迭代直到 pb.Next 返回 false 值为止。 因为 StartTimer 、 StopTimer 和 ResetTimer 这三个函数都带有全局作用， 所以 body 函数不应该调用这些函数； 除此之外， body 函数也不应该调用 Run 函数。
```
func (b *B) RunParallel(body func(*PB))
```

example
```
package main

import (
	"bytes"
	"testing"
	"text/template"
)

func main() {
	// Parallel benchmark for text/template.Template.Execute on a single object.
	testing.Benchmark(func(b *testing.B) {
		templ := template.Must(template.New("test").Parse("Hello, {{.}}!"))
		// RunParallel will create GOMAXPROCS goroutines
		// and distribute work among them.
		b.RunParallel(func(pb *testing.PB) {
			// Each goroutine has its own bytes.Buffer.
			var buf bytes.Buffer
			for pb.Next() {
				// The loop body is executed b.N times total across all goroutines.
				buf.Reset()
				templ.Execute(&buf, "World")
			}
		})
	})
}
```

如果你的一个测试文件里面有多个测试函数，但是你只想运行其中某些测试，那么你无须将不想运行的测试注释，只需在其开始加入Skip
```
func (c *T) Skip(args ...interface{}) //直接跳过该测试，不在运行该测试中Skip后面的代码，并且不认为该测试失败
func (c *T) SkipNow() //
func (c *T) Skipf(format string, args ...interface{})

B跳过测试的函数签名相同，在此不赘诉

```

TB 类型同时拥有 T 类型和 B 类型提供的接口。
```
package nterface {
    Error(args ...interface{})
    Errorf(format string, args ...interface{})
    Fail()
    FailNow()
    Failed() bool
    Fatal(args ...interface{})
    Fatalf(format string, args ...interface{})
    Log(args ...interface{})
    Logf(format string, args ...interface{})
    Name() string
    Skip(args ...interface{})
    SkipNow()
    Skipf(format string, args ...interface{})
    Skipped() bool
    // contains filtered or unexported methods
}main
```
## 小结
通过上面对单元测试和压力测试的学习，我们可以看到`testing`包很轻量，编写单元测试和压力测试用例非常简单，配合内置的`go test`命令就可以非常方便的进行测试，这样在我们每次修改完代码,执行一下go test就可以简单的完成回归测试了。



