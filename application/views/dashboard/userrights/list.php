<div class="row page-titles">
    <div class="col-md-5 align-self-center">
        <h4 class="text-themecolor">User Rights</h4>
    </div>
    <div class="col-md-7 align-self-center text-right">
        <div class="d-flex justify-content-end align-items-center">
            <ol class="breadcrumb">
                <!--<li class="breadcrumb-item"><a href="<?php echo base_url() . 'Userrights/listRecords' ?>">List</a></li>-->
				<li class="breadcrumb-item"><a onclick="loadView('<?php echo base_url() . 'Userrights/listRecords' ?>'); return false;" href="javascript:void(0)">List</a></li>
                <li class="breadcrumb-item active">User Rights</li>
            </ol>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-12">
        <div class="card card-body">
            <h4 class="card-title">User Rights</h4>
            <div class="row">
				<div class="col-sm-6">
					<label>Role</label>
					<select id="role" name="role" class="form-control">
						<option value="">--Select Role--</option>	
						<?php 
							if($roles)
							{
								foreach($roles->result_array() as $s)
								{
									echo '<option value="'.$s['role'].'">'.$s['roleName'].'</option>';
								}
							}
						?>
					</select>
				</div>
				<div class="col-sm-12 mt-3">
					<button id="searchRights" type="button" class="btn btn-info">Search Rights</button>
				</div>
			</div>
			<div class="row">
                <div class="col-sm-12 col-xs-12">
					<h4 class="box-title m-t-20">Rights Details</h4>
                    <hr>
                </div>
				<div class="col-sm-12" id="myform">
					
				</div>
            </div>
        </div>
    </div>
</div>
<script>
    $('.con').bind('keypress', function(e) {
        return validateFloatKeyPress(this, event, 9, -1);
    });
	$("body").on("click","#updateSubmit",function(e) {
		$('#updateSubmit').prop('disabled', true);
		var fd = new FormData();
		var other_data = $('form').serializeArray();
		$.each(other_data,function(key,input){
			fd.append(input.name,input.value);
		});  
		$.ajax({
			type: "POST",
			url: base_path+"Userrights/update",
			enctype: 'multipart/form-data',
			contentType: false,
			processData: false,
			data: fd,
			success: function (data) {
				var obj = JSON.parse(data);				 
				var status = obj.status;
				if(obj.status)
				{  
					toastr.success(obj.message, 'User Rights', { "progressBar": true });    
					loadView('<?php echo base_url() . 'Userrights/listRecords' ?>'); return false;
				} 
				else
				{
					toastr.error(obj.message, 'User Rights', { "progressBar": true });
					loadView('<?php echo base_url() . 'Userrights/listRecords' ?>'); return false;
				}
				$('#updateSubmit').prop('disabled', false);
			}
		});		 
	}); 	
	$(document).ready(function () {
		$("#searchRights").on("click",function(e){			
			var role = $("#role").val();
			if(role!="" && role!=undefined)
			{
				$("#myform").empty();
				$.ajax({
					type: "POST",
					url: base_path+"Userrights/getRightsForRole", 
					data: {'role':role}, 
					success:function(response)
					{
						var result =  JSON.parse(response);
						if(result.status)
						{
							var appendHtml = result.html;
							$("#myform").append(appendHtml);
						} 
					}
				});
			}
		});		
	});
</script>