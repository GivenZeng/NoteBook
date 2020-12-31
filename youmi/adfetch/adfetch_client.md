一个用于调用的adfetch的client封装
### 使用方法
```
Adfetch    adfetchclient.Conf
adfetch, err := Adfetch.New()  //得到一个 adfetchclient.Client
```




```
//只返回ctids
func (c *redisClient) Fetch(_ context.Context, req *schema.Request) (l []string, err error)
//返回ads
func (c *redisClient) FetchAds(_ context.Context, req *schema.Request) (ads []*ada.Ad, err error)

具体使用方法 参考adin/adin/adin.go func (adin *Adin) GetAdfetchAds(ctx *ymapi.Context) (uads []UnivAd)
```
