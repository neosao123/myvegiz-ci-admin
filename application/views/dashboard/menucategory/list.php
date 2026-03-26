<div class="page-wrapper">
	<div class="page-breadcrumb">
		<div class="row">
			<div class="col-5 align-self-center">
				<h4 class="page-title">Menu Category</h4>
				<div class="d-flex align-items-center">
					<nav aria-label="breadcrumb">
						<ol class="breadcrumb">
							<li class="breadcrumb-item"><a href="<?php echo base_url() . 'admin/index'; ?>">Home</a></li>
							<li class="breadcrumb-item active" aria-current="page">Menu Category List</li>
						</ol>
					</nav>
				</div>
			</div>
		</div>
	</div>
	
	<div class="container-fluid">
		<div class="row">
			<div class="col-5">
				<div class="card">
				<div class="text-danger text-center mt-2" id="error_message"></div>
					<div class="card-body" id="appendForm"> 
					</div>
				</div>
			</div>
			
			<div class="col-7">
				<div class="card">
					<div class="card-body">
						<h4 class="card-title">Menu Category List</h4>
						<div class="table-responsive">
							<table id="datatableMenucategory" class="table table-striped table-bordered ">
								<thead>
									<tr>
										<th>Sr no.</th>
										<th>Code</th>
										<th>Category</th>
										<th>Priority</th>
										<th>Status</th>
										<th>Operations</th>
									</tr>
								</thead>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	
	<div id="responsive-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title">View Menu Category</h4>
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">X</button>
				</div>
				<div class="modal-body">
					
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
				</div>
			</div>
		</div>
	</div>   
	
</div>

<script>
$( document ).ready(function() {
	
	onloadAddForm();
	
	function onloadAddForm(){
		$('#error_message').html('');
		$.ajax({
			url: base_path+"Food/Menucategory/add",  
			method:"GET",
			datatype:"text",
			success: function(data)
			{
				$("#appendForm").html(data);
			},complete: function(){
				// $( "form" ).on( "submit", function(e) {
				$("#saveMenuCategory").click(function(){
					var menuCategoryName = $('#menuCategoryName').val();
					menuCategoryName = menuCategoryName.trim();
					if(menuCategoryName!=""){
						var fd = new FormData();
						var other_data = $('#saveMenuCategoryForm').serializeArray();
						$.each(other_data,function(key,input){
							fd.append(input.name,input.value);
						});
						
						$.ajax({
							type: "POST",
							url: base_path+"Food/Menucategory/save",
							enctype: 'multipart/form-data',
							contentType: false,
							processData: false,
							data: fd,
							success: function (data) {
								var obj=JSON.parse(data);
								var status = obj.status;
								if(status == 'true')
								{  
									toastr.success(obj.message, 'Menu Category', 'success',{ "progressBar": true });
									loadTable();
									onloadAddForm();
								}
								else if(status == 'error')
								{
									$('#error_message').html(obj.message);
								}
								else
								{
									toastr.success(obj.message, 'Menu Category', 'error',{ "progressBar": true });
									loadTable();
									onloadAddForm();
								}
							}
						});
						// e.preventDefault();
					}else{
						$('#err').html('Menu category field is required.');
					}
					});
			}
		});
	}				
						
	loadTable();
	function loadTable(){
		if ($.fn.DataTable.isDataTable("#datatableMenucategory")) {
			$('#datatableMenucategory').DataTable().clear().destroy();
		}
		var dataTable = $('#datatableMenucategory').DataTable({ 
			stateSave: true,
			"processing":true,  
			"serverSide":true,  
			"order":[],
			"searching": true,
			"ajax":{  
				url: base_path+"Food/Menucategory/getMenuCategoryList",  
				type:"POST",  
				"complete": function(response) {
					
					$(".edit").click(function(){
						// debugger;
						$('#error_message').html('');
						var code=$(this).data('seq');
						$.ajax({
							url: base_path+"Food/Menucategory/edit",  
							method:"post",
							data:{code:code},
							datatype:"text",
							success: function(data)
							{
								$("#appendForm").html(data);
							},complete: function(){
								
								$("#backToAdd").click(function(){
									onloadAddForm();
								});
								
								// $( "form" ).on( "submit", function(e) {
								$("#updateMenuCategory").click(function(){
									var menuCategoryName = $('#menuCategoryName').val();
									menuCategoryName = menuCategoryName.trim();
									if(menuCategoryName!=""){
										var fd = new FormData();
										var other_data = $('#updateMenuCategoryForm').serializeArray();
										$.each(other_data,function(key,input){
											fd.append(input.name,input.value);
										});
										
										$.ajax({
											type: "POST",
											url: base_path+"Food/Menucategory/update",
											enctype: 'multipart/form-data',
											contentType: false,
											processData: false,
											data: fd,
											success: function (data) {
												var obj=JSON.parse(data);
												var status = obj.status;
												if(status == 'true')
												{  
													toastr.success(obj.message, 'Menu Category', 'success', { "progressBar": true });
													loadTable();
													onloadAddForm();
												}
												else if(status == 'error')
												{
													$('#error_message').html(obj.message);
												}
												else
												{
													toastr.success(obj.message, 'Menu Category', 'error',{ "progressBar": true });
													loadTable();
													onloadAddForm();
												}
											}
										});
										// e.preventDefault();
									}else{
										$('#err').html('this field id required..!');
									}
								});
							}
						});
					});
					
					//delete
					$('.mywarning').on("click", function() {
						var code=$(this).data('seq');
						//alert(code);
						swal({
							title: "You want to delete Menu Category "+code+" ?",
							// text: " Category against Product and stock also deleted.",
							type: "warning",
							showCancelButton: !0,
							confirmButtonColor: "#DD6B55",
							confirmButtonText: "Yes, delete it!",
							cancelButtonText: "No, cancel it!",
							closeOnConfirm: !1,
							closeOnCancel: !1
						}, function(e) {
							console.log(e);
							if(e)
							{
								$.ajax({
									url: base_path+"Food/Menucategory/delete",
									type: 'POST',
									data:{'code':code},
									success: function(data) {
										if(data)
										{
											swal({ 
												title: "Completed",
												text: "Successfully Deleted",
												type: "success" 
											},
											function(isConfirm){
												if (isConfirm) {
													//location.reload(true);
													loadTable();
												}
											});
										}
										else
										{
											swal("Failed", "Record Not Deleted", "error");
										}
									},
									error: function(xhr, ajaxOptions, thrownError) {
										var errorMsg = 'Ajax request failed: ' + xhr.responseText;
										alert(errorMsg);
										console.log("Ajax Request for patient data failed : " + errorMsg);
									}
								});
							}
							else
							{
								swal("Cancelled", "Your Menu Category Record is safe :)", "error");
							}
						});
					});
				}
			}
		});
	}

	var data='<?php echo $error; ?>';
	if(data!='')
	{
		var obj=JSON.parse(data);
		if(obj.status)
		{
			toastr.success(obj.message, 'Menu Category', 'success',{ "progressBar": true });
		}
		else
		{
			toastr.error(obj.message, 'Menu Category', 'error',{ "progressBar": true });
		}
	}
	//end show alerts
	
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
	
});

</script>