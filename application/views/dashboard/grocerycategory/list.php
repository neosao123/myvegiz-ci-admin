<div class="page-wrapper">
    <div class="page-breadcrumb">
        <div class="row">
            <div class="col-5 align-self-center">
                <h4 class="page-title">Grocery Category</h4>
                <div class="d-flex align-items-center">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="<?php echo base_url().'index.php/admin/index';?>">Home</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Grocery Category List</li>
                        </ol>

                    </nav>
                </div>
            </div>
            <div class="col-7 align-self-center">
                <div class="d-flex no-block justify-content-end align-items-center">
                    
                    <div class=""><a class="btn btn-myve" href="<?php echo base_url().'index.php/Grocerycategory/add';?>">Create Grocery Category</a></div>
                </div>
            </div>
        </div>
    </div>
            
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">

                    <div class="card-body">
                        <h4 class="card-title">Grocery Category List</h4>
                       
                        
                        <div class="table-responsive">
                            <table id="datatableCategoryG" class="table table-striped table-bordered ">
                                <thead>
                                    <tr>
                                       <th>ID</th>
                                      <th>Code</th>
                                      <th>Main Category</th>
                                      <th>Category Name</th>
                                      <th>Category Short Name</th>
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
					<h4 class="modal-title">View Grocery Category</h4>
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
    loadTable();
    function loadTable(){
		if ($.fn.DataTable.isDataTable("#datatableCategoryG")) {
		  $('#datatableCategoryG').DataTable().clear().destroy();
		}
   var dataTable = $('#datatableCategoryG').DataTable({ 
		stateSave: true,
		"processing":true,  
	   "serverSide":true,  
	   "order":[],
	   "searching": true,
	   "ajax":{  
			url: base_path+"Grocerycategory/getGrocerycategoryList",  
			type:"POST",  
	 "complete": function(response) {
		$(".blue").click(function(){
			 var code=$(this).data('seq');
			 $.ajax({
					url: base_path+"Grocerycategory/view",  
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
 $('.mywarning').on("click", function() {
	var code=$(this).data('seq');
	//alert(code);
		swal({
			title: "You want to delete Category "+code+" ?",
			text: " Category against Product and stock also deleted.",
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
					url: base_path+"Grocerycategory/delete",
					type: 'POST',
					data:{
					  'code':code
					},
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
				swal("Cancelled", "Your Category Record is safe :)", "error");
			}
		});
	});

	}
		   }
	  });
	}
});

$( document ).ready(function() {
	//show alerts
    var data='<?php echo $error; ?>';
    if(data!='')
    {
      var obj=JSON.parse(data);
      if(obj.status)
      {
		  
	
         toastr.success(obj.message, 'Category', { "progressBar": true });
   
      }
      else
      {
		  toastr.error(obj.message, 'Category', { "progressBar": true });
       
      }
    }
	//end show alerts
});
</script>