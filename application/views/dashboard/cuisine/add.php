<h4 class="card-title">Add Cuisine</h4>
<form method="post" id="addForm" enctype="multipart/form-data" novalidate>
 
	<div class="form-row">
		<div class="col-md-12 mb-3">
			<label for="cuisineName">Cuisine Name : <b style="color:red">*</b> </label>
			<input type="text" id="cuisineName" name="cuisineName" class="form-control" required> 
		</div> 
		<div class="col-md-12 mb-3">
			<label for="imagePath">Cuisine Image : <b style="color:red">*</b></label>
			<input type="file" id="imagePath" name="imagePath" class="form-control"  required accept="image/png, image/gif, image/jpeg" >
			
		</div>
	</div>
	<div class="form-row">
	    <span class="text-danger" id="err"></span>
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
		<button type="button" class="btn btn-success" id="addCuisine">Submit</button> 
	</div>
	 
</form>