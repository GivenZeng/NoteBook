* 用于根据req的要求和广告限量规则过滤广告https://conf.umlife.net/pages/viewpage.action?pageId=46238950
* 定义了函数type Filter func(ctx context.Context, req *Request, ad *Ad) NMR 及其实现

* func filterTargetSource(ctx context.Context, req *Request, ad *Ad) NMR//判断请求的target（视频、图片）是否与ad符合
* func filterPackageTargeting(_ context.Context, req *Request, ad *Ad)NMR //判断该请求的渠道的包名是否在ad的黑白名单中，再白名单则放行
* func filterVideoAd(_ context.Context, req *Request, ad *Ad) NMR//判断ad是否为视频
* func filterCap(ctx context.Context, req *Request, ad *Ad) NMR //根据限量规则过滤
* func filterCountry(_ context.Context, req *Request, ad *Ad) NMR//请求中有一个国家列表，如果ad的target.country包含列表中的某一个，则成功
* func filterAid(ctx context.Context, req *Request, ad *Ad) NMR //请求的aid是否在ad的黑名单中、ad的ad.Campaign.Tap联盟id是否在请求的黑名单中、广告是否在媒介的白名单中、媒介是否在联盟的白名单中、应用-联盟-广告开关、媒介-offer等级
* func filterName(_ context.Context, req *Request, ad *Ad) NMR //请求的包名是否与ad匹配
* func filterCarrier(_ context.Context, req *Request, ad *Ad) NMR//请求的运营商是否在ad的运营商名单中
* func filterPlatformAndSV(_ context.Context, req *Request, ad *Ad) NMR //根据sdk版本过滤
* func filterPointType(_ context.Context, req *Request, ad *Ad) NMR //0非激励，1激励，2都接受，根据此类型过滤
* func filterGoal(_ context.Context, req *Request, ad *Ad) NMR
* func filterDeviceTargeting(_ context.Context, req *Request, ad *Ad) NMR
