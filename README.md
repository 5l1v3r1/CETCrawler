---
title: PHP爬虫-正方教务系统一键报名四六级
date: 2018-03-23 13:12:27
categories: 笔记
---

## 前言

在我们学校四六级也是在正方教务系统进行报名的，login页面是跟查询成绩绩点的差不多，就是添加了几个表单数据。

require页面中一点点区别，模拟登录和之前的成绩绩点爬取是一样的。在报名页面有个地方是很关键的。


## 正文

### login_cet.php

login_cet.php页面跟login_grade.php是差不多的，获取到cookie和验证码保存到本地。

以下是提交表单界面

![](http://obr4sfdq7.bkt.clouddn.com/psb1.png)

### require_cet.php

在前言说到，模拟登录跟之前是一样的，加之目前学校已经关闭了报名页面，所以无法抓包。

### Posting multipart form data using curl in PHP

如下代码demo

```
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "http://localhost:8888");
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
curl_setopt($ch, CURLOPT_POSTFIELDS, array('a' => 'b', 'c' => 'd'));
curl_exec($ch);
```

以上是一个简单的curl发送一个POST请求

这个demo的产生request如下

```
POST / HTTP/1.1
Host: localhost:8888
Accept: */*
Content-Length: 228
Expect: 100-continue
Content-Type: multipart/form-data; boundary=------------------------f287dd3807057a2c

--------------------------f287dd3807057a2c
Content-Disposition: form-data; name="a"
Content-Length: 1

b
--------------------------f287dd3807057a2c
Content-Disposition: form-data; name="c"
Content-Length: 1

d
--------------------------f287dd3807057a2c--
```

从以上代码中总结出的就是，在curl_setopt($ch, CURLOPT_POSTFIELDS, $post)这个函数中，

如果$post是字符串，则Content-Type是application/x-www-form-urlencoded。

如果$post是k=>v的数组，则Content-Type是multipart/form-data

在这里因为这个爬虫中，确定提交的POST包的Content-Type是multipart/form-data。

所以这里有用到这个知识点。代码如下:

```
	$params=['__EVENTTARGET' => '','__EVENTARGUMENT'=>'','__VIEWSTATE'=>$state,'txtxxmc'=>'',$cet=>'on','txtSFZH'=>$idcard,'btnSubmit'=>'确 定','TextBox1'=>''];

	
	function post_cet($url2,$cookie,$params)
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url2);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch,CURLOPT_HTTPHEADER,array(
			'User-Agent: Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/64.0.3282.186 Safari/537.36',
			'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8',
			'Accept-Encoding: gzip, deflate',
			'Accept-Language: zh-CN,zh;q=0.9',
			'Upgrade-Insecure-Requests: 1',
			'Cache-Control: max-age=0'
		));
		curl_setopt($ch, CURLOPT_REFERER, $url2);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);  //不自动输出数据，要echo才行
		curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
	//	curl_setopt($ch, CURLINFO_HEADER_OUT, TRUE);
		curl_exec($ch);
	//	echo curl_getinfo($ch, CURLINFO_HEADER_OUT);
		curl_close($ch);
		return $response;
	}
	
	$con3=post_cet($url2,$cookie,$params);
```

以上的代码就可以实现提交确认POST数据报了，接下来我还做了个简单的判断，判断是否报名成功。

如果报名成功了，页面会有报名记录的。这里因为无法抓包，就只能根据代码说明了。

这里我没有使用正则匹配，用到的xpath定位。

代码如下：

```
	$con3=post_cet($url2,$cookie,$params);
	//echo $con3;
	// create document object model
	$dom = new DOMDocument();
	// load html into document object model
	@$dom->loadHTML($con3);
	// create domxpath instance
	$xPath = new DOMXPath($dom);
	// get all elements with a particular id and then loop through and print the href attribute
	$elements = $xPath->query('//*[@id="DBGridInfo"]/tbody/tr[2]');
	if($elements){
		echo'
		<div class="weui-msg">
			<div class="weui-msg__icon-area"><i class="weui-icon-success weui-icon_msg"></i></div>
			<div class="weui-msg__text-area">
				<h2 class="weui-msg__title">报考成功</h2>
			</div>
			<div class="weui-msg__opr-area">
				<p class="weui-btn-area">
					<a href="javascript:;" onClick="location.href=document.referrer" class="weui-btn weui-btn_primary">返回</a>
				</p>
			</div>
		</div>';
	}
	else{
		echo'
		<div class="weui-msg">
			<div class="weui-msg__icon-area"><i class="weui-icon-warn weui-icon_msg"></i></div>
			<div class="weui-msg__text-area">
				<h2 class="weui-msg__title">报考失败,请返回重试</h2>
			</div>
			<div class="weui-msg__opr-area">
				<p class="weui-btn-area">
					<a href="javascript:;" onClick="location.href=document.referrer" class="weui-btn weui-btn_primary">返回</a>
				</p>
			</div>
		</div>';
	}
```

其中的 

```
//*[@id="DBGridInfo"]/tbody/tr[2] 
```

是需要判断的xpath路径，更加匹配该地址的值来判断是否实现报名。


## Github

https://github.com/uknowsec/CETCrawler


## Reference

[Posting multipart form data using curl in PHP.](http://titohernandez.com/titohernandez/posting-multipart-form-data-using-curl-in-php/)
