<h4 class="card-title">Add App Alert</h4>
<form class="needs-validation" method="post" id="saveAppAlertForm" enctype="multipart/form-data" novalidate>
<!--action="< ?php echo base_url().'index.php/Vendor/save';?>"-->
	<div class="form-row">
		<div class="col-md-12 mb-3">
			<label for="title">Title : <b style="color:red">*</b></label>
			<input type="text" id="title" name="title" class="form-control"  required>
			<div class="invalid-feedback">
								Required Field!
							</div>
		</div>
	</div>
	<div class="form-row">
		<div class="col-md-12 mb-3">
			<label for="description">Description : <b style="color:red">*</b></label>
			<input type="description" id="description" name="description" class="form-control"  required>
			<div class="invalid-feedback">
								Required Field!
							</div>
		</div>
	</div>
	<div class="form-group">
		<div class="custom-control custom-checkbox mr-sm-2">
			<div class="custom-control custom-checkbox">
				<input type="checkbox" value="1" class="custom-control-input" id="isActive" name="isActive">
				<label class="custom-control-label" for="isActive">Active</label>
			</div>
		</div>
		
	</div>
	<div class="text-xs-right">	
		<button type="button" class="btn btn-success" id="saveAppAlert">Submit</button>
	</div>
</form>



