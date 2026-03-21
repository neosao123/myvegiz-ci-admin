<!doctype html>
<html>
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<title>MyVegiz - Notification</title> 
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" />
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

<style>
body { min-height: 100vh;background-image: linear-gradient(-20deg, #d558c8 0%, #24d292 100%); }
.container { margin: 150px auto; max-width: 600px; }
h1 { color: #fff;  }
</style>
 <script>
       var base_path = "<?= base_url() ?>";
	</script>
</head>

<body>
  <div class="container">
  <input type="hidden" id="userCount" value="<?=$listLength?>">
  <input type="hidden" id="offerCode" value="<?=$offerCode?>">
  <input type="hidden" id="datestart" value="<?=$datestart?>">
  <input type="hidden" id="dateEnd" value="<?=$dateEnd?>">
  <input type="hidden" id="cityCode" value="<?=$cityCode?>">
<div class="panel panel-default">
    <div class="panel-heading">
     <h3 class="panel-title">Send Notification...........</h3>
    </div>
      <div class="panel-body">
       <span id="success_message"></span>
       <div class="form-group" id="process" style="display:block;">
        <div class="progress">
       <div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100" style="">
       </div>
      </div>
       </div>
      </div>
     </div>
<script>
  $(document).ready(function() {
	  var userCount=$("#userCount").val();
	  var offerCode=$("#offerCode").val();
	  var datestart=$("#datestart").val();
	  var dateEnd=$("#dateEnd").val();
	  var cityCode=$("#cityCode").val();
	  
	
		$('#process').css('display', 'block');
		var percentage = 0;
		 progress_bar_process(10);
	sendData(userCount,0,500);
	
	
	function sendData(userCount,startVal,endVal)
	{
		var divisionFactor=(userCount/endVal);
		var length=endVal;
		//console.log(userCount+" "+startVal+" "+endVal);
		 
		 $.ajax({
			url: base_path+"Offer/sendOffersNotify",  
			method:"POST",     
			data:{offerCode,cityCode,startVal,endVal},
			datatype:"text",
			success: function(data)
			{
				console.log(data);
				 startVal=startVal+length;
                 if(userCount>startVal){
						percentage = percentage + (100/divisionFactor);
						progress_bar_process(percentage);
                       sendData(userCount,startVal,endVal);
                 }
				 else         
				 {
					 progress_bar_process(100);
					 setTimeout(function(){
						 progress_bar_process(101);
						}, 3000);
					
				 }           
			}
		});
	 }
	 
	 
	  function progress_bar_process(percentage)
	  {
	   $('.progress-bar').css('width', percentage + '%');
	   if(percentage > 100)
	   {		
		$('#process').css('display', 'none');
		$('.progress-bar').css('width', '0%');
		$('#success_message').html("<div class='alert alert-success'>All Notifications Sent Successfully</div>");
		setTimeout(function(){
		// var objWindow = window.open(location.href, "_self");
        // objWindow.close();
		}, 5000);
	   }
	  }
  
 });
</script>
</body>
</html>
