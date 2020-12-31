提供一个构建可以使用redis-cli查询方法调用的接口的平台

## 提供一个redis处理接口,返回[]byte.可参考adfetch
```
func (a *Adfetch) HandleRedis(method string, args [][]byte) ([]byte, error) {
	defer func() {
		if er := recover(); er != nil {
			debug.PrintStack()
			alog.Error(er)
			return
		}
	}()
	ctx := context.TODO() // TODO
	switch method {
	case "GET", "get", "UGET":
		if len(args) < 1 {
			return nil, errors.New("GET: invalid number of args")
		}
		//// 解析请求
		req := new(schema.Request)
		var err error
		if method == "UGET" {
			err = req.ParseQuery(string(args[0]))
		} else {
			err = req.ParseMsgpack(args[0])
		}
		if err != nil {
			return nil, err
		}
		if req.Ctid != "" {
			ad := a.Get(req.Ctid)
			code := a.FilterAd(ctx, &Request{Request: req}, ad)
			return []byte(strconv.Itoa(int(code))), nil
		}
		//// 筛选广告
		ads, err := a.FetchAds(ctx, req)
		if err != nil {
			return nil, err
		}
		//// 格式化广告
		switch req.Format {
		case schema.FormatDetail:
			return json.Marshal(ads)
		case schema.FormatCtidJSON:
			return json.Marshal(ada.GetCtids(ads))
		default: //
			return msgpack.Marshal(ada.GetCtids(ads))
		}
	case "LEN", "len":
		return []byte(strconv.Itoa(a.NumberOfAds())), nil
	case "CTIDS":
		return json.Marshal(a.Ctids())
	}
	return nil, errors.New("unsupported method: " + method)
}

```

## 监听请求
```
migo.Panic(rediserver.ListenAndServe(*addr, adfetch.HandleRedis))
```

## 请求
```
redis-cli -p your_port
UGET goal=22&format=1
```
