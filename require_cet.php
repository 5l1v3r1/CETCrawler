<!DOCTYPE html>
<html lang='zh_cn'>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=0">
	<title><?php printf($_POST['account']); ?> - 报名结果</title>
	<link rel="stylesheet" href="//cdn.bootcss.com/weui/0.4.0/style/weui.min.css"/>
	<link rel="stylesheet" href="style/accordion.css">
	<link rel="stylesheet" href="style/weui.css">
	<link rel="stylesheet" href="style/example.css">
</head>

<?php 
    session_start();
    header("Content-type: text/html; charset=utf-8");  //视学校而定，一般是gbk编码，php也采用的gbk编码方式
    
    //function: 构造post数据并登陆
    function login_post($url,$cookie,$post){
		global $cookie;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);  //不自动输出数据，要echo才行
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);  //重要，抓取跳转后数据
        curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie); 
        curl_setopt($ch, CURLOPT_REFERER, 'http://202.119.160.5/default2.aspx');  //重要，302跳转需要referer，可以在Request Headers找到 
        curl_setopt($ch, CURLOPT_POSTFIELDS,$post);  //post提交数据
        $result=curl_exec($ch);
        curl_close($ch);
        return $result;
    }
	
    //获取VIEWSTATE
    $_SESSION['xh']=$_POST['account'];
    $xh=$_POST['account'];
    $pw=$_POST['password'];
    $cet=$_POST['cet'];
    $idcard=$_POST['idcard'];
    $code= $_POST['verify_code'];
    $cookie = dirname(__FILE__) . '/cookie/'.$_SESSION['id'].'.txt';
    $url="http://202.119.160.5/default2.aspx";  //教务地址
    $con1=login_post($url,$cookie,'');               //登陆
    preg_match_all('/<input type="hidden" name="__VIEWSTATE" value="([^<>]+)" \/>/', $con1, $view); //获取__VIEWSTATE字段并存到$view数组中
    //为登陆准备的POST数据
    $post=array(
        '__VIEWSTATE'=>$view[1][0],
        'txtUserName'=>$xh,
        'TextBox2'=>$pw,
        'txtSecretCode'=>$code,
        'RadioButtonList1'=>iconv('utf-8', 'gb2312', '学生'),
        'Button1'=>iconv('utf-8', 'gb2312', '登录'),
        'lbLanguage'=>'',
        'hidPdrs'=>'',
        'hidsc'=>''
    );
    $con2=login_post($url,$cookie,http_build_query($post));

	
   //若登陆信息输入有误
    if(!preg_match("/xs_main/", $con2)){
		//echo $con2;
        echo '<h2>&nbsp;<i class="weui_icon_warn"></i>&nbsp;您的账号 or 密码输入错误，或者是表单填写错误，请<a href="/login_cet.php">返回</a>重新输入</h2>';
        exit();
    }


	preg_match_all('/<span id="xhxm">([^<>]+)<\/span>/', $con2, $xm);
	$xm[1][0]=substr($xm[1][0],0,-4);  //字符串截取，获得姓名
	$url2="http://202.119.160.5/bmxmb.aspx?xh=".$xh."&xm=".$xm[1][0]."&gnmkdm=N121303";
	$viewstate=login_post($url2,$cookie,'');
	preg_match_all('/<input type="hidden" name="__VIEWSTATE" value="([^<>]+)" \/>/', $viewstate, $vs);
	$state=$vs[1][0];  //$state存放一会post的__VIEWSTATE
//	preg_match_all('(?<=action=").+(?=" id)', $viewstate, $vs1);
//	print_r($vs1);
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
?>
</body>
</html>
