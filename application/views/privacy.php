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

<style>
.fixed-top {
    position: sticky;
    top: 0;
    right: 0;
    left: 0;
    z-index: 1030;
}
header{
	background: #ffae00;
}
.privacy-wrapper{
    padding-top:60px;
    padding-bottom:60px;
}

.privacy-card{
    background:#ffffff;
    border-radius:12px;
    box-shadow:0 8px 30px rgba(0,0,0,0.08);
    padding:40px;
}

.privacy-card h2{
    font-weight:600;
    margin-bottom:10px;
}

.section-title{
    margin-top:35px;
    font-weight:600;
    font-size:18px;
    color:#222;
}

.privacy-card p{
    font-size:14.5px;
    color:#555;
    line-height:1.8;
    margin-bottom:10px;
}

.divider{
    width:60px;
    height:3px;
    background:#ffae00;
    margin:15px 0 25px 0;
}
</style>

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
				      <li><a href="#">Privacy Policy</a></li>
				      <li><a style="display:none" href="<?php echo $curPageName == 'index.php' ? '#about': 'index.php'?>">About us</a> </li>	
				      <li><a style="display:none" href="<?php echo $curPageName == 'index.php' ? '#feature': 'index.php'?>">Features</a></li>
				      <li><a style="display:none" href="<?php echo $curPageName == 'index.php' ? '#screenshots': 'index.php'?>">Screenshots</a></li>
				      <li><a style="display:none" href="<?php echo $curPageName == 'index.php' ? '#reviews': 'index.php'?>">Reviews</a></li>
				      <li><a style="display:none" href="<?php echo $curPageName == 'index.php' ? '#download"': 'index.php'?>">Download</a></li>
				    </ul>				   
				  </div>
				</nav>
			</div>
		</div>
	</div>
</div> <!-- new-navbar-->
</header>
<div class="privacy-wrapper">
    <div class="container">
        <div class="row">
            <div class="col-lg-10 offset-lg-1">
                <div class="privacy-card">
                    <h2>Privacy Policy</h2>
                    <div class="divider"></div>

                    <p>Myvegiz Food &amp; Vegetables Delivery Services ("Myvegiz", "we", "our", or "us") operates the Myvegiz mobile application available on the Google Play Store. This Privacy Policy describes how we collect, use, and share information when you use our mobile application.</p>
                    <p>By downloading, installing, or using the Myvegiz app, you agree to the collection and use of information in accordance with this Privacy Policy.</p>
                    <p><strong>Last Updated: 11 March 2026</strong></p>

                    <h3 class="section-title">1. Information We Collect</h3>
                    <p>We may collect the following types of information when you use the Myvegiz app.</p>

                    <p><strong>Personal Information</strong></p>
                    <ul>
                        <li><p>Name</p></li>
                        <li><p>Email address</p></li>
                        <li><p>Phone number</p></li>
                        <li><p>Delivery address</p></li>
                        <li><p>Profile photo (if provided)</p></li>
                    </ul>

                    <p><strong>Device Information</strong></p>
                    <ul>
                        <li><p>Device type</p></li>
                        <li><p>Operating system version</p></li>
                        <li><p>IP address</p></li>
                        <li><p>App version</p></li>
                        <li><p>Unique device identifiers</p></li>
                    </ul>

                    <p><strong>Usage Information</strong></p>
                    <ul>
                        <li><p>Order history</p></li>
                        <li><p>App interaction data</p></li>
                        <li><p>Crash logs and diagnostics</p></li>
                    </ul>

                    <p><strong>Location Information</strong></p>
                    <p>The Myvegiz app may collect approximate or precise location information from your device to provide delivery services, calculate delivery availability, and improve user experience.</p>
                    <p>Users may disable location access at any time through their device settings, however some features of the application may not function properly.</p>

                    <h3 class="section-title">2. How We Use Your Information</h3>
                    <p>We use the information we collect to:</p>
                    <ul>
                        <li><p>Process and deliver grocery and vegetable orders</p></li>
                        <li><p>Provide customer support</p></li>
                        <li><p>Improve app performance and user experience</p></li>
                        <li><p>Send order updates, service notifications, and alerts</p></li>
                        <li><p>Detect and prevent fraud or misuse of our services</p></li>
                        <li><p>Ensure security and reliability of the application</p></li>
                    </ul>

                    <h3 class="section-title">3. Data Sharing and Disclosure</h3>
                    <p>We may share your information with trusted third parties only when necessary, including:</p>
                    <ul>
                        <li><p>Delivery partners for order fulfillment</p></li>
                        <li><p>Payment service providers for payment processing</p></li>
                        <li><p>Technology service providers that help operate and maintain the application</p></li>
                    </ul>
                    <p>These third parties are obligated to use your information only for providing services related to Myvegiz.</p>
                    <p>We do not sell or rent users' personal information to third parties.</p>

                    <h3 class="section-title">4. Data Security</h3>
                    <p>We implement reasonable administrative, technical, and physical security measures to protect your personal information from unauthorized access, misuse, alteration, or disclosure.</p>
                    <p>However, no method of electronic transmission or storage is completely secure, and we cannot guarantee absolute security.</p>

                    <h3 class="section-title">5. Children's Privacy</h3>
                    <p>The Myvegiz app is not intended for children under the age of 13. We do not knowingly collect personal information from children under 13. If we become aware that such data has been collected, we will take appropriate steps to delete it.</p>

                    <h3 class="section-title">6. Third-Party Services</h3>
                    <p>Our application may use third-party services that may collect information used to identify you.</p>
                    <p>Examples include:</p>
                    <ul>
                        <li><p>Google Maps</p></li>
                        <li><p>Google Firebase (Analytics, Crash Reporting, Notifications)</p></li>
                        <li><p>Payment gateways</p></li>
                    </ul>
                    <p>These third-party services operate under their own privacy policies.</p>

                    <h3 class="section-title">7. Changes to This Privacy Policy</h3>
                    <p>We may update this Privacy Policy from time to time. When we do, we will revise the "Last Updated" date at the top of this page. Continued use of the application after any changes indicates acceptance of the updated policy.</p>

                    <h3 class="section-title">8. Contact Information</h3>
                    <p>If you have any questions, concerns, or requests regarding this Privacy Policy, please contact us.</p>
                    <div style="background: #f9f9f9; padding: 20px; border-radius: 8px; margin-top: 10px;">
                        <p><strong>Developer / Company:</strong> Myvegiz Food &amp; Vegetables Delivery Services</p>
                        <p><strong>Contact Person:</strong> Mr. Rohan Rajaram Sidhanerle</p>
                        <p><strong>Email:</strong> support@myvegiz.com</p>
                        <p><strong>Phone:</strong> +91 9373747055</p>
                    </div>

                    <p style="margin-top: 30px; font-size: 12px; color: #999;">
                        &copy; 2026 Myvegiz. All Rights Reserved.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="section-footer animatedParent" id="screen">
  <div class="container">
    <div class="row">     
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
      <div class="footer-text animated fadeInUpShort go">      
        <a href="https://www.facebook.com/My-vegiz-164088871180953" target="_blank"><i class="fa fa-facebook-official" aria-hidden="true"></i></a>
        <a href="https://twitter.com/MyVegiz" target="_blank"><i class="fa fa-twitter-square" aria-hidden="true"></i></a>
      </div>
    </div><!-- col-lg -->
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
      <div class="footer-text animated fadeInUpShort go">
        <p>©2019-<?php echo date("y");?> My Vegiz. All Rights Reserved. | Created by : <a href="https://neosao.com/" target="_blank">Neosao Services Pvt. Ltd.</a></p>
      </div>
    </div><!-- col-lg -->
  </div><!--  row-->
  </div><!-- container -->
</div><!-- section-footer -->


<!-- loader script -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>

<script>
	$(window).on('load', function() { // makes sure the whole site is loaded 
  $('#status').fadeOut(); // will first fade out the loading animation 
  $('#preloader').delay(550).fadeOut('slow'); // will fade out the white DIV that covers the website. 
  $('body').delay(550).css({'overflow':'visible'});
})
</script>
<!-- loader script -->



<!-- top btn -->
<button onclick="topFunction()" id="back-to-top-btn" title="Go to top"><img src="assets/images/top_button.png"></button>
<!-- top btn -->
<script src="<?= base_url();?>assets/js/popper.min.js" type="text/javascript"></script>
<script src="<?= base_url();?>assets/js/bootstrap.bundle.min.js" type="text/javascript"></script>
<script src="<?= base_url();?>assets/js/slim.min.js" type="text/javascript"></script>
<script src="<?= base_url();?>assets/js/bootstrap.min.js" type="text/javascript" charset="utf-8" async defer></script>
<script src="<?= base_url();?>assets/js/animate-it.js" type="text/javascript"></script> 
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script src="<?= base_url();?>assets/js/script.js" type="text/javascript"></script> 
<!-- bxslider -->
  <script src="https://cdn.jsdelivr.net/bxslider/4.2.12/jquery.bxslider.min.js"></script>
  <script src="<?= base_url();?>assets/js/script.js" type="text/javascript"></script> 
<!-- bxslider -->

<!-- smaooth fixed scripts scrooling -->
<script>
$(document).ready(function(){
  // Add smooth scrolling to all links
  $("a").on('click', function(event) {

    // Make sure this.hash has a value before overriding default behavior
    if (this.hash !== "") {
      // Prevent default anchor click behavior
      event.preventDefault();

      // Store hash
      var hash = this.hash;

      // Using jQuery's animate() method to add smooth page scroll
      // The optional number (800) specifies the number of milliseconds it takes to scroll to the specified area
      $('html, body').animate({
        scrollTop: $(hash).offset().top
      }, 800, function(){

        // Add hash (#) to URL when done scrolling (default click behavior)
        window.location.hash = hash;
      });
    } // End if
  });
});
</script>
<!-- smaooth fixed scripts scrooling -->


<!-- stick header -->
<script>
$(window).scroll(function() {
    if ($(this).scrollTop() > 1){  
        $('header').addClass("sticky");
    }
    else{
        $('header').removeClass("sticky");
    }
});
</script>
<!-- stick header -->

</body>




</html>