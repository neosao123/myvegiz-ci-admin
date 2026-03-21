<style>
	.select2-container--classic .select2-selection--single, .select2-container--default .select2-selection--multiple, .select2-container--default .select2-selection--single, .select2-container--default .select2-selection--single .select2-selection__arrow, .select2-container--default .select2-selection--single .select2-selection__rendered {
		border-color: rgba(0,0,0,0.25);
		height: auto;  			 
	}
	.select2-container--default .select2-selection--multiple .select2-selection__choice {
		background:#588002
	}
	.select2-container--default .select2-results__option--highlighted[aria-selected],.select2-container--default .select2-results__option[aria-selected=true]  {background:#96bf3fbd}
	.select2-container--default .select2-selection--multiple .select2-selection__choice > span { color:white!important;forn-weight:bold}
</style>
        <!-- ============================================================== -->
        <!-- Page wrapper  -->
        <!-- ============================================================== -->
        <div class="page-wrapper">
            <!-- ============================================================== -->
            <!-- Bread crumb and right sidebar toggle -->
            <!-- ============================================================== -->
            <div class="page-breadcrumb">
                <div class="row">
                    <div class="col-5 align-self-center">
                        <h4 class="page-title">Notification</h4>
                        <div class="d-flex align-items-center">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="<?php echo base_url().'index.php/admin/index';?>">Home</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Notification</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
            <!-- ============================================================== -->
            <!-- End Bread crumb and right sidebar toggle -->
            <!-- ============================================================== -->
            <!-- ============================================================== -->
            <!-- Container fluid  -->
            <!-- ============================================================== -->
            <div class="container-fluid">
                <!-- ============================================================== -->
                <!-- Start Page Content -->
                <!-- ============================================================== -->
                <!-- basic table -->
				
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title"> Notification </h4>
							
									<!-- form start -->
									<form class="form-horizontal" action="<?= base_url().'CustomerNotification/notificationprocess'?>" method="post" enctype="multipart/form-data">
									<div class="form-row">
											<div class="form-group col-sm-4">
												<label for="inputEmail3" class="control-label">Title : <b style="color:red">*</b></label>
												<input type="text" class="form-control" id="inputEmail3" placeholder="Title" name="title" required >
											</div>
											
											<div class="form-group col-sm-8">
												<label for="inputPassword3" class="control-label">Message : <b style="color:red">*</b></label>
												<input type="text" class="form-control" id="inputPassword3" placeholder="Message"  name="msg" required>
											</div>
										</div>
										<div class="form-row">
											<div class="col-md-4 mb-3">						
                                                <label for="place">City : <b style="color:red">*</b></label>							
                                                <div class="controls">
                                                    <select  id="cityCode" name="cityCode" class="form-control" required>
                                                        <option value="">Select City</option>
                                                        <?php
                                                        if (isset($city)) {
                                                            foreach ($city->result() as $c) {
                                                                $selected = $c->code == $row->cityCode ? "selected" : "";
                                                                echo '<option value="' . $c->code . '" ' . $selected . '>' . $c->cityName . '</option>';
                                                            }
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                            
                                             <div class="col-md-8 mb-3">							
                    							<label for="address">Places where services available : </label>
                    							<select class="form-control js-example-responsive" name="addressCode[]"  id="addressCode" multiple="multiple" data-border-color="primary" data-border-variation="accent-2"   style="width:100%" >
                    							</select>
                    							<div class="invalid-feedback">
                    								Required Field!
                    							</div>								
                    						</div>
											
									</div>
									<div class="form-row">
										<div class="col-md-12 mb-3">							
                							<label for="address">Customers : </label>
                							<select class="form-control js-example-responsive" name="clientCodes[]"  id="clientCodes" multiple="multiple" data-border-color="primary" data-border-variation="accent-2"   style="width:100%" >
                							</select>
                							<div class="invalid-feedback">
                								Required Field!
                							</div>								
                						</div>
                    				</div>
									<div class="form-row">	
                					
                						<div class="form-group col-sm-4">
											<label for="inputEmail3" class="control-label">Select Image(PNG,JPG) : </label>
												<input type="file" id="notificationImage" class="form-control" name="notificationImage" accept="image/jpeg, image/png"  onchange="readURL(this);">
										
										</div>
										<div class="form-group col-sm-8">
											<label for="inputEmail3" class="control-label">Selectd Image: </label>
											<center><br><img id="immg" src="<?=base_url().'assets/images/preview-icon.png'?>" height="150" width="300" > </center> <br> 
										</div>	
									</div>
									<div class="form-row">		
										<div class="form-group col-md-4">
											<div class="col-sm-10">
											    <center>
												    <input type="submit" class="btn btn-primary " value="Send Notification" name="simple">
												</center> 
											</div>
										</div>
									</div>
									</form> 
							</div>
						</div>			 
            <!-- ============================================================== -->
            <!-- End Container fluid  -->
            <!-- ============================================================== -->
			<!-- sample modal content -->
	</div>
</div>
<div id="responsive-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none; ">
	<div class="modal-dialog">	
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">View Employee</h4>
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
<script>
	$(document).ready(function(){
	    $("#addressCode").select2({});
	    $("#clientCodes").select2({});
	    $("#productCode").select2({});
	    var previous;
	    $("#cityCode").on('focus', function () {
			// Store the current value on focus and on change
			previous = this.value;
		
		   
		}).change(function() {
			var cityCode= $(this).val().trim();
			if(cityCode!=""){
			    
				$("#addressCode").select2({
					ajax: { 
						url:  base_path+'CustomerNotification/getAddressByCity',
						type: "get",
						delay:250,
						dataType: 'json',
						data: function (params) {
							var query = {
                                search: params.term,
								cityCode:cityCode // search term
							};
							return query;
						}, 
						processResults: function (response) {
							return {
								results: response
							};
						},
						cache: true
					}
				}).on("select2:select", function (e) { 
                  var select_val = $(e.currentTarget).val();
                  console.log(select_val)
                   getClients(cityCode,select_val);
                });
				
				 getClients(cityCode,"");
			}
			else
			{
		    	$('#clientCodes').val(null).trigger('change');
		        $('#addressCode').val(null).trigger('change');
			}
		});
	    
	    function getClients(cityCode,areaCodes)
	    {
	        $("#clientCodes").select2({
				ajax: {
					url:  base_path+'CustomerNotification/getCustomersListByCityArea',
					type: "get",
					delay:250,
					dataType: 'json',
					data: function (params) {
						var query = {
                            search: params.term,
							cityCode:cityCode,
							areaCodes:areaCodes// search term
                          }
                    
                          // Query parameters will be ?search=[term]&type=public
                          return query;
					}, 
					processResults: function (response) {
						console.log(response);
						return {
							results: response
						};
					},
					cache: true
				}
				});
	    }
	    
	    $("#simple").show();
	    	$("#custom").show();
	});
	$( document ).ready(function() {
		<?php if ($this->session->flashdata('message')) { ?>
		var alertmsg = '<?php echo $this->session->flashdata('message'); ?>';
		if (alertmsg)
		{
			toastr.success(alertmsg, 'Notification', { "progressBar": true });
		}
		else {
			toastr.error(alertmsg, 'Notification', { "progressBar": true });
		}
		<?php } ?>
		
		
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
   
     function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function (e) {
                $('#immg')
                    .attr('src', e.target.result)
                    .width(400)
                    .height(300);
            };

            reader.readAsDataURL(input.files[0]);
        }
    }
    
</script>
	