<!doctype html>
<!--[if IE 7 ]>    <html lang="en" class="no-js ie7"> <![endif]-->
<!--[if IE 8 ]>    <html lang="en" class="no-js ie8"> <![endif]-->
<!--[if IE 9 ]>    <html lang="en" class="no-js ie9"> <![endif]-->
<!--[if (gt IE 9)|!(IE)]><!--> <html lang="en" class="no-js"> <!--<![endif]-->
<head>
	<meta charset="UTF-8">
	<!--[if IE 8 ]><meta http-equiv="X-UA-Compatible" content="IE=7"><![endif]-->
	<title>.:: Portal Universitas Terbuka - Korea Selatan ::.</title>
	<meta name="description" content="" />
	<meta name="author" content="" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0">	
	<!-- CSS Styles -->
	<link rel="stylesheet" href="<?php echo admin_tpl_path()?>css/style.css">
	<link rel="stylesheet" href="<?php echo admin_tpl_path()?>css/jquery.tipsy.css">
	<noscript>
    	<h1 style="text-align: center;color:#000000;font-size: 24px">Javascript Harus Diaktifkan</h1>
    </noscript>
	<script src="<?php echo admin_tpl_path()?>js/libs/modernizr-1.7.min.js"></script>
    <style>
    #notif_error,#notif_information,#notif_frgt_error {position: absolute;width: 332px;z-index: -1;margin-top:-87px;opacity:0}     
    </style>
    
</head>
<body class="login">
	<section role="main">	
		<!--<a href="index.html" title="Back to Homepage"></a>-->
		<h1 style='font-size: 1.6em;text-align: center;color: #FFFFFF;'>
			Portal Akademik <br />Universitas Terbuka Korea Selatan
		</h1>
        
        <p style='font-size: 1em;text-align: center;color: #FFFFFF;padding-top:10px'>
        Untuk Mahasiswa Semester 1 Gunakan NIM anda untuk login (bukan No Pendaftaran), dengan menghilangkan 0 di depan.
        Misal NIM 017500000, Usernamenya adalah 17500000. Untuk password, masih sama dengan yang sebelumnya.
        
        Bila ada kesulitan untuk login, silahkan post di Facebook UT Korea 
        </p> 
        <!-- Login box -->
		<article id="login-box">
			<form action="main/ajax_login" id="frmLogin">
				<fieldset>
					<dl>
						<dt>
							<label>Username</label>
						</dt>
						<dd>
							<input type="text" class="large" name="username" id="username" />
						</dd>
						<dt>
							<label>Password</label>
						</dt>
						<dd>
							<input type="password" class="large" name="password" id="password" />
						</dd>
					</dl>
				</fieldset>
                <span class="left">
                    <?php
                        echo anchor("javascript://ajax","Lupa Password?","id='forgotPwd'");
                    ?>                    
                </span>
				<button type="submit" class="right" id="loginBtn">Log in</button>
                </form>
                
                <form action="" id="forgetPwdfrm">
                <fieldset id="forgotFrm" style="display:none;margin:0;padding:5px">
                    <legend>Masukkan Username Anda</legend>
                    <div style="padding-top:5px">
                        <span>
                            <input type="text" class="medium" name="usernameFrgt" id="usernameFrgt" />
                        </span>
    		            <span>
                            <button type="submit" class="right" id="loginBtnFrgt">Submit</button>
                        </span>
                    </div>
                      
				</fieldset>
			    </form> 
		</article>
		<!-- /Login box -->
	
		<!-- Notification -->
		<div class="notification error" id="notif_error">			
			<p><strong>Username dan Password Harus Diisi</strong></p>
		</div>
        <div class="notification information" id="notif_information">			
			<p><strong>Mohon Tunggu.....</strong></p>
		</div>
        <div class="notification error" id="notif_frgt_error">			
			<p><strong>Username Harus Diisi</strong></p>
		</div>
		<!-- /Notification -->
		
	</section>

	<!-- JS Libs at the end for faster loading -->
	<script src="<?php echo admin_tpl_path()?>js/jquery-1.8.0.min.js"></sript>
	<script src="<?php echo admin_tpl_path()?>js/libs/selectivizr.js"></script>
	<script src="<?php echo admin_tpl_path()?>js/jquery/jquery.tipsy.js"></script>
	<script type="text/javascript">
        $(function () {
           $('#forgotPwd').toggle(function(){
                    $('#forgotFrm').show("slow");
                },function(){
                    $('#forgotFrm').hide("slow");
                }
           );
            
           $('#frmLogin').submit(function(){
                $('div[class^="notif_"]').hide();
                
                var uval = $('#username').val();
                var passval = $('#password').val();
                
                if ( (uval == '') ||  (passval == '') ) {
                    $('#notif_error').animate({
                        marginTop : "10px",
                        zIndex : "1000",
                        opacity : "1"
                    },1500);
                } else {
                    $.post("<?php base_url()?>main/check_login", {username:uval, password:passval},
                     function(data) {
                       //console.log("datanya " + data)
                       if (data == "0") {
							$('#notif_error').html("<p><strong>Username atau Password Tidak Sesuai</strong></p>");
                            $('#notif_error').animate({
                                marginTop : "10px",
                                zIndex : "1000",
                                opacity : "1"
                            },1500);    
                       } else {
							window.location.replace('<?php echo base_url()?>');
					   }                       
                    });
                }
                return false;
           }); 
		   
		   $(document).ajaxStart(function(){
                $('#notif_information').animate({
												marginTop : "10px",
												zIndex : "1000",
												opacity : "1"
												},500);
		   });
		   
		   $(document).ajaxStart(function(){
				$('#notif_information').animate({
												marginTop : "-87px",
												zIndex : "-1000",
												opacity : "0"
												},500);
		   });
           
           $('#forgetPwdfrm').submit(function(){
                $('div[class^="notif_"]').hide();  
                var uval = $('#usernameFrgt').val();
                
                if (uval == '') {
                    $('#notif_frgt_error').animate({
                        marginTop : "10px",
                        zIndex : "1000",
                        opacity : "1"
                    },1500);
                } else {
                    $.post("<?php base_url()?>main/forgot_password", {username:uval},
                     function(data) {
                       if (data == "0") 
                       {
							$('#notif_error').html("<p><strong>Username Tidak Ditemukan</strong></p>");
                            $('#notif_error').animate({
                                marginTop : "10px",
                                zIndex : "1000",
                                opacity : "1"
                            },1500);    
                       } 
                       else if (data == "1")
                       {
							$('#notif_error').html("<p><strong>Anda Telah Melakukan Permintaan Rubah Password, Cek Email Anda.</strong></p>");
                            $('#notif_error').animate({
                                marginTop : "10px",
                                zIndex : "1000",
                                opacity : "1"
                            },1500);
					   }                       
                       else 
                       {
							$('#notif_information').html("<p><strong>Permintaan Sedang Diproses. Silakan Cek Email Anda.</strong></p>");
                            $('#notif_information').animate({
                                marginTop : "10px",
                                zIndex : "1000",
                                opacity : "1"
                            },1500);
					   }
                    });
                }
                return false;
           });
		   
        });
    </script>    
</body>

</html>