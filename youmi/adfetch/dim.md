#### dim.go
定义一个Ad结构体，是对普通Ad的封装
* func (ad \*Ad) Dims() (dims [][]string) // 获取广告所有能被索引到的维度
  * 参数* Ad，获取该Ad的纬度：目标-地区-package-name
  ```
  dim := []string{
    dimGoal,
    region,
    ad.App.Package,
    ad.App.Name,
  }
  dims = append(dims, dim)
  ```

* func (req \*Request) Query() []string  //为Request*  添加一个Query方法。
  * 返回该请求的query索引
  ```
  return []string{
		goal,
		country,
		req.Pn,
		req.AppName,
	}
  ```
