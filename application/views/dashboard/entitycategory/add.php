<h4 class="card-title">Add Category</h4>
<form method="post" id="saveEntityCategoryForm" enctype="multipart/form-data" novalidate>
<!--action="< ?php echo base_url().'index.php/Vendor/save';?>"-->
	<div class="form-row">
		<div class="col-md-12 mb-3">
			<label for="entityCategoryName">Category Name : <b style="color:red">*</b></label>
			<input type="text" id="entityCategoryName" name="entityCategoryName" class="form-control"  required>
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
		<button type="button" class="btn btn-success" id="saveEntityCategory">Submit</button>
	</div>
</form>