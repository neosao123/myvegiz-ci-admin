
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
		<input type="hidden" id="port" name="port" value="<?= $port ?>">
		<input type="hidden" id="deliveryBoyCode" name="deliveryBoyCode" value="<?= $deliveryBoyCode ?>">
		<input type="hidden" id="orderCode" name="orderCode" value="<?= $orderCode ?>">
		<div class="row">
			<div class="col-lg-12">
				<?php 
				if($type==2){ ?>
					<div class="row">
						<div class="col-md-3">
							<input type="text" placeholder="Latitude" class="form-control" id="latitude" name="latitude" />
						</div>
						<div class="col-md-3">
							<input type="text" placeholder="Longitude" class="form-control" id="longitude" name="longitude" />
						</div>
						<div class="col-md-3">
							<input type="button" class="btn btn-primary" id="send" name="send" value="Send"/>
						</div>
					</div>
				<?php }else{ ?>
						<div id="messages">
							<div class="table-responsive" >
								<table id="datatable_delBoy" class="table table-bordered table-stripped" width="100%">
									<thead>
										<tr>
										    <th>Date</th>
                                            <th>Latitude</th>									 
                                            <th>Longitude</th>									 
										</tr>
									</thead>
									<tbody id="message-tbody">
                                    <?php
										if($trackingDetails){
											foreach($trackingDetails as $tr){
                                    ?>
                                        <tr>
                                            <td><?= date('d/m/Y h:i A',strtotime($tr['addDate'])) ?></td>
											<td><?= $tr['latitude'] ?></td>
											<td><?= $tr['longitude'] ?></td>
                                        </tr>
                                    <?php } 
										}?>
                                    </tbody>
								</table>
							</div>
						</div>
				<?php } ?>
			</div>
		</div>
		
	</div>
</div>
<script src="<?php echo base_url('server/node_modules/socket.io/client-dist/socket.io.js');?>"></script>
<script>
	var trackingPort = $('#port').val();
$(document).ready(function(){
	$(document).on("click","#send",function() {
		var dataString = {
			latitude : $("#latitude").val(),
			longitude : $("#longitude").val(),
			deliveryBoyCode : $("#deliveryBoyCode").val(),
			orderCode : $("#orderCode").val(),
		};
		$.ajax({
			type: "POST",
			url: "<?php echo base_url('Send/send');?>",
			data: dataString,
			dataType: "json",
			cache : false,
			success: function(data){
				if(data.success ==true){
					var socket = io.connect( 'https://'+window.location.hostname+':'+trackingPort,{secure:true ,reconnect: true, rejectUnauthorized : false});
					socket.emit('new_message', {
						latitude: data.latitude,
						longitude: data.longitude,
						addDate: data.addDate
					});
				}
			} 
		});
	});
});
var socket = io.connect( 'https://'+window.location.hostname+':'+trackingPort,{secure:true,reconnect: true, rejectUnauthorized : false});
socket.on( 'new_message', function( data ) {
   console.log(data);
   $('#latitude').val('');
   $('#longitude').val('');
	$("#message-tbody").prepend('<tr><td>'+data.addDate+'</td><td>'+data.latitude+'</td><td>'+data.longitude+'</td></tr>');
});
</script>
</body>
</html>