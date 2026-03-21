
<div class="page-wrapper">
	<!-- ============================================================== -->
	<!-- Bread crumb and right sidebar toggle -->
	<!-- ============================================================== -->
	<div class="page-breadcrumb">
		<div class="row">
			<div class="col-7 align-self-center">
				<h4 class="page-title">Real time chat with users</h4>
				<div class="d-flex align-items-center">
					<nav aria-label="breadcrumb">
						<ol class="breadcrumb">
							<li class="breadcrumb-item"><a href="#">Home</a></li>
							<li class="breadcrumb-item active" aria-current="page">Chat</li>
						</ol>
					</nav>
				</div>
			</div>
		</div>
	</div>
	<div class="container-fluid">
		<div class="row">
			<div class="col-lg-12">
				<div style="float:right;" class="d-none"><p><h4>Messages: <b><span id="msgcount"></span></b></h4></p></div>
				<div class="row">
                    <div class="col-md-3">
                        <input type="text" placeholder="Type Here..." class="form-control" id="message" name="message" />
                    </div>
                    <div class="col-md-3">
                        <input type="button" class="btn btn-primary" id="send" name="send" value="Send"/>
                    </div>
					<div class="col-md-6" style="text-align:right;">
                        <input type="button" class="btn btn-success" id="serverConnect" name="serverConnect" value="Connect to Server"/>
                    </div>
                </div>
			</div>
		
		</div>
		<div id="msg_div">
			<div class="row" >
				<div class="col-sm-12">
					<div class="card">
						<div class="card-body">
							<h5 class="p-2">Message List</h5>
							<div class="table-responsive" >
								<table id="datatable_delBoy" class="table table-bordered table-stripped" width="100%">
									<thead>
										<tr>
										    <th>Date</th>
                                            <th>Message</th>									 
										</tr>
									</thead>
									<tbody id="message-tbody">
                                    <?php
                                        foreach($allMsgs as $row){ 
                                    ?>
                                        <tr>
                                            <td><?php echo $row['date']; ?></td>
                                            <td><?php echo $row['msg']; ?></td>
                                        </tr>
                                    <?php } ?>
                                    </tbody>
								</table>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<script src="<?php echo base_url('server/node_modules/socket.io/client-dist/socket.io.js');?>"></script>
<script>
$(document).ready(function(){
	$(document).on("click","#send",function() {
		var dataString = {
			message : $("#message").val()
		};
		$.ajax({
			type: "POST",
			url: "<?php echo base_url('Send/send');?>",
			data: dataString,
			dataType: "json",
			cache : false,
			success: function(data){
				if(data.success ==true){
					var socket = io.connect( 'https://'+window.location.hostname+':8008',{secure:true ,reconnect: true, rejectUnauthorized : false});
					socket.emit('new_message', {
						message: data.message,
						date: data.date,
						msgcount: data.msgcount
					});
				}
			} 
		});
	});
	$(document).on("click","#serverConnect",function() {
		$.ajax({
			type: "POST",
			url: "<?php echo base_url('Send/connectServer');?>",
			cache : false,
			success: function(data){
				debugger;
			} 
		});
	});
});
var socket = io.connect( 'https://'+window.location.hostname+':8008',{secure:true,reconnect: true, rejectUnauthorized : false});
socket.on( 'new_message', function( data ) {
   console.log(data);
   $('#message').val('');
$("#message-tbody").prepend('<tr><td>'+data.date+'</td><td>'+data.message+'</td></tr>');
$("#msgcount").text(data.msgcount);
});
</script>
</body>
</html>