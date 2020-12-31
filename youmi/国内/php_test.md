### 测试
- 测试手机
	- 设置手机连接youmi wifi，设置代理为本机地址
	- 安装测试demo，发送一条点击信息
- 本机器使用charles捕获一条请求url
- 使用dnsmasq做测试host配置
- 在开发分支将git.umlife.net/adn/wall/v3/lib/android/req_wall_front.php的verifyDevice注释掉
- 将测试代码放到测试机器：国内跳板机-gw_test_00 ~/vhost/gateway
- 在本机器的浏览器请求上述url
