记录各种过滤的结果码
```
package adfetchx

// No Match Reason
type NMR int

// TODO 分大类已方便扩充
// TODO move to separate package
const (
	OK              = 0
	NMRAdDisabled   = 1 // 广告已下线
	NMRPointType    = 3 // 广告激励类型过滤
	NMRGoal
	NMRDeviceInfo   = 4  // 设备参数过滤
	NMRPlatform     = 5  // 操作系统定向过滤
	NMRRegion       = 16 // 地区定向过滤
	NMRPnX          = 18
	NMRPn           = 19
	NMRAppName      = 20
	NMRCtlWL        = 21 // 媒介对于广告白名单
	NMRAidTapWL     = 22 // 联盟对于媒介白名单
	NMRAidTargetWL  = 23 // 广告对于媒介白名单
	NMRAidTargetBL  = 24 // 广告对于媒介黑名单
	NMRTapBL        = 25 // 联盟黑名单
	NMRTapOfferLv   = 26 // 联盟媒介等级
	NMRTapAidSwitch = 27
	// 各种 Cap 过滤
	NMRCapRuleLimit     = 299
	NMRCapLimit         = 300
	NMRCampaignCPCLimit = 301
	NMRCampaignCPALimit = 302
	NMRCreativeCPCLimit = 303
	NMRCreativeCPALimit = 304
	//
	NMRCarrier = 31 // 运营商定向
	NMRVideoAd = 32 // 视频过滤
	//NMRWebAdDailyView = 33
	NMRSrcPn        = 34
	NMRTargetSource = 35 // 流量出口过滤
	NMRSort         = 36 // 排序过滤
	NMRSeenCtid     = 37 // 重复
)

```
