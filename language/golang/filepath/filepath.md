

```
// 返回path的绝对路径
// 如果path是个绝对路径，那么直接返回
// 如果path是一个相对路径，那么会将其与当前工作目录连接，然后返回
func Abs(path string) (string, error)

// 提取路径的最后一个元素
// 如果路径最后是分隔符号，那么先去掉分隔符号
func Base(path string) string

// 将path规范化，如：a//b/./c  转化为a/b/c
func Clean(path string) string

// 先Clean一下path，然后返回目录
// 如 a//b//c 返回a/b
func Dir(path string) string


func EvalSymlinks(path string) (string, error)

// 返回目录中的最后一个部分中“.”之后的部分，即扩展名
func Ext(path string) string

// 将路径中的/转化为系统分隔符
func FromSlash(path string) string

// 返回符合pattern正则的所有文件的名字或目录
func Glob(pattern string) (matches []string, err error)

// 判断p的前缀是否为prefix
func HasPrefix(p, prefix string) bool

// 是否为绝对目录
func IsAbs(path string) bool

// 将elem的各个元素使用系统分隔符号连接起来
func Join(elem ...string) string

// 判断name是否符合正则pattern
func Match(pattern, name string) (matched bool, err error)

// 返回tarpath相对于basepath的路径，即相对路径
func Rel(basepath, targpath string) (string, error)

// 分开目录和文件名，如果最后一个字符是系统分隔符，那么file为空
func Split(path string) (dir, file string)

// 将path使用系统分隔符分隔
func SplitList(path string) []string

// 将path中的系统分隔符号转化为/
func ToSlash(path string) string

// 返回挂载卷目录，如c://a 返回c，/a/b/c返回/a/b
func VolumeName(path string) string

// 遍历root目录下的所有文件和目录，包含root本身。
// 并用walkFun处理这些文件或者目录
// 当目录下的子文件、子目录过多的时候，耗时比较长
func Walk(root string, walkFn WalkFunc) error

// path--文件/目录路径
// 文件/目录描述信息
type WalkFunc func(path string, info os.FileInfo, err error) error
```
