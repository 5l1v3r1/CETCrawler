<?php 
    session_start();
    $id=session_id();
    $_SESSION['id']=$id;
	global $cookie;
    $rand_id = rand(100000, 999999);    //for verifycode
    require_verify_code();  //获取验证码
    function require_verify_code(){
        $cookie = dirname(__FILE__).'/cookie/'.$_SESSION['id'].'.txt';    //cookie路径  
        $verify_code_url = "http://202.119.160.5/CheckCode.aspx";      //验证码地址
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $verify_code_url);
        curl_setopt($curl, CURLOPT_COOKIEJAR, $cookie);                     //保存cookie
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $img = curl_exec($curl);                                            //执行curl
        curl_close($curl);
        global $rand_id;
        $path_of_verifyCode =dirname(__FILE__).'/verifyCodes/verifyCode_'.$rand_id.'.jpg';
        $fp = fopen($path_of_verifyCode,"w");                                  //文件名
        fwrite($fp,$img);                                                   //写入文件
        fclose($fp);
    }
?>

<!DOCTYPE html>
<html lang="zh_cn">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=0">
	<title>一键四六级报名</title>
	<link rel="stylesheet" href="//cdn.bootcss.com/weui/0.4.0/style/weui.min.css"/>
    <script src="//cdn.bootcss.com/jquery/3.0.0/jquery.min.js"></script>
	<script src="/js/login_score.js"></script>  
</head>
<body>
    <!-- 使用的是WeUI -->
	<form action="./require_cet.php" method="post">
		<div class="weui_cells_title">登录信息</div>
		<div class="weui_cells weui_cells_form">
			<div class="weui_cell">
				<div class="weui_cell_hd">
					<label class="weui_label">学号</label>
				</div>
				<div class="weui_cell_bd weui_cell_primary">
					<input class="weui_input" name="account" type="text" placeholder="请输入学号">
				</div>
			</div>

			<div class="weui_cell">
				<div class="weui_cell_hd">
					<label class="weui_label">密码</label>
				</div>
				<div class="weui_cell_bd weui_cell_primary">
					<input class="weui_input" name="password" type="password" placeholder="请输入教务密码(jwjx.njit.edu.cn)">
				</div>
			</div>
			
			<div class="weui_cell">
				<div class="weui_cell_hd">
					<label class="weui_label">身份号</label>
				</div>
				<div class="weui_cell_bd weui_cell_primary">
					<input class="weui_input" name="idcard" type="text" placeholder="请输入身份证号">
				</div>
			</div>

            <div class="weui_cell weui_cell_select weui_select_after">
                <div class="weui_cell_hd">
                    CET选择
                </div>
                <div class="weui_cell_bd weui_cell_primary">
                    <select class="weui_select" name="cet">
                        <option value="DBGrid:_ctl2:chkSelect">CET-4</option>
                        <option value="DBGrid:_ctl3:chkSelect">CET-6</option>
                    </select>
                </div>
            </div>
			<div class="weui_cell weui_vcode">
				<div class="weui_cell_hd"><label class="weui_label">验证码</label></div>
				<div class="weui_cell_bd weui_cell_primary">
					<input class="weui_input" name="verify_code" type="text" placeholder="请输入验证码"/>
				</div>
				<div class="weui_cell_ft">
                <img id="verify_code" src="/verifyCodes/verifyCode_<?php print $rand_id ?>.jpg" onclick="update_verify_code()" />
				</div>
			</div>
		</div>

        <!-- loading toast -->
            <div id="loadingToast" class="weui_loading_toast" style="display:none;">
                <div class="weui_mask_transparent"></div>
                <div class="weui_toast">
                    <div class="weui_loading">
                        <div class="weui_loading_leaf weui_loading_leaf_0"></div>
                        <div class="weui_loading_leaf weui_loading_leaf_1"></div>
                        <div class="weui_loading_leaf weui_loading_leaf_2"></div>
                        <div class="weui_loading_leaf weui_loading_leaf_3"></div>
                        <div class="weui_loading_leaf weui_loading_leaf_4"></div>
                        <div class="weui_loading_leaf weui_loading_leaf_5"></div>
                        <div class="weui_loading_leaf weui_loading_leaf_6"></div>
                        <div class="weui_loading_leaf weui_loading_leaf_7"></div>
                        <div class="weui_loading_leaf weui_loading_leaf_8"></div>
                        <div class="weui_loading_leaf weui_loading_leaf_9"></div>
                        <div class="weui_loading_leaf weui_loading_leaf_10"></div>
                        <div class="weui_loading_leaf weui_loading_leaf_11"></div>
                    </div>
                    <p class="weui_toast_content">数据加载中</p>
                </div>
            </div>

        <script>
            //Loading旋转菊花
            $(function() {
                $('#showLoadingToast').click(function() {
                    $('#loadingToast').fadeIn();
                });
            })
        </script>

		<input class="weui_btn weui_btn_primary" type="submit" value="查询" id="showLoadingToast"/>
	</form>		

<article class="weui_article">


<h1>
<i class="weui_icon_success_circle"></i>&nbsp;账号和密码不会保留，请放心使用。<br>
<i class="weui_icon_warn"></i>&nbsp;数据仅供参考，请以教务系统为准。<br></h1>
<br>

<br><br><br>	

<section>
如数据有问题(或者网站打不开了)请联系:<br>
<a href="http://www.uknowsec.cn">Uknow</a>(uknowsec@gmail.com)<br>
Stay hungry Stay foolish.<br>
<a target="_blank" href="//shang.qq.com/wpa/qunwpa?idkey=0066244a8e61e13b566e6ecd1c0cc5685aa333a187c3bbd5dcf884d6da9b4e43"><img border="0" src="//pub.idqqimg.com/wpa/images/group.png" alt="南工程技术交流群" title="南工程技术交流群"></a><br>
适用南京工程学院<br>
</section>
</article>
</article>
</body>
</html>



