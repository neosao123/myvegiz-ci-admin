<link href="<?php echo base_url().'assets/admin/assets/welcome/style.css';?>" rel="stylesheet">

<style>
.cd-intro-content{
	background: linear-gradient(rgba(0,0,0, 0.7), rgba(0, 0, 0, .8)),url('<?= base_url()?>assets/admin/assets/images/big/myvegiz_login-min.jpg') ;
	background-position: center; 
	background-repeat: no-repeat;
	background-size: cover;
}
</style>
<script src="<?php echo base_url().'assets/admin/assets/welcome/modernizr.js';?>"></script>
<div class="page-wrapper">
 <?php $session_key = $this->session->userdata('key'.SESS_KEY);
 $username = ($this->session->userdata['logged_in'.$session_key]['username']);
 $userFname = ($this->session->userdata['logged_in'.$session_key]['userFname']);
 ?>
 
<section class="cd-intro">
	<div class="cd-intro-content mask">
		<h1 data-content="<?=$userFname?>"><span></span></h1>
		<h1 data-content="Welcome to the Myvegiz"><span></span></h1>
		<div class="action-wrapper">
			<p>
			<!--	<a class="cd-btn main-action" href="<php// echo base_url() . 'index.php/authentication/logout'?>"><i class="fa fa-power-off m-r-5 m-l-5"></i> Logout</a> -->
			</p>
		</div>
	</div>
</section>
 </div>
	