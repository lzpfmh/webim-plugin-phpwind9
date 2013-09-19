WebIM For PHPWind 9.0
=====================

为PHPWind9.0提供的站内在线聊天插件，更新内容请查看 CHANGELOG.md


需求
-----------------------------

*	MySQL版本不低于5.0
*	需要PHP版本不低于5.3 
*	PHP访问外部网络，WebIM连接时需要访问WebIM服务器, 请确保您的php环境是否可连接外部网络, 设置php.ini中`allow_url_fopen=ON`.


安装
-----------------------------

首先将下载文件解压到PHPWind9.0的src/extentions/目录

	.
	|-- webim
	|   |-- README.md
	|   |-- controller


1.  数据库初始化脚本，SQL脚本文件webim/conf/schema.sql 

	注意: 默认库表前缀为pw_，如不同请修改脚本

2.  管理后台"云平台/应用管理/本地安装"，选择webim插件启用

3.  设置插件网站域名、网站ApiKey、IM服务器、服务器端口等参数，

卸载
-----------------------------

1. 管理后台"云平台/应用管理/已安装"，选择webim插件卸载

