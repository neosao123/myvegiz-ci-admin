<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<title>MyVegiz</title>

<!-- googlefont -->
<link rel="preconnect" href="https://fonts.gstatic.com">
<link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
<!-- googlefont -->
<!-- favicon -->
<link rel="icon" href="assets/images/favicon.png" type="assets/image/gif" sizes="32x32">
<!-- <link rel="shortcut icon" type="image/x-icon" href="images/favicon.ico"> -->
  <!-- bxslider -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/bxslider/4.2.12/jquery.bxslider.css">
<!-- bxslider -->
<!-- fontawesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<!-- fontawesome -->
<!-- css -->
<link rel="stylesheet" href="<?= base_url();?>assets/css/bootstrap.min.css">
<link rel="stylesheet" href="<?= base_url();?>assets/css/style.css">
<link rel="stylesheet" href="<?= base_url();?>assets/css/responsive.css">
<link rel="stylesheet" href="<?= base_url();?>assets/css/animations.css">

<!-- css  -->
</head>
<body>
<!-- top -->
<div id="top"></div> 
<!-- top -->
<!-- Preloader -->
<div id="preloader">
  <div id="status">&nbsp;</div>
</div>
<!-- Preloader -->
<?php $curPageName = substr($_SERVER["SCRIPT_NAME"],strrpos($_SERVER["SCRIPT_NAME"],"/")+1);?>


<header>
	<div class="new-navbar fixed-top">
	<div class="container">
		<div class="row">
			<div class="col-lg-12 ">
				<nav class="navbar navbar-expand-lg navbar-light">
				  <a href="index.php"><img src="<?= base_url();?>assets/images/logo.png" alt="logo"></a>
				  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
				    <span class="navbar-toggler-icon"></span>
				  </button>
				  <div class="collapse navbar-collapse" id="navbarSupportedContent">
				    <ul class="navbar-nav ml-auto">
				      <li><a href="#">Home</a></li>
				      <li><a href="<?php echo $curPageName == 'index.php' ? '#about': 'index.php'?>">About us</a> </li>	
				      <li><a href="<?php echo $curPageName == 'index.php' ? '#feature': 'index.php'?>">Features</a></li>
				      <li><a href="<?php echo $curPageName == 'index.php' ? '#screenshots': 'index.php'?>">Screenshots</a></li>
				      <li><a href="<?php echo $curPageName == 'index.php' ? '#reviews': 'index.php'?>">Reviews</a></li>
				      <li><a href="<?php echo $curPageName == 'index.php' ? '#download"': 'index.php'?>">Download</a></li>
				    </ul>				   
				  </div>
				</nav>
			</div>
		</div>
	</div>
</div> <!-- new-navbar-->
</header>

