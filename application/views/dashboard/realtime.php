<style>
	.food {
		color: #FF7F00;
	}

	.vege {
		color: #17c725;
	}
</style>
<div class="page-wrapper">
	<div class="page-breadcrumb">
		<div class="row">
			<div class="col-7 align-self-center">
				<h4 class="page-title">Broadcast Message</h4>
				<div class="d-flex align-items-center">
					<nav aria-label="breadcrumb">
						<ol class="breadcrumb">
							<li class="breadcrumb-item"><a href="#">Home</a></li>
							<li class="breadcrumb-item active" aria-current="page">Broadcast Message</li>
						</ol>
					</nav>
				</div>
			</div>
		</div>
	</div>
	<div class="container-fluid">
		<div class="row">
			<div class="col-md-8">
				<div class="card border-bottom">
					<div class="card-body">
						<h4>User : <?= $username ?></h4>
						<?php 
						if($code=='USR18_1'){ 
						?>
						<form name="addPostForm">
							<input type="text" id="message" name="message" />
							<input type="submit" class="btn btn-round btn-primary" name="submit"/>
						</form>
						<?php } ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<script>
/*$(document).ready(function() {
	$.ajax({
		type: "get",
		url: base_path + "Admin/startServer",
		data: {},
		success: function(response) {
			debugger;
		}
	});
});*/
var conn = new WebSocket('wss://myvegiz.com:2000');
conn.onopen = function(e) {
    console.log("Connection established!");
};

conn.onmessage = function(e) {
	var getData=jQuery.parseJSON(e.data);
    var html="<b>"+getData.name+"</b>: "+getData.msg+"<br/>";
	jQuery('#msg_box').append(html);
};
 conn.onerror = function (evt) {
            console.log("ERR: " + evt.data);
        };

jQuery('#btn').click(function(){
	var msg=jQuery('#msg').val();
	var name="<?php echo $_SESSION['name']?>";
	var content={
		msg:msg,
		name:name
	};
	conn.send(JSON.stringify(content));
	
	var html="<b>"+name+"</b>: "+msg+"<br/>";
	jQuery('#msg_box').append(html);
	jQuery('#msg').val('');
});
</script>