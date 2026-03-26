<style>
input[type=color] {
  height: 35px;
}
</style>
<div class="page-wrapper">  
    <div class="page-breadcrumb">
        <div class="row">
            <div class="col-5 align-self-center">
                <h4 class="page-title">Tag List</h4>
                <div class="d-flex align-items-center">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="<?php echo base_url() . 'admin/index'; ?>">Home</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Tag List</li>
                        </ol>
                    </nav>
                </div>
            </div>
          
        </div>
    </div>
  
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
				<div class="card">
					<div class="card-body">
						<h4 class="card-title">Create Tag</h4>
						<hr/>
						<form class="needs-validation" id="tagForm" method="post">
							<div class="text-danger text-center" id="error_message"></div>
							<div class="form-row">
								<div class="col-md-5 mb-3">
									<label for="cityName">Tag Title: <b style="color:red">*</b></label>                            
									<input type="text" id="tagTitle" name="tagTitle" class="form-control">
									<input type="hidden" id="code" name="code" class="form-control">
								</div>
								<div class="col-md-3 mb-3">
									<label for="cityName">Tag Color: <b style="color:red">*</b></label>                            
									<input type="color" id="tagColor" name="tagColor" class="form-control" value="#ffffff">
								</div>
								<div class="col-md-2 mb-3" style="margin-top:30px;" >
									<div class="custom-control custom-checkbox mr-sm-2">
										<div class="custom-control custom-checkbox">
											<input type="checkbox" value="1" class="custom-control-input" id="isActive" name="isActive">
											<label class="custom-control-label" for="isActive">Active</label>
										</div>
									</div>
								</div>
								<div class="col-md-2 mb-3" style="margin-top:25px;">
									<button type="submit" id="saveBtn" class="btn btn-success" onclick="page_isPostBack=true;">Save</button>
									<button type="reset" class="btn btn-reset">Reset</button>
								</div>
							</div>
						</form>
					</div>
				</div>
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title"> Tag List</h4>
                        <div class="table-responsive">
                            <table id="datatableTag" class="table table-striped table-bordered ">
                                <thead>
                                    <tr>
                                        <th>Sr.No</th>
                                        <th>Code</th>
                                        <th>Tag Title</th>
                                        <th>Tag Color</th>
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
   
</div>
<!-- sample modal content -->
<div id="responsive-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">View Tag</h4>
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
<!-- /.modal -->
	<script>
		   $( document ).ready(function() {
			    loadTable();
				$('#tagForm').submit(function(e){
				  e.preventDefault();
				  var tagTitle=$('#tagTitle').val().trim();
				  var tagColor=$('#tagColor').val().trim();
				  if(tagTitle!='' && tagColor!=''){
					   $.ajax({
							url: base_path+"Tag/save",  
							method:"POST",
							data:$("#tagForm").serialize(),
							beforeSend: function() {
								if($('#saveBtn').text()=='Update'){
									$('#saveBtn').text('Updating');
								}else{
									$('#saveBtn').text('Saving');
								}
								$('#saveBtn').attr("disabled", true);
							},
							success: function(response){
								var obj=JSON.parse(response);
								if(obj.status){
									$('#tagColor').val('');
									$('#tagTitle').val('');
									$('#code').val('');
									$('#isActive').prop('checked',false);
									$('#tagTitle').focus()
									loadTable();
									toastr.success(obj.message, 'Tag', { "progressBar": true });
								}else{
									toastr.error(obj.message, 'Tag', { "progressBar": true });
								}
							},complete: function() {
								$('#saveBtn').text('Save');
								$("#saveBtn").removeAttr("disabled");
							},
						});
				    }else{
						$('#error_message').text("* fields are required")
					}
				});
				function loadTable(){
					if ($.fn.DataTable.isDataTable("#datatableTag")) {
					  $('#datatableTag').DataTable().clear().destroy();
					}
			        var dataTable = $('#datatableTag').DataTable({  
					stateSave: true,
                lengthMenu: [10, 25, 50, 200, 500, 700, 1000],
                processing: true,
                serverSide: true,
                ordering: false,
                searching: true,
                paging: true,
				"ajax":{  
					url: base_path+"Tag/getTagList",  
					type:"POST" , 
				   "complete": function(response) {
					    $(".edit").click(function(){
						 var code=$(this).data('seq');
						 var tagTitle=$(this).data('title');
						 var tagColor=$(this).data('color');
						 var isActive=$(this).data('active');
						 if(isActive==1){
							 $('#isActive').prop('checked',true);
						 }else{
							 $('#isActive').prop('checked',false);
						 }
						 $('#code').val(code)
						 $('#tagTitle').val(tagTitle)
						 $('#tagColor').val(tagColor)
						 $('#saveBtn').text('Update');
						});
					 $(".view").click(function(){
						 var code=$(this).data('seq');
						 $.ajax({
								url: base_path+"Tag/view",  
								method:"POST",
								data:{code:code},
								datatype:"text",
								success: function(data)
								{
									$(".modal-body").html(data);
									
								}
							});
						});
						//delete
						$('.delete').on("click", function() {
							var code=$(this).data('seq');
							swal({
								title: "Are you sure?",
								text: "You want to delete Tag Record of "+code,
								type: "warning",
								showCancelButton: !0,
								confirmButtonColor: "#DD6B55",
								confirmButtonText: "Yes, delete it!",
								cancelButtonText: "No, cancel it!",
								closeOnConfirm: !1,
								closeOnCancel: !1
							}, function(e) {
								if(e){
									$.ajax({
										url: base_path+"Tag/delete",
										type: 'POST',
										data:{
										'code':code
										},
										success: function(data) {
											if(data){
												swal({ 
													title: "Completed",
													text: "Successfully Deleted",
													type: "success" 
												},
												function(isConfirm){
													if (isConfirm) {
														loadTable();
													}
												});
											}else{
												swal("Failed", "Record Not Deleted", "error");
											}
										}
									});
								}else{
									swal("Cancelled", "Your UOM Record is safe :)", "error");
								}
							});
						});	
					}
				   }
			  });
			}   
		   });
		</script>





 

 