 <style>
 	.refreshbtn {
 		width: 32px;
 		height: 32px;
 		border-radius: 50%;
 		background-color: #4CAF50;
 		display: inline-block;
 		padding: 7px;
 		line-height: 1.0;
 		font-size: 16px;
 		color: #ffffff !important;
 		float: right;
 		cursor: pointer;
 	}
 </style>
 <?php echo '<input type="text" name="placeFlag" id="placeFlag" value="' . $placeFlag . '">'; ?>
 <div class="page-wrapper">
 	<div class="page-breadcrumb">
 		<div class="row">
 			<div class="col-12 align-self-center">
 				<h4 class="page-title">Order Details</h4>
 				<div class="d-flex align-items-center">
 					<nav aria-label="breadcrumb">
 						<ol class="breadcrumb">
 							<li class="breadcrumb-item"><a href="#">Home</a></li>
 							<li class="breadcrumb-item active" aria-current="page">View</li>
 						</ol>
 					</nav>
 				</div>
 			</div>
 		</div>
 	</div>
 	<div class="container-fluid col-md-12">
 		<form class="needs-validation" method="post" id="order" action="<?php echo base_url() . 'Order/confirm'; ?>" novalidate>
 			<?php
				echo "<div class='text-danger text-center' id='error_message'>";
				if (isset($error_message)) {
					echo $error_message;
				}
				echo "</div>";
				foreach ($query->result() as $row) {
				?>
 				<div class="col-12">
 					<div class="card">
 						<div class="card-body">
 							<h4 class="card-title">Order Details</h4>
 							<hr />
 							<div class="form-row">
 								<div class="col-md-4 mb-3">
 									<label for="orderCode"> Order Code : </label>
 									<input type="text" id="orderCode" name="orderCode" class="form-control-line" value="<?= $row->code ?>" required readonly>
 									<input type="hidden" id="cityCode" name="cityCode" class="form-control-line" value="<?= $row->cityCode ?>" required readonly>
 									<input type="hidden" id="areaCode" name="areaCode" class="form-control-line" value="<?= $row->areaCode ?>" required readonly>
 								</div>
								<div class="col-md-3 mb-3">
 									<label > Order Date : </label>
 									<input type="text" id="orderDate" name="orderDate" value="<?= date('d-m-Y',strtotime($row->addDate)) ?>" class="form-control-line" required readonly>
 								</div>
 								<div class="col-md-3 mb-3">
 									<label for="clientCode"> Client Code : </label>
 									<input type="text" id="clientCode" name="clientCode" value="<?= $row->clientCode ?>" class="form-control-line" required readonly>
 								</div>
 								<div class="col-md-5 mb-3">
 									<label for="clientName"> Client Name : </label>
 									<input type="text" id="clientName" name="clientName" value="<?= $clientName ?>" class="form-control-line" required disabled>
 								</div>
 							</div>
 							<div class="form-row">
 								<div class="col-md-3 mb-3">
 									<label for="paymentStatus"> Payment Status : </label>
 									<input type="hidden" id="paymentStatus" name="paymentStatus" value="<?= $row->paymentStatus ?>" />
 									<select id="paymentStatusl" name="paymentStatusl" class="form-control-line" value="<?= $row->paymentStatus ?>" disabled>
 										<option value="">Select option</option>
 										<?php
											foreach ($paymentStatus->result() as $pay) {
												echo '<option value="' . $pay->statusSName . '">' . $pay->statusName . '</option>';
											} ?>
 									</select>
 									<script>
 										var paymentStatus = '<?= $row->paymentStatus ?>';
 										$('#paymentStatusl').val(paymentStatus);
 									</script>
 								</div>
 								<div class="col-md-3 mb-3">
 									<label for="orderStatus"> Order Status : </label>
 									<?php
										$curOrderStatus = $row->orderStatus;
										if ($orderStatus) {
											foreach ($orderStatus->result() as $status) {
												if ($status->statusSName == $curOrderStatus) {
													$curOrderStatus =  $status->statusName;
												}
											}
										}
										?>
 									<input type="text" id="orderStatus" name="orderStatus" value="<?= $curOrderStatus ?>" class="form-control-line" required disabled>
 								</div>
 								<div class="col-md-3 mb-3">
 									<label for="paymentmode"> Payment Mode : </label>
 									<input type="text" id="paymentmode" name="paymentmode" class="form-control-line" value="<?= $row->paymentmode ?>" readonly>
 								</div>
 								<div class="col-md-3 mb-3">
 									<label for="phone"> Phone : </label>
 									<input type="number" id="phone" name="phone" class="form-control-line" value="<?= $row->phone ?>" readonly>
 								</div>
 							</div>
 							<div class="form-row">
 								<div class="col-md-3 mb-3">
 									<label for="address"> Order from City : </label>
 									<input type="text" id="city" name="city" class="form-control-line" value="<?= $city ?>" readonly>
 								</div>
 								<div class="col-md-9 mb-3">
 									<label for="address"> Address : </label>
 									<input type="text" id="address" name="address" class="form-control-line" value="<?= preg_replace('/\s+/', '', $row->address) ?>" readonly>
 								</div>
 							</div>
 						</div>
 					</div>
 				</div>
 				<div class="col-12">
 					<div class="card">
 						<div class="card-body">
 							<h4 class="card-title">Delivery Boy Details</h4>
 							<hr />
 							<?php
								$flg = 0;
								$dbContact = $dbName = "";
								if ($dBoyList && $dBoyList->num_rows() > 0) {
									$dBoyList  = $dBoyList->result()[0];
									$flg = 1;
									//$dbName = $dBoyList->firstName . ' ' . $dBoyList->lastName;
									//$dbContact = $dBoyList->contact1;
									$dbName = $dBoyList->name;
									$dbContact = $dBoyList->mobile;
								?>
 								<div class="form-row">
 									<div class="col-md-6 mb-3">
 										<input type="hidden" name="deliveryB" id="deliveryB" value="<?= $dBoyList->code ?>">
 										<label for="deliveryBoyCode"> Delivery Boy: </label>
 										<input type="text" id="deliveryBoyCode" name="deliveryBoyCode" class="form-control-line" value="<?= $dbName ?>" required readonly>
 									</div>
 									<div class="col-md-4 mb-3">
 										<label for="deliveryBoyContact"> Delivery Boy Contact: </label>
 										<input type="text" id="deliveryBoyContact" name="deliveryBoyContact" class="form-control-line" value="<?= $dbContact ?>" required readonly>
 									</div>
 								</div>
 							<?php
								} else {
									echo "Delivery Boy Not Assigned";
								}
								?>
 						</div>
 					</div>
 				</div>
 				<div class="col-12">
 					<div class="card">
 						<div class="card-body">
 							<h4 class="card-title">Product Details</h4>
 							<hr />
 							<div class="table-responsive">
 								<table id="datatableOrderDetails" class="table table-striped table-bordered">
 									<thead>
 										<tr>
 											<th>Sr.No.</th>
 											<th> Product Code</th>
 											<th>Product Name</th>
 											<th>Weight</th>
 											<th>Product Uom </th>
 											<th>Product Price</th>
 											<th>Quantity </th>
 											<th>Total Price</th>
 										</tr>
 									</thead>
 								</table>
 							</div>
 							<div class="form-row ">
 								<div class="float-left" style="width:50%;"> 
 									<?php if(($row->orderStatus =="PND" || $row->orderStatus =="PLC") && $row->deliveryBoyCode!=""){?>
 										
										<div class="col-md-6 mt-6 text-center">
 											<label for="transferDeliveryBoy" style="width:100%">
 												<span>Delivery Boy:<b style="color:black">(<?= $dbName ?>)</b></span>
 												
 											</label> 											
 										</div>
										<div class="col-md-6 mt-6 text-center d-flex">
										    <select class="custom-select form-control" id="transferDeliveryBoy" name="transferDeliveryBoy">
 												<option value="" readonly>Select another delivery boy</option>
 											</select>
											<span class="text-primary refreshbtn ml-2" id="pendingDBGet" title="click here to get current available delivery boy"> <i class="fas fa-sync"></i></span>
										</div>
 									<?php }
										if ($row->orderStatus == "PND" && $row->deliveryBoyCode == "") { ?>
 										<div class="col-md-6 mt-3">
										   
 											<label for="assignDeliveryBoy" style="width:100%;display: flex; align-items: center;">
 												<span style="font-size:16px; flex-grow: 1;"><b style="color:black"> Assign Delivery Boy: </b></span>
 												
 											</label>
										</div>
										<div class="col-md-6 mt-3 d-flex">
 											<select class="custom-select form-control" id="assignDeliveryBoy" name="assignDeliveryBoy">
 												<option value="" readonly>Select delivery boy</option>
 											</select>
											<span class="text-primary refreshbtn ml-2" id="pendingGet" title="Click here to find available delivery executives not assigned any orders">
 													<i class="fas fa-sync"></i>
 											</span>
 										</div>
 									<?php } ?>
 									<input type="hidden" class="form-control" id="deliveryBoyCode" name="deliveryBoyCode" value="<?= $row->deliveryBoyCode ?>">
 								</div>
 								<div class="float-right" style="width:50%;">
 									<div class="col-md-6 offset-md-6">
 										<b style="width:100%"><label>Item Total: </label> <span class="float-right"><?= number_format($row->subTotal + $row->discount, 2, '.', '') ?></span></b>
 									</div>
 									<div class="col-md-6 offset-md-6">
 										<b style="width:100%"><label>Discount (-): </label><span class="float-right"><?= number_format($row->discount, 2, '.', '') ?></span></b>
 									</div>
 									<div class="col-md-6 offset-md-6">
 										<div style="border-bottom:2px dashed;margin:10px 0"></div>
 									</div>
 									<div class="col-md-6 offset-md-6">
 										<b style="width:100%"><label>Sub Total: </label><span class="float-right"><?= number_format($row->subTotal, 2, '.', '') ?></span></b>
 									</div>
 									<div class="col-md-6 offset-md-6">
 										<b style="width:100%"><label>Tax (+): </label><span class="float-right"><?= number_format($row->gst, 2, '.', '') ?></span></b>
 									</div>
 									<div class="col-md-6 offset-md-6">
 										<b style="width:100%;"><label>Packaging Charges (+): </label><span class="float-right"><?= number_format($row->packagingCharges, 2, '.', '') ?></span></b>
 									</div>
 									<div class="col-md-6 offset-md-6">
 										<b style="width:100%;"><label>Shipping Charges (+): </label><span class="float-right"><?= number_format($row->shippingCharges, 2, '.', '') ?></span></b>
 									</div>
 									<div class="col-md-6 offset-md-6">
 										<div style="border-bottom:2px dashed;margin:10px 0">
										
										</div>
 									</div>
 									<div class="col-md-6 offset-md-6">
 										<b style="width:100%;"><label>Grand Total: </label><span class="float-right"><?= number_format($row->totalPrice, 2, '.', '') ?></span></b>
 									</div>
 									<input type="hidden" id="code" readonly name="code" class="form-control" value="<?= $row->code ?>">
									
									<div class="col-md-6 offset-md-6">
 										<div style="border-bottom:2px dashed;margin:10px 0">
										     <div>
											   <input type="hidden" id="code" readonly name="code" class="form-control" value="<?= $row->code?>">
											   </div>
												<label class='d-none'>
													<h4 class="d-no">Expired By Admin:</h4>
												</label>
											 <div class="custom-control custom-checkbox mr-sm-2">							
												<div class="custom-control custom-checkbox">
													<input type="checkbox" value="1" class="custom-control-input" id="isExpired" name="isExpired" <?php if($row->isExpired == 1) { echo "checked";} ?>>
													<label class="custom-control-label" for="isExpired">Order Expire</label>
												</div>
											</div>
										</div>
 									</div>							
								   
 								</div>
 							</div>
 						</div>
 					</div>
 				</div>
 				<?php if ($row->orderStatus == 'PLC') { ?>
 					<div class="col-12">
 						<div class="card">
 							<div class="card-body">
 								<div class="text-xs-right">
 									<button type="submit" id="submit" class="btn btn-success" onclick="page_isPostBack=true;">Confirm</button>
 									<button type="button" id="discard" class="btn btn-reset" onclick="page_isPostBack=true;">Discard</button>
 									<button type="button" id="revoke" class="btn btn-primary" onclick="page_isPostBack=true;">Revoke</button>
 								</div>
 							</div>
 						</div>
 					</div>
 			<?php }
				}
				?>
 		</form>
 	</div>
 </div>
 <script>
 	$(document).ready(function() {
 		$("#orderStatus").attr("disabled", true);
 		$("#revoke").hide();
 		var placeFlag = $('#placeFlag').val();
 		var orderCode = '';

 		if (orderStatus == 'RJT') {
 			$("#submit").hide();
 			$("#discard").hide();
 			$("#revoke").show();
 		} else if (orderStatus == 'CAN') {
 			$("#submit").hide();
 			$("#discard").hide();
 			$("#revoke").hide();
 		}

 		loadTable();

 		function loadTable() {
 			if ($.fn.DataTable.isDataTable("#datatableOrderDetails")) {
 				$('#datatableOrderDetails').DataTable().clear().destroy();
 			}
 			var orderCode = $('#orderCode').val();
 			var dataTable = $('#datatableOrderDetails').DataTable({
 				"processing": true,
 				"serverSide": true,
 				"order": [],
 				"searching": false,
 				"ajax": {
 					url: base_path + "Order/getOrderDetails",
 					type: "GET",
 					data: {
 						'orderCode': orderCode
 					},
 					"complete": function(response) {}
 				}
 			});
 		}
 	}); // End Ready

 	// Page Leave Yes / No 
 	var page_isPostBack = false;

 	function windowOnBeforeUnload() {
 		if (page_isPostBack == true)
 			return; // Let the page unload

 		if (window.event)
 			window.event.returnValue = 'Are you sure?';
 		else
 			return 'Are you sure?';
 	}
 	window.onbeforeunload = windowOnBeforeUnload;
 	// End Page Leave Yes / No 
 	setTimeout(function() {
 		$('#error_message').hide('fast');
 	}, 4000); // End Set Time Function 
 	$('#discard').click(function() {
 		orderCode = $('#orderCode').val();
 		placeFlag = $('#placeFlag').val();
 		swal({
 			title: "Are you sure?",
 			text: "Order Will Be Cancelled..",
 			type: "warning",
 			showCancelButton: !0,
 			confirmButtonColor: "#DD6B55",
 			confirmButtonText: "Reject",
 			cancelButtonText: "Cancel",
 			closeOnConfirm: !1,
 			closeOnCancel: !1
 		}, function(e) {
 			//console.log(e);
 			if (e) {
 				$.ajax({
 					url: base_path + "Order/delete",
 					type: 'POST',
 					data: {
 						'code': orderCode
 					},
 					success: function(data) {
 						// console.log(data);
 						if (data != 'false') {
 							swal({
 									title: "Completed",
 									text: "Successfully Rejected Order",
 									type: "success"
 								},
 								function(isConfirm) {
 									if (isConfirm) {
 										//  alert(placeFlag); 
 										if (placeFlag == 1) {
 											window.location.href = base_path + "Order/placedListRecords";
 										} else {
 											window.location.href = base_path + "Order/pendingListRecords";
 										}
 									}
 								});
 						}
 					},
 					error: function(xhr, ajaxOptions, thrownError) {
 						var errorMsg = 'Ajax request failed: ' + xhr.responseText;
 						alert(errorMsg);
 						// console.log("Ajax Request for patient data failed : " + errorMsg);
 					}
 				});
 			} else {
 				swal("Cancelled", "Your Order Reject Request Failed", "error");
 			}
 		});
 	});
 	$('#revoke').click(function() {
 		orderCode = $('#orderCode').val();
 		placeFlag = $('#placeFlag').val();
 		swal({
 			title: "Revoke",
 			text: "Are You Sure Revoke This Entry.",
 			type: "warning",
 			showCancelButton: !0,
 			confirmButtonColor: "#DD6B55",
 			confirmButtonText: "Revoke",
 			cancelButtonText: "Cancel",
 			closeOnConfirm: !1,
 			closeOnCancel: !1
 		}, function(e) {
 			//console.log(e);
 			if (e) {
 				$.ajax({
 					url: base_path + "Order/revoke",
 					type: 'POST',
 					data: {
 						'orderCode': orderCode
 					},
 					success: function(data) {
 						//console.log(data);
 						if (data != 'false') {
 							swal({
 									title: "Completed",
 									text: "Successfully  Revoked Order",
 									type: "success"
 								},
 								function(isConfirm) {
 									if (isConfirm) {
 										location.reload();
 									}
 								});
 						}
 					},
 					error: function(xhr, ajaxOptions, thrownError) {
 						var errorMsg = 'Ajax request failed: ' + xhr.responseText;
 						alert(errorMsg);
 					}
 				});
 			} else {
 				swal("Cancelled", "No change was made in the order", "error");
 			}
 		});
 	});
 	// Example starter JavaScript for disabling form submissions if there are invalid fields
 	(function() {
 		'use strict';
 		window.addEventListener('load', function() {
 			// Fetch all the forms we want to apply custom Bootstrap validation styles to
 			var forms = document.getElementsByClassName('needs-validation');
 			// Loop over them and prevent submission
 			var validation = Array.prototype.filter.call(forms, function(form) {
 				form.addEventListener('submit', function(event) {
 					if (form.checkValidity() === false) {
 						event.preventDefault();
 						event.stopPropagation();
 					}
 					form.classList.add('was-validated');
 				}, false);
 			});
 		}, false);
 	})();

 	$("#pendingDBGet").on("click", function() {
 		$(this).addClass("fa-spin");
 		$.ajax({
 			url: base_path + "Order/getPendingDeliveryBoys",
 			method: "GET",
 			data: {},
 			datatype: "text",
 			success: function(data) {
 				if (data) {
 					$("#pendingDBGet").removeClass("fa-spin");
 					$("#transferDeliveryBoy").html(data);
 				} else { //swal("no delivery boy found!", "error"); 
 					swal({
 						title: "warning!",
 						text: "no delivery boy found!",
 						type: "warning",
 						button: "ok",
 					});
 					//alert("");
 				}
 			}
 		});
 	});

 	$('#transferDeliveryBoy').on('change', function() {
 		//alert( this.value );
 		var fromDeliveryBoy = $('#deliveryB').val();
 		var toDeliveryBoy = $(this).val();
 		if (toDeliveryBoy == "") return false;
 		var orderCode = $('#orderCode').val();
 		var orderStatus = $('#orderStatus').val();
 		var orderType = "vegetable";
 		swal({
 				title: "Confirmation",
 				text: "Do you want to transfer this order to another delivery boy?",
 				type: "warning",
 				showCancelButton: true,
 				confirmButtonColor: "#DD6B55",
 				confirmButtonText: "yes",
 				closeOnConfirm: false
 			},
 			function(isConfirm) {
 				if (isConfirm) {
 					//alert(fromDeliveryBoy+" "+toDeliveryBoy+" "+orderCode+" "+orderStatus+" "+orderType);

 					$.ajax({
 						url: base_path + "foodOrderList/FoodOrderList/transferOrder",
 						method: "POST",
 						data: {
 							fromDeliveryBoy,
 							toDeliveryBoy,
 							orderCode,
 							orderStatus,
 							orderType
 						},
 						datatype: "text",
 						success: function(data) {
 							console.log(data);
 							var obj = JSON.parse(data);
 							if (obj.status) {
 								swal("Transfered!", obj.message, "success");

 							} else {
 								swal("Transfered!", obj.message, "error");
 							}
 						}
 					});

 					//   swal("Deleted!", "Your imaginary file has been deleted.", "success"); 
 					//  alert(placeFlag); 
 					// 			if (placeFlag == 1) {
 					// 				window.location.href = base_path + "Order/placedListRecords";
 					// 			} else {
 					// 				window.location.href = base_path + "Order/pendingListRecords";
 					// 			}
 				}
 			});



 	});

 	$("#pendingGet").on("click", function() {
 		$(this).addClass("fa-spin");
 		$.ajax({
 			url: base_path + "Order/getPendingDeliveryBoys",
 			method: "GET",
 			data: {},
 			datatype: "text",
 			success: function(data) {
 				//console.log(data);
 				if (data) {
 					$("#pendingGet").removeClass("fa-spin");
 					$("#assignDeliveryBoy").html(data);
 				} else {
 					swal({
 						title: "warning!",
 						text: "no delivery boy found!",
 						type: "warning",
 						button: "ok",
 					});

 				}
 			}
 		});
 	});

 	$('#assignDeliveryBoy').on('change', function() {
 		// alert( this.value );

 		var deliveryBoyCode = $(this).val();

 		var orderCode = $('#orderCode').val();
 		var orderStatus = $('#orderStatus').val();
 		var orderType = "vegetable";
 		swal({
 				title: "Confirmation",
 				text: "Do you want to Assign this selected delivery boy?",
 				type: "warning",
 				showCancelButton: true,
 				confirmButtonColor: "#DD6B55",
 				confirmButtonText: "yes",
 				closeOnConfirm: false
 			},
 			function(isConfirm) {
 				if (isConfirm) {


 					$.ajax({
 						url: base_path + "Order/assignDeliveryBoy",
 						method: "POST",
 						data: {
 							deliveryBoyCode,
 							orderCode,
 							orderStatus,
 							orderType
 						},
 						datatype: "text",
 						success: function(data) {
 							console.log(data);
 							var obj = JSON.parse(data);
 							if (obj.status) {
 								swal({
 									title: "Assigned!",
 									text: obj.message,
 									type: "success"
 								}, function(inputValue) {
 									//console.log(inputValue);
 									//if (inputValue==false) {
 									window.location.href = base_path + "Order/pendingListRecords";
 									//}

 								});

 							} else {
 								swal("Assigned!", obj.message, "error");
 							}
 						}
 					});

 				}
 			});



 	});
 	// Expired By Admin
 	$('#isExpired').on('click', function() {
        
 		var isExpired = $("#isExpired").val();

 		if ($("#isExpired").prop('checked') == true) {

 			var isExpired = $(this).val();
 			var orderCode = $('#orderCode').val();
 			var orderStatus = $('#orderStatus').val();

 			$.ajax({
 				url: "<?php echo site_url('Order/checkDeliveryBoyOrders'); ?>",
 				method: "GET",
 				data: {
 					"code": orderCode
 				},
 				datatype: "text",
 				success: function(data) {
					
 					console.log(data);
 					var obj1 = JSON.parse(data);
 					if (obj1.status) {
 						swal({
 							title: "Are you sure?",
 							text: "The Delivery Boy has Orders, Click OK to expire this order.",
 							type: "warning",
 							button: "Ok",
 							showCancelButton: !0,
 							closeOnConfirm: false
 						}, function(isConfirm) {
 							if (isConfirm) {
								
 								expireOrder(isExpired, orderCode, orderStatus, obj1.dbCode);
 							} else {
 								$('#isExpired').prop('checked', false);
 							}
 						});

 					} else {
 						swal({
 								title: "Confirmation",
 								text: "Do you want to Make This Action?",
 								type: "warning",
 								showCancelButton: true,
 								confirmButtonColor: "#DD6B55",
 								confirmButtonText: "yes",
 								closeOnConfirm: false
 							},
 							function(isConfirm) {
 								if (isConfirm) {
									
 									expireOrder(isExpired, orderCode, orderStatus, "");
 								} else {
 									$('#isExpired').prop('checked', false);
 								}
 							});
 					}

 				}
 			});

 		}

 	});


 	function expireOrder(isExpired, orderCode, orderStatus, dbCode) {
 		$.ajax({
 			url: base_path + "Order/expiredByAdmin",
 			method: "POST",
 			data: {
 				isExpired,
 				orderCode,
 				orderStatus,
 				dbCode
 			},
 			datatype: "text",
 			success: function(data) {
 				var obj = JSON.parse(data);
 				if (obj.status) {
 					swal({
 							title: "Assigned!",
 							text: obj.message,
 							type: "success"
 						},
 						function(isConfirm) {
 							if (isConfirm) {
 								window.location.href = base_path + "Order/pendingListRecords";
 							}
 						});

 				} else {
 					swal("Assigned!", obj.message, "error");
 				}
 			}
 		});
 	}
 </script>