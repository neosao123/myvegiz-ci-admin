<h4 class="card-title">Add Food Slider Image</h4>
<form method="post" id="addForm" enctype="multipart/form-data" novalidate>
 
	<div class="form-row">
		<div class="col-md-12 mb-3 d-none">
			<label for="caption">Slider Caption</label>
			<input type="text" id="caption" name="caption" class="form-control"> 
		</div> 
		<div class="col-md-12 mb-3">
			<label for="imagePath">Category Image : <b style="color:red">*</b></label>
			<input type="file" id="imagePath" name="imagePath" class="form-control"  required>
			<span class="text-danger" id="err"></span>
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
		<button type="button" class="btn btn-success" id="addSliderPhoto">Submit</button> 
	</div>
	 
</form>