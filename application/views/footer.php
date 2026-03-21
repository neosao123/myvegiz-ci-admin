
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