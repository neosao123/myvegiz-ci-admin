<div class="page-wrapper">
	<div class="page-breadcrumb">
		<div class="row">
			<div class="col-5 align-self-center">
				<h4 class="page-title">Main Category</h4>
				<div class="d-flex align-items-center">
					<nav aria-label="breadcrumb">
						<ol class="breadcrumb">
							<li class="breadcrumb-item"><a href="<?php echo base_url() . 'index.php/admin/index'; ?>">Home</a></li>
							<li class="breadcrumb-item active" aria-current="page">Main Category List</li>
						</ol>
					</nav>
				</div>
			</div>
		</div>
	</div>
	
	<div class="container-fluid">
		<div class="row">
			<div class="col-5" id="editForm" style="display:none">
				<div class="card">
				<div class="text-danger text-center mt-2" id="error_message"></div>
					<div class="card-body" id="appendForm">						
					</div>
				</div>
			</div>
			<div class="col-7">
				<div class="card">
					<div class="card-body">
						<h4 class="card-title">Main Category List</h4>
						<div class="table-responsive">
							<table id="datatableMainCategory" class="table table-striped table-bordered ">
								<thead>
									<tr>
										<th>Sr no.</th>
										<th>Code</th>
										<th>Main Category Name</th>
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
		loadTable();
		function loadTable(){
			if ($.fn.DataTable.isDataTable("#datatableMainCategory")) {
				$('#datatableMainCategory').DataTable().clear().destroy();
			}
			var dataTable = $('#datatableMainCategory').DataTable({ 
				stateSave: true,
				"processing":true,  
				"serverSide":true,  
				"order":[],
				"searching": false,
				"ajax":{  
					url: base_path+"Maincategory/getMainCategoryList",  
					type:"POST",  
					"complete": function(response) {
						//edit click
						$(".edit").click(function(){
							$('#error_message').html('');
							var code=$(this).data('seq');
							$.ajax({
								url: base_path+"Maincategory/edit",  
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
									
									$("#deleteImage").click(function(){
										var data_code = $(this).data('id');
										$.ajax({
											type: "POST",
											url: base_path+"Maincategory/update",
											enctype: 'multipart/form-data',
											contentType: false,
											processData: false,
											data: fd,
											success: function (data) {
												var obj=JSON.parse(data);
												if(obj.status==true)
												{
													toastr.success(obj.message, 'Main Category', { "progressBar": true });
													$("#imageDiv").remove();
												}
												else
												{
													toastr.success(obj.message, 'Main Category', { "progressBar": true });
												}
											}
										})
									});
									
									// $( "form" ).on( "submit", function(e) {
									$("#updateMainCategory").click(function(){
										var imagePath = $('#imagePath').val().trim(); 
										if(imagePath!="")
										{ 
											var fd = new FormData();
											var files = $('#imagePath')[0].files;
											var code = $('#code').val().trim();
											// Check file selected or not
											if(files.length > 0 ){
											   	fd.append('imagePath',files[0]);
												fd.append('code',code);
												$.ajax({
													type: "POST",
													url: base_path+"Maincategory/update",
													enctype: 'multipart/form-data',
													contentType: false,
													processData: false,
													data: fd,
													success: function (data) {													 
														var obj=JSON.parse(data);												 
														if(obj.status == 'true')
														{  
															toastr.success(obj.message, 'Main Category', { "progressBar": true });
															loadTable();
															$("#editForm").hide();
															//onloadAddForm();
														}
														else if(obj.status == 'false')
														{
															toastr.success(obj.message, 'Main Category', { "progressBar": true });
															$("#editForm").hide();
														}
														else
														{
															$('#error_message').html(obj.message);
															$("#editForm").hide();
															//onloadAddForm();
														}
													}
												});
											}
										}
										else
										{
											$('#err').html('this field id required..!');
										}
									});
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
				toastr.success(obj.message, 'Main Category', { "progressBar": true });
			}
			else
			{
				toastr.error(obj.message, 'Main Category', { "progressBar": true });
			}
		}
		//end show alerts
   });

</script>