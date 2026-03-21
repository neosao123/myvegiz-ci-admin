<div class="page-wrapper">
	<div class="page-breadcrumb">
		<div class="row">
			<div class="col-5 align-self-center">
				<h4 class="page-title">Cuisines</h4>
				<div class="d-flex align-items-center">
					<nav aria-label="breadcrumb">
						<ol class="breadcrumb">
							<li class="breadcrumb-item"><a href="<?php echo base_url().'index.php/admin/index';?>">Home</a></li>
							<li class="breadcrumb-item active" aria-current="page">Cuisines List</li>
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
						<h4 class="card-title">Cuisine List</h4>
						<div class="table-responsive">
							<table id="datatableCuisines" class="table table-striped table-bordered ">
								<thead>
									<tr>
										<th>Sr no.</th>
										<th>Code</th>
										<th>Cuisine Name</th>
										<th>Image</th>
										<th>Status</th>
										<th></th>
									</tr>
								</thead>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<script>	 
	 
	$( document ).ready(function() {
		
	 $(document).on("change", "#imagePath", function () {
        var filePath = $(this).val();
        var allowedExtensions = /(\.jpeg|\.jpg|\.png)$/i;
		if(filePath!=""){
			if (!allowedExtensions.exec(filePath)) {
				toastr.error('Invalid File type', 'Cuisine', {
						"progressBar": false
					});
				$(this).val(null);
				return false; 
			}
		}
       });
		
		onloadAddForm();
	
		function onloadAddForm()
		{
			$('#error_message').html('');
			$.ajax({
				url: base_path+"Food/Cuisine/add",  
				method:"GET",
				datatype:"text",
				success: function(data)
				{
					$("#appendForm").html(data);
				},
				complete: function()
				{
					$("#addCuisine").click(function()
					{
						var imagePath = $('#imagePath').val().trim(); 
						var cuisine = $('#cuisineName').val().trim(); 
						if(imagePath!="" && cuisine!="")
						{ 
							var fd = new FormData();
							var files = $('#imagePath')[0].files;							 
							var cuisineName = $('#cuisineName').val().trim(); 
							if($('#isActive').is(':checked'))
							{
								isActive= 1;
							}
							else
							{
								isActive= 0;
							}
							 
							// Check file selected or not
							if(files.length > 0 )
							{
								fd.append('imagePath',files[0]);								 
								fd.append('cuisineName',cuisineName); 
								fd.append('isActive',isActive); 
								$.ajax({
									type: "POST",
									url: base_path+"Food/Cuisine/save",
									enctype: 'multipart/form-data',
									contentType: false,
									processData: false,
									data: fd,
									success: function (data) {
										var obj=JSON.parse(data);
										var status = obj.status;
										if(status == 'true')
										{  
											toastr.success(obj.message, 'Cuisine', 'success',{ "progressBar": true });
											loadTable();
											onloadAddForm();
										}
										else if(status == 'error')
										{
											$('#error_message').html(obj.message);
										}
										else
										{
											toastr.success(obj.message, 'Cuisine', 'error',{ "progressBar": true });
											loadTable();
											onloadAddForm();
										}
									}
								});
								
							}
						} 
						else
						{
							$('#err').html('* Fields are required');
						}
					});
				}
			});
		}				
	
		loadTable();
		function loadTable(){
			if ($.fn.DataTable.isDataTable("#datatableCuisines")) {
				$('#datatableCuisines').DataTable().clear().destroy();
			}
			var dataTable = $('#datatableCuisines').DataTable({ 
				stateSave: true,
				"processing":true,  
				"serverSide":true,  
				"order":[],
				"searching": false,
				"ajax":{  
					url: base_path+"Food/Cuisine/getCuisineList",  
					type:"GET",  
					"complete": function(response) {
						//edit click
						$(".edit").click(function(){
							$('#error_message').html('');
							var code=$(this).data('seq');
							$.ajax({
								url: base_path+"Food/Cuisine/edit",  
								method:"post",
								data:{code:code},
								datatype:"text",
								success: function(data)
								{
									$("#appendForm").html(data);
									$("#editForm").show();
								},
								complete: function()
								{
									
									$("#backToAdd").click(function(){
										onloadAddForm();
									});									 
									
									// $( "form" ).on( "submit", function(e) {
									$("#updateCuisine").click(function(){
										
										var imagePath = $('#imagePath').val().trim(); 
									    var cuisine = $('#cuisineName').val().trim(); 
						                if(cuisine!="")
										{ 
											var fd = new FormData();
											var files = $('#imagePath')[0].files;
											var code = $('#code').val().trim();
											var cuisineName = $('#cuisineName').val().trim(); 
											if($('#isActive').is(':checked'))
											{
												isActive= 1;
											}
											else
											{
												isActive= 0;
											} 
										   	fd.append('imagePath',files[0]);
											fd.append('code',code);
											fd.append('cuisineName',cuisineName);
											fd.append('isActive',isActive);
											$.ajax({
												type: "POST",
												url: base_path+"Food/Cuisine/update",
												enctype: 'multipart/form-data',
												contentType: false,
												processData: false,
												data: fd,
												success: function (data) {													 
													var obj=JSON.parse(data);												 
													if(obj.status == 'true')
													{  
														toastr.success(obj.message, 'Cuisine', { "progressBar": true });
														loadTable();
														onloadAddForm();
													}
													else if(obj.status == 'false')
													{
														toastr.success(obj.message, 'Cuisine', { "progressBar": true });
													}
													else
													{
														$('#error_message').html(obj.message);
														//onloadAddForm();
													}
												}
											});
										
										}
										else
										{
											$('#err').html('* Fields are required');
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
								title: "You want to delete Cuisine "+code+" ?",
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
										url: base_path+"Food/Cuisine/delete",
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
									swal("Cancelled", "Your Cuisine Record is safe :)", "error");
								}
							});
						});
						
					}
				}
			});
		}	
		//show alerts
		var data='<?php echo $error; ?>';
		if(data!='')
		{
			var obj=JSON.parse(data);
			if(obj.status)
			{
				toastr.success(obj.message, 'Cuisine', { "progressBar": true });
			}
			else
			{
				toastr.error(obj.message, 'Cuisine', { "progressBar": true });
			}
		}
		//end show alerts
   });

</script>