## 简介
用于校验一个对象的参数、属性是否合法

## [go-validator](https://github.com/go-validator/validator)
一个用于goalng的校验库
### 安装
```
go get gopkg.in/validator.v2
```

### example
```
import (
	"gopkg.in/validator.v2"
)

type NewUserRequest struct {
	Username string `validate:"min=3,max=40,regexp=^[a-zA-Z]*$"`
	Name string     `validate:"nonzero"`
	Age int         `validate:"min=21"`
	Password string `validate:"min=8"`
}

nur := NewUserRequest{Username: "something", Age: 20}
if errs := validator.Validate(nur); errs != nil {
	// values not valid, deal with errors here
}
```

### 校验器
```
len： 可规定数字长度、字符串的字符数、切片或数组的元素个数、map的元素个数

max
	For numeric numbers, max will simply make sure that the
	value is lesser or equal to the parameter given. For strings,
	it checks that the string length is at most that number of
	characters. For slices,	arrays, and maps, validates the
	number of items. (Usage: max=10)

min
	For numeric numbers, min will simply make sure that the value
	is greater or equal to the parameter given. For strings, it
	checks that the string length is at least that number of
	characters. For slices, arrays, and maps, validates the
	number of items. (Usage: min=10)

nonzero
	This validates that the value is not zero. The appropriate
	zero value is given by the Go spec (e.g. for int it's 0, for
	string it's "", for pointers is nil, etc.) For structs, it
	will not check to see if the struct itself has all zero
	values, instead use a pointer or put nonzero on the struct's
	keys that you care about. (Usage: nonzero)

regexp
	Only valid for string types, it will validator that the
	value matches the regular expression provided as parameter.
	(Usage: regexp=^a.*b$)
```

### 自定义校验器
https://github.com/go-validator/validator
