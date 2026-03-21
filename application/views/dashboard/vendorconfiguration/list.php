<div class="page-wrapper">
	<div class="page-breadcrumb">
		<div class="row">
			<div class="col-12 align-self-center">
				<h4 class="page-title">Vendor Configuration</h4>
				<div class="d-flex align-items-center" <nav aria-label="breadcrumb">
					<ol class="breadcrumb">
						<li class="breadcrumb-item"><a href="#">Home</a></li>
						<li class="breadcrumb-item active" aria-current="page">Update Vendor Configuration</li>
					</ol>
					</nav>
				</div>
			</div>
		</div>
	</div>
	<div class="container-fluid col-md-8">
		<div class="card">
			<div class="card-body">
				<div class="col-sm-12 mt-3">
				    <h4 class="card-title"> Vendor Configuration</h4>
				    <hr />
    			    <form class="needs-validation" method="post" id="configForm" enctype="multipart/form-data" novalidate>
    				    <?php 
    				        $configCode = "";
    				        $maxCODAmount =  $defaultVendorCommission = $shippingCharges = $shippingChargesUpto = 0;
    				        if($vendorconfiguration)
    				        {
    				            foreach($vendorconfiguration->result_array() as $c){
    				                $configCode = $c['code'];
            				        $maxCODAmount = $c['maxCODAmount'];
            				        $defaultVendorCommission = $c['defaultVendorCommission'];
            				        $shippingCharges = $c['shippingCharges'];
            				        $shippingChargesUpto = $c['shippingChargesUpto'];
    				            }
    				        }
    				    ?>
        			    <div class="form-row">
        					<div class="col-md-6 mb-3 d-none">
        						<label for="maxCODAmount">Maximum COD Amount : <b style="color:red">*</b></label>
        						<input type="hidden" id="configCode" readonly name="configCode" value="<?= $configCode?>" class="form-control">
        						<input type="number" id="maxCODAmount" name="maxCODAmount" value="<?= $maxCODAmount?>" class="form-control">
        						<div class="invalid-feedback">
        							Required Field !
        						</div>
        					</div>
        					<div class="col-md-6 mb-3">
        						<label for="defaultVendorCommission">Default Vendor Commission (%): <b style="color:red">*</b></label>
        					    <input type="number" id="defaultVendorCommission" name="defaultVendorCommission" value="<?= $defaultVendorCommission?>" class="form-control" required>
        						 <div class="invalid-feedback">
        							Required Field !
        						</div>
        					</div>
        				</div> 
        				<div class="form-row d-none">
        					<div class="col-md-6 mb-3">
        						<label for="shippingCharges">Shipping Charges Amount: <b style="color:red">*</b></label> 
        						<input type="number" id="shippingCharges" name="shippingCharges" value="<?= $shippingCharges?>" class="form-control">
        						<div class="invalid-feedback">
        							Required Field !
        						</div>
        					</div>
        					<div class="col-md-6 mb-3">
        						<label for="shippingChargesUpto">Shipping Charges On Order Amount: <b style="color:red">*</b></label>
        					    <input type="number" id="shippingChargesUpto" name="shippingChargesUpto" value="<?= $shippingChargesUpto?>" class="form-control">
        						 <div class="invalid-feedback">
        							Required Field !
        						</div>
        					</div>
        				</div> 
        				<div class="form-row">
        				    <button type="submit" id="configBtn" class="btn btn-success" onclick="page_isPostBack=true;">Submit</button>
        				</div>
    			    </form>
			    </div>     
				 
				<div class="col-sm-12 mt-3 d-none">
				    <h4 class="card-title"> Commission Slabs</h4>
				    <hr />
    				<form class="needs-validation" method="post" id="slabForm" enctype="multipart/form-data" novalidate>
    					
    					<div id="price_fields"> 
    					  <?php 
    					    $i = 1;
    					    if($vendorcommissionslabs)
    					    {
    					       foreach($vendorcommissionslabs->result_array() as $sl)
    					       {
    					          
    					          ?>
    					            <div class="form-row mt-1 slabs removeclassP<?=$i?>">							
                						<div class="col-md-4"> 
                						    <input type="number" id="amountFrom<?=$i?>" required name="amountFrom[]" onchange="checkFromAmount(<?=$i?>)" value="<?=$sl['amountFrom']?>" class="form-control amountFrom" placeholder="From Amount">	 
                						</div>
                						<div class="col-md-4">
                							<input type="number" id="amountTo<?=$i?>" required name="amountTo[]" onchange="checkFromAmount(<?=$i?>)" value="<?=$sl['amountTo']?>" class="form-control amountTo" placeholder="To Amount ">	 
                						</div>
                						<div class="col-md-3">
                							<input type="number" id="commissionRate<?=$i?>" required name="commissionRate[]" value="<?=$sl['commissionRate']?>" class="form-control commissionRate" placeholder="Commission (%)">
                						</div>
                						<div class="col-md-1">
    										<div class="form-group">
    											<button class="btn btn-danger" type="button" onclick="remove_fields('<?php echo $i ?>','edit','<?php echo $sl['code'] ?>');">
    												<i class="fa fa-trash"></i>
    											</button>
    										</div>
    									</div> 
                					</div>
    					          <?php
    					           $i++;
    					       }
    					    }
    					   ?> 
    					</div> 
						<div class="form-row mb-3 mt-1">							
    						<div class="col-md-4"> 
    						    <input type="number" id="amountFrom0" required name="amountFrom[]" onchange="checkFromAmount(0)" class="form-control amountFrom" placeholder="From Amount">	 
    						</div>
    						<div class="col-md-4">
    							<input type="number" id="amountTo0" required name="amountTo[]" onchange="checkToAmount(0)" class="form-control amountTo" placeholder="To Amount ">	 
    						</div>
    						<div class="col-md-3">
    							<input type="number" id="commissionRate0" required name="commissionRate[]" class="form-control commissionRate" placeholder="Commission (%)">
    						</div>
    						<div class="col-md-1">								 
    							<button class="btn btn-success" type="button" onclick="commissionFields();"><i class="fa fa-plus"></i></button>   
    						</div> 
    					</div> 
    					<div class="mt-3 text-right">
    						<button type="submit" class="btn btn-success" id="slabBtn" onclick="page_isPostBack=true;">Submit</button>
    					</div>
    				</form>
    			</div>	
			</div>
		</div>
	</div>
</div>
<script>
	$("body").delegate(".sellingPrice","change",function() {
		var productRegularPrice = $("#productRegularPrice").val();
		var productSellingPrice = $(this).val();
		if (parseInt(productRegularPrice) < parseInt(productSellingPrice)) {
			alert('Regular Price must be high than Selling Price');
			$(this).val('');
		}
	});
	 
	$('document').ready(function() {
	    $("#slabForm").on("submit", function(e) {
			var fd = new FormData();
			var other_data = $('#slabForm').serializeArray();
			$.each(other_data,function(key,input){
				fd.append(input.name,input.value);
			}); 
			$.ajax({
				type: "POST",
				url: base_path+"index.php/Food/Vendorconfiguration/save_commissionslab",
				enctype: 'multipart/form-data',
				contentType: false,
				processData: false,
				data: fd,
				beforeSend: function() {
					$('#slabBtn').prop('disabled', true);
				},
				success: function (data) {
					var obj=JSON.parse(data);
					var status = obj.status;					 
					if(status){  
						toastr.success(obj.message, 'Slabs', { "progressBar": true });    
						setTimeout(function(){	location.reload();},5000);
					}else{
						toastr.error(obj.message, 'Slabs', { "progressBar": true });
					    setTimeout(function(){	location.reload();},5000);
					}
					$('#slabBtn').prop('disabled', false);
				}
			});
			e.preventDefault();
		});
		
		$("#configForm").on("submit", function(e) {
			var fd = new FormData();
			var other_data = $('#configForm').serializeArray();
			$.each(other_data,function(key,input){
				fd.append(input.name,input.value);
			}); 
			$.ajax({
				type: "POST",
				url: base_path+"index.php/Food/Vendorconfiguration/update_configuration",
				enctype: 'multipart/form-data',
				contentType: false,
				processData: false,
				data: fd,
				beforeSend: function() {
					$('#configBtn').prop('disabled', true);
				},
				success: function (data) {
					var obj=JSON.parse(data);
					var status = obj.status;					 
					if(status){  
						toastr.success(obj.message, 'Configuration', { "progressBar": true });
						setTimeout(function(){	location.reload();},5000);
					}else{
						toastr.error(obj.message, 'Configuration', { "progressBar": true });
						setTimeout(function(){	location.reload();},5000);
					}
					$('#configBtn').prop('disabled', false);
				}
			});
			e.preventDefault();
		});
	 
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
	var room = 1;
	var room2 = 1;
	 

	function commissionFields(count, flag) {
		debugger;
		if (flag == 'edit') {
			if (room2 == 1) {
				room2 = count;
			}
		}
		var objTo = document.getElementById('price_fields')
		var divtest = document.createElement("div");
		var amountFrom= $('#amountFrom0').val();
		var amountTo= $('#amountTo0').val();
		var commissionRate= $('#commissionRate0').val();
		if($('#amountFrom0').val()!=0 && $('#amountTo0').val()!=0 && $('#amountFrom0').val()!="" && $('#amountTo0').val()!="" && $('#commissionRate0').val()!=0 && $('#commissionRate0').val()!=0){
			divtest.setAttribute("class", "form-row mt-1 slabs removeclassP" + room2);  
			divtest.innerHTML = '<div class="col-md-4"> '+
					'<input type="number" id="amountFrom'+room2+'" name="amountFrom[]" onchange="checkFromAmount('+room2+')" class="form-control amountFrom" value="'+amountFrom+'" placeholder="From Amount">	 '+
				'</div>'+
				'<div class="col-md-4">'+
					'<input type="number" id="amountTo'+room2+'" name="amountTo[]" onchange="checkToAmount('+room2+')" class="form-control amountTo" value="'+amountTo+'" placeholder="To Amount ">	 '+
				'</div>'+
				'<div class="col-md-3">'+
					'<input type="number" id="commissionRate'+room2+'" name="commissionRate[]" value="'+commissionRate+'" class="form-control commissionRate" placeholder="Commission (%)">'+
				'</div>'+
				'<div class="col-md-1">'+
					'<button class="btn btn-danger" onclick="remove_price_fields('+room2+');"><i class="fa fa-trash"></i></button>'+
				'</div>';
			objTo.appendChild(divtest);
			room2++;
			$('#amountFrom0').val('');
			$('#amountTo0').val('');
			$('#commissionRate0').val('');
			$('#amountFrom0').focus();
		}else{
			toastr.error('Please provide all inputs valid', 'Configuration Commission Slabs', { "progressBar": true });
		}
	}
	$("body").delegate(".cityCode","change",function(){
		if(room2>1){				 
			var city_code=$(this).val();
			var thisid = $(this).attr('id');
			var counter=room2;
			var duplicate="";
			if(city_code!=""){
				$(".cityCode").each(function(){
					if($(this).attr('id')!=thisid){
						var prevRecord=$(this).val();	
						if(prevRecord==city_code) {
							$("#"+thisid).val("");
							swal('City','This City is Selected Already!','success');
						}
					}
				});
			}
		}
	});
 
	function checkFromAmount(id){
		debugger;
		var amountFrom = $('#amountFrom'+id).val();
		var totals=$('.slabs').length;
		if(totals>0){
			var previousToAmount = $('#amountTo'+totals).val();
			if(Number(amountFrom)<=Number(previousToAmount) && Number(previousToAmount)!='' && Number(previousToAmount)!=0 && Number(amountFrom)!='' && Number(amountFrom)!=0){
				toastr.error('From amount should not be overlapped with previous one', 'Commission Slab From amount', { "progressBar": true });
				$('#amountFrom'+id).val('');
				$('#amountFrom'+id).focus();
				return;
			}
		}
	}
	function checkToAmount(id){
		debugger;
		var toAmount = $('#amountTo'+id).val();
		var fromAmount = $('#amountFrom'+id).val();
		if(Number(toAmount)<=Number(fromAmount) && Number(toAmount)!='' && Number(fromAmount)!='' && Number(fromAmount)!=0 && Number(toAmount)!=0){
			toastr.error('To amount should be greater than from amount', 'Commission Slab to amount', { "progressBar": true });
			$('#amountTo'+id).val('');
			$('#amountTo'+id).focus();
			return;
		}
	}
	function remove_fields(rid, flag, code) {
	if (flag == 'edit') {
			swal({
				title: "Are you sure?",
				text: "You will not be able to recover this Slab record!",
				type: "warning",
				showCancelButton: !0,
				confirmButtonColor: "#DD6B55",
				confirmButtonText: "Yes, delete it!",
				cancelButtonText: "No, cancel please!",
				closeOnConfirm: !1,
				closeOnCancel: !1
				}, function(e) {
				console.log(e);
				if (e) {
					$.ajax({
						url: base_path + "index.php/Food/Vendorconfiguration/delete_commision_slab_line",
						type: 'POST',
						data: {
							'code': code
						},
						success: function(data) {
							//console.log(data);
							if (data != 'false') {
								swal({
									title: "Completed",
									text: "Successfully Deleted",
									type: "success"
								}, function(isConfirm) {
									if (isConfirm) {
										$('.removeclassP' + rid).remove();										 
									}
								});
							}
						},
						error: function(xhr, ajaxOptions, thrownError) {
							var errorMsg = 'Ajax request failed: ' + xhr.responseText;
							alert(errorMsg);
							console.log("Ajax Request for patient data failed : " + errorMsg);
						}
					});
				} else {
					swal("Cancelled", "Your slab record is safe :)", "error");
				}
			});
		} else {
			$('.removeclassP' + rid).remove();
		}
	}
</script>