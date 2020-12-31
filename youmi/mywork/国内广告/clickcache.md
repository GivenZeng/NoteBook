
### click cache
##### clickcache 点击缓存服务文档：
- https://conf.umlife.net/pages/viewpage.action?pageId=27624909
- 广告主配置文件文档 https://conf.umlife.net/pages/viewpage.action?pageId=43224679
- [点击上报合作文档](https://conf.umlife.net/pages/viewpage.action?pageId=19078791)


#### 点击接口对接
将点击信息发送给广告主，需要广告主提供点击上报接口，但是由于部分广告主的接口有些不同的要求，如请求头、响应body、参数编码加密等等，因此需要对这些广告主的接口进行配置

#### 配置

```
type AderConfig struct {
	// URLFunc 构造请求url, 返回值为url及是否需要进行参数替换
	URLFunc func(utils.Params, *common.AdInfo) (string, bool)
	// PostContentFunc 构造post请求的body, body为application/x-www-form-urlencoded形式
	PostContentFunc func(utils.Params, *common.AdInfo) string
	// HeaderFunc 构造请求的header
	HeaderFunc func(utils.Params) map[string]string
	// CallbackRespFunc 构造广告主回调时的返回值
	CallbackRespFunc func(utils.Params) string
	// ParseCallbackFunc 广告主回调时从请求中取出我们需要的参数
	ParseCallbackFunc func(utils.Params) utils.Params
}

```

- 实例化上述结构体，根据广告主要求选择性的实现上述方法，如

```
	package ader_config

import (
	"encoding/json"

	"git.umlife.net/gateway/clickcache/common"
	"git.umlife.net/gateway/clickcache/utils"
)

// Ader_da6574d3cc0b537b 封面新闻
var Ader_da6574d3cc0b537b = AderConfig{
	URLFunc: func(params utils.Params, adInfo *common.AdInfo) (string, bool) {
		return adInfo.Ad.AderURL.String, false
	},
	PostContentFunc: func(params utils.Params, adInfo *common.AdInfo) string {
		postMap := map[string]interface{}{
			"mac":          params.GetString("mac_formated"),
			"idfa":         params.GetString("ifa_formated"),
			"callback_url": params.GetString("callback_url"),
		}
		postContent, _ := json.Marshal(&postMap)
		return string(postContent)
	},
	HeaderFunc: func(params utils.Params) map[string]string {
		return map[string]string{
			"Content-Type": "application/json",
			"token":        "wxyHadZ2P5r1s0GA",
		}
	},
	CallbackRespFunc: func(params utils.Params) string {
		resp := map[string]interface{}{
			"msg":  "success",
			"code": 0,
		}
		respStr, _ := json.Marshal(&resp)
		return string(respStr)
	},
}

```

- 在ader_config.go注册配置，如

```
	// 封面新闻
	"da6574d3cc0b537b": ader_config.Ader_da6574d3cc0b537b,
```

- 在ader_config_test.go 编写测试用例

```
// TestAder_9f6d499833b07eab
func TestAder_9f6d499833b07eab(t *testing.T) {
	Convey("TestAder_9f6d499833b07eab", t, func() {
		params := utils.NewParamsFromMap(map[string]interface{}{
			"cid":          "test",
			"ad":           1234,
			"aid":          1234,
			"from":         2,
			"at":           2,
			"product":      2,
			"ifa":          "testingifa",
			"ifa_formated": "testingifa",
			"package":      "com.test.com",
			"ei":           "testimei",
			"mac":          "abcdadwiquer",
			"andid":        "ksdajhgaksdgf",
		})
		params.Set("rsd", utils.GetRandomString(10))
		params.Set("s", EncodeS(params))

		adInfo := &common.AdInfo{
			Ad: &schema.Ad{
				AdSecret: "47c2ea4bcb5a45c5",
			},
		}
		url, _, ext := GetAderConfigURL(params, adInfo)
		t.Log(url)
		respStr := GetAderCallbackResp(params, "bd7e46a009436def")
		t.Log(respStr)
		So(ext, ShouldBeFalse)
	})
}
```
当测试运行没问题后就push，然后进行下一步——部署

#### 部署
- 国内机器：adn_gw_xx

- 上线：
应该使用 https://codeship-awscn.umlife.net 系统进行发布。下面代码是手工发布，不推荐
```
ssh 国内跳板机器
cd ~/gateway/clickcache
make build
make adn   // 如果是灰度测试就：make gray
```
注意：Makefile里面运行ansible playbook命令(playbook文件就在跳板机的 ~/gateway/clickcache/ansible里面)，ansible playbook里面包含了上传、运行等task，因此make adn已经实现了自动部署
