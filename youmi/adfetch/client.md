```
// Client as adfetch interface
type Client interface {
	// 返回广告ID
	Fetch(context.Context, *schema.Request) (ctids []string, err error)
	// 返回广告详情
	FetchAds(context.Context, *schema.Request) (ads []*ada.Ad, err error)
}

```
