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
                   
                    <h3 class="section-title">1. GENERAL</h3>
                    <p>a. This Mobile Application with the name of <strong>Myvegiz</strong> ("App/Myvegiz") is operated by Myvegiz food & vegetables delivery services ("We/Our/Us"). We are committed to protecting and respecting your privacy. We collect your personal information and process your personal data in accordance with the IT Act, 2000 (21 of 2000) and other national and state laws which relate to the processing of personal data.</p>
                    <p>b. Downloading, accessing, or otherwise using the App indicates that you have read this Privacy Policy and consent to its terms. If you do not consent to these terms, do not proceed to download or use the App.</p>
                    <p>c. We collect your personal information in order to provide and continually improve our products and services.</p>
                    <p>d. Our privacy policy is subject to change at any time without notice. To ensure you are aware of any changes, please review this policy periodically.</p>

                    <h3 class="section-title">2. HOW WE COLLECT INFORMATION</h3>
                    <p>a. <strong>Directly from you:</strong> We collect information through the App when you visit or interact with our services.</p>
                    <p>b. <strong>Business interaction:</strong> We may collect information through business interactions with you or your employees.</p>
                    <p>c. <strong>Third-party sources:</strong> We may receive information from public databases, marketing partners, social media platforms, or carriers (such as updated delivery and address information) to correct our records.</p>

                    <h3 class="section-title">3. INFORMATION WE COLLECT</h3>
                    <p>During your use of the App, we may collect:</p>
                    <ul>
                        <li><p><strong>Personal Data:</strong> Name, email address, postal address, phone number, and photo.</p></li>
                        <li><p><strong>Automatic Information:</strong> Device type, Operating System (OS), IP address, unique user ID, access times, and language.</p></li>
                        <li><p><strong>Usage Data:</strong> Purchase history, content use history, crash logs, and usage statistics.</p></li>
                        <li><p><strong>Location:</strong> Geolocation information from your device.</p></li>
                        <li><p><strong>Cookies:</strong> Small data files stored on your device to improve marketing and user experience.</p></li>
                    </ul>

                    <h3 class="section-title">4. HOW WE USE INFORMATION</h3>
                    <p>We use your information to:</p>
                    <ul>
                        <li><p>Improve our services, App functionality, and business operations.</p></li>
                        <li><p>Personalize your experience and provide recommendations.</p></li>
                        <li><p>Process, manage, and account for transactions and invoices.</p></li>
                        <li><p>Provide customer support and respond to inquiries.</p></li>
                        <li><p>Communicate promotions, upcoming events, and security alerts.</p></li>
                        <li><p>Protect against fraudulent, unauthorized, or illegal activity.</p></li>
                    </ul>

                    <h3 class="section-title">5. DATA TRANSFER & SHARING</h3>
                    <p>a. We share data with your consent or to complete transactions you have authorized.</p>
                    <p>b. We employ third-party companies to perform functions such as fulfilling orders, delivering packages, processing payments, and providing marketing assistance. These providers have access only to the information needed to perform their functions.</p>
                    <p>c. We release personal information when required to comply with the law or to protect the safety and rights of Myvegiz and its users.</p>

                    <h3 class="section-title">6. DATA SECURITY</h3>
                    <p>a. We have implemented appropriate physical, technical, and organizational measures to secure your information against accidental loss and unauthorized access.</p>
                    <p>b. Access to personal data is limited to employees and contractors with a legitimate business need.</p>
                    <p>c. It is your responsibility to protect against unauthorized access to your password and devices.</p>

                    <h3 class="section-title">7. THIRD-PARTY LINKS & PLUGINS</h3>
                    <p>Our App may contain links to third-party websites. We are not responsible for the privacy practices of these external sites. Similarly, social network plugins (like Facebook or Twitter buttons) are governed by the privacy policies of the respective social networks.</p>

                    <h3 class="section-title">8. CHILDREN'S PRIVACY</h3>
                    <p>If you are under the age of 18, you may only use Our App with the consent of a parent or legal guardian. We are not liable for any cause of action arising from non-compliance with this section.</p>

                    <h3 class="section-title">9. CONTACT & GRIEVANCES</h3>
                    <p>If you have any concerns about privacy or grievances, please contact our grievance officer:</p>
                    <div style="background: #f9f9f9; padding: 20px; border-radius: 8px; margin-top: 10px;">
                        <p><strong>Name:</strong> Mr. Rohan Rajaram Sidhanerle</p>
                        <p><strong>Phone:</strong> +91 9373747055</p>
                        <p><strong>Email:</strong> myvegiz.com</p>
                    </div>

                    <p style="margin-top: 30px; font-size: 12px; color: #999;">
                        ©2021 Myvegiz | Powered by Neosao Services Pvt. Ltd.
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