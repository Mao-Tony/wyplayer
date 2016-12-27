# 网易音乐播放器
  本播放器需要单独服务器，并非虚拟空间，请提前确认自己的主机是否为VPS或者云服务器。
  
	下载后修改music.js中的代码，先进行beas64位解密，在进行ESCAPE解密，把里面的地址修改为自己服务器的Ip地址或者绑定的域名
	
	
	
	再打开js文件夹，修改player.js中的   apiurl = "http://api.yinrao.cc/wyplayer/get.php";   将其域名修改为自己服务器Ip地址或绑定的域名
	
	
	修改music.js 请将修改完成的代码进行ESCAPE加密处理，在进行beas64加密，添加至原位置中
	
	
	至此，完成修改，调用方法为：﻿<script>auto="open";random="open";name="演示播放器";geci="open";user="291420238";welcome="open";tips="欢迎访问我的网站";</script>
<script type="text/javascript" src="http://api.yinrao.cc/wyplayer/music.js"></script>  



	所用函数为：
		默认自动播放，随机播放，歌词打开，修改可添加以下参数。
		auto   //colse 不自动播放
		random    //colse 顺序播放  open 随机播放
		name   //播放器名名称
		geci   //colse 歌词不显示  open显示歌词
		user   //网易主页个人ID
		welcome   //colse  关闭网站欢迎语     open打开
		tips   //网站欢迎语

		
服务器一周自动清除缓存，请放心使用~
