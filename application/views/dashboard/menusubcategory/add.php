<h4 class="card-title">Add Sub Category</h4>
<form method="post" id="saveMenuSubCategoryForm" enctype="multipart/form-data" novalidate>
 	<div class="form-row">
		<div class="col-md-12 mb-3">
			<label for="menuCategoryCode">Category : <b style="color:red">*</b></label>
			<select id="menuCategoryCode" name="menuCategoryCode" class="form-control" required>
				<option value="">Select Menu Category</option>
				<?php 
					if($query1)
					{
						
						foreach($query1->result_array() as $r)
						{
							echo '<option value="'.$r['code'].'">'.$r['menuCategoryName'].'</option>';
						}
					}
				?>
			</select>
			<span class="text-danger" id="err"></span>
		</div>
	</div>
	<div class="form-row">
		<div class="col-md-12 mb-3">
			<label for="menuSubCategoryName">Sub Category Name : <b style="color:red">*</b></label>
			<input type="text" id="menuSubCategoryName" name="menuSubCategoryName" class="form-control" required>
			<span class="text-danger" id="errSubCategory"></span>
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
		<button type="button" class="btn btn-success" id="saveMenuSubCategory">Submit</button>
	</div>
</form>