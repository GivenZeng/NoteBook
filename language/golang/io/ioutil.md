ioutil是一个提供了一些io工具函数的包

### 伪写入
```
var Discard io.Writer = devNull(0)
```

使用方法
```
ioutil.Discard.Write([]byte)
```

### 读取
```
func NopCloser(r io.Reader) io.Reader
返回一个包裹了给定Reader的ReaderCloser，这个ReaderCloser带有一个无参的Close方法

func ReadAll(r io.Reader)([]byte , erroo) 
读取所有信息，直到发生错误或者读取到EOF。当读取到EOF，代表读取完成，并不会返回错误信息

func ReadDir(dirname string)([]os.FileInfo,error)
返回dirname目录的子目录、子文件。注意，返回的结果包含子文件。

func ReadFile(filename string)([]byte ,error)
读取一个文件，并返回文件所有内容。当读取到EOF，代表读取完成，并不会返回错误信息
```

### 写入
```
func WriteFile(filename string, data []byte,perm os.FileMode) error
如果文件不存在，就创建它并设定权限为perm。
如果文件已经存在，那么在写入之前会将旧有内容清空。
```

### 文件创建
```
func TempDir(dir,prefix string)(name string,err error)
在dir目录创建一个以prefix为前缀的临时目录，并返回临时目录的路径。
主要用于要暂存一些数据的场景，当目录不再使用的时候，需要手动删除该临时目录及其子文件。
如果dir为空，就是用os.TempDi中指定的目录作为默认目录。
创建过程进程安全（原理？可能与时间有关）

func TempFile(dir,prefix string)(f * os.File,err error)
在dir目录创建一个以prefix为前缀的临时文件
创建过程进程安全
```




