### REST
* REpresentational State Transfer 直接翻译：表现层状态转移。
* 全称是 Resource Representational State Transfer：通俗来讲就是：资源在网络中以某种表现形式进行状态转移。

### 出处
Roy Fielding的毕业论文。这哥们参与设计HTTP协议，也是Apache Web Server项目（可惜现在已经是 nginx 的天下）的co-founder。PhD的毕业学校是 UC Irvine，Irvine在加州，有着充裕的阳光和美丽的海滩，是著名的富人区。Oculus VR 的总部就坐落于此（虚拟现实眼镜，被FB收购，CTO为Quake和Doom的作者 John Carmack）。

* 论文地址：Architectural Styles and the Design of Network-based Software Architectures：http://www.ics.uci.edu/~fielding/pubs/dissertation/top.htm
* REST章节：Fielding Dissertation: CHAPTER 5: Representational State Transfer (REST)http://www.ics.uci.edu/~fielding/pubs/dissertation/rest_arch_style.htm
* 知乎 https://www.zhihu.com/question/28557115

### 实际操作
即：URL定位资源，用HTTP动词（GET,POST,DELETE,DETC）描述操作。
* GET    用来获取资源，
* POST  用来新建资源（也可以用于更新资源），
* PUT    用来更新资源，
* DELETE  用来删除资源。比
![](./img/rest.jpg)

Server提供的RESTful API中，URL中只使用名词来指定资源，原则上不使用动词。“资源”是REST架构或者说整个网络处理的核心。

Server和Client之间传递某资源的一个表现形式，比如用JSON，XML传输文本，或者用JPG，WebP传输图片等。当然还可以压缩HTTP传输时的数据（on-wire data compression）。
用 HTTP Status Code传递Server的状态信息。比如最常用的 200 表示成功，500 表示Server内部错误等。
![](./img/restapi.jpg)


### 具体设计
* URL root,版本可以放在url中
```
https://example.org/api/v1/*
https://api.example.com/v1/*
```

* URI使用名词而不是动词，且推荐用复数。
* 保证  HEAD 和 GET 方法是安全的，不会对资源状态有所改变（污染）。
* 资源的地址推荐用嵌套结构。比如：GET /friends/10375923/profile
* 警惕返回结果的大小。如果过大，及时进行分页（pagination）或者加入限制（limit）。HTTP协议支持分页（Pagination）操作，在Header中使用 Link 即可。
* 使用正确的HTTP Status Code表示访问状态 https://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html
* 在返回结果用明确易懂的文本（String。注意返回的错误是要给人看的，避免用 1001 这种错误信息），而且适当地加入注释。
* 关于安全：自己的接口就用https，加上一个key做一次hash放在最后即可。考虑到国情，HTTPS在无线网络里不稳定，可以使用Application Level的加密手段把整个HTTP的payload加密。有兴趣的朋友可以用手机连上电脑的共享Wi-Fi，然后用Charles监听微信的网络请求（发照片或者刷朋友圈）。
