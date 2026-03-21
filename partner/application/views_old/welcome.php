<?php
$session_key = $this->session->userdata('key' . SESS_KEY_PARTNER);
$userFname = ($this->session->userdata['logged_in' . $session_key]['userFname']);
$userLname = ($this->session->userdata['logged_in' . $session_key]['userLname']);
?>
<script src="<?php echo base_url() . 'assets/admin/assets/welcome/modernizr.js'; ?>"></script>
<div class="page-wrapper">
	<div class="page-breadcrumb">
		<div class="row">
			<div class="col-5 align-self-center">
				<h4 class="page-title">Recent Orders...</h4>
				<span class="breadcrumb-item active" aria-current="page">Update in every 1 min. </span> 
			</div>
			<div class="col-7 align-self-center">
				<div class="d-flex no-block justify-content-end align-items-center">
					<div class=""><small>Refresh Time</small>
						<h4 class="text-info m-b-0 font-medium"><span id="timeOut"></span>'s</h4>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="container-fluid"> 
		<div class="row" id="orders"> 
		</div>
	</div>
</div>
<script>
	var i = 0;
	$("#timeOut").text(i);

	function getOrderCounts() {
		$.ajax({
			type: "get",
			url: base_path + "Home/getDashboardOrders",
			data: {},
			success: function(response) {
				//debugger;
				if (response) {
					$("#orders").empty();
					$("#orders").append(response);
				}
			}
		});
	}
	$("body").on("click", ".actionBtn", function(e) {
		var orderCode = $(this).data("id");
		var orderStatus = $(this).data("status");
		var dataAction = $(this).data("action");
		swal({
			title: "Are you sure?",
			text: dataAction,
			type: "warning",
			showCancelButton: !0,
			confirmButtonColor: "#DD6B55",
			confirmButtonText: "Ok",
			cancelButtonText: "Cancel",
			closeOnConfirm: !1,
			closeOnCancel: !1
		}, function(e) {
			if (e) {
				$.ajax({
					url: base_path + "Home/updateOrderStatus",
					type: 'POST',
					data: {
						'orderCode': orderCode,
						'orderStatus':orderStatus
					},
					success: function(response) {
						var res = JSON.parse(response); 
						if (res.status) {
							swal({
									title: "Order",
									text: res.message,
									type: "success"
								},
								function(isConfirm) {
									if (isConfirm) { 
											getOrderCounts(); 
									}
								});
						} else {
							toastr.error(res.message, 'Order', {
								"progressBar": true
							});
							getOrderCounts();
						}
					},
					error: function(xhr, ajaxOptions, thrownError) {
						var errorMsg = 'Ajax request failed: ' + xhr.responseText;
						alert(errorMsg);
					}
				});
			} else {
				swal("Cancelled", "Failed to update the order...", "error");
			}
		});
	});
	$(document).ready(function() {
		//loadTable();   
		setInterval(function(e) {
			//console.log("Time = ", i);
			if (i == 60) {
				i = 0;
				$("#timeOut").text(i);
				getOrderCounts();
			} else {
				i++;
				$("#timeOut").text(i);
			}
		}, 1000);
		getOrderCounts();
	});
</script> 