<h4 class="card-title">Update Category</h4>
<form method="post" id="updateMenuSubCategoryForm" enctype="multipart/form-data" novalidate>
<!--action="< ?php echo base_url().'index.php/Vendor/save';?>"-->
	<?php foreach($query->result() as $row){  ?>
	<div class="form-row">
		<div class="col-md-12 mb-3">
			<label for="menuCategoryCode">Category : <b style="color:red">*</b></label>
			<select type="text" id="menuCategoryCode" name="menuCategoryCode" class="form-control"  required>
				<option value="">Select Menu Category</option>
				<?php 
					if($query1)
					{
						foreach($query1->result_array() as $r)
						{   
						    $select = $row->menuCategoryCode == $r['code'] ? 'selected' : '';
							echo '<option value="'.$r['code'].'" '.$select.'>'.$r['menuCategoryName'].'</option>';
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
			<input type="text" id="menuSubCategoryName" value="<?=$row->menuSubCategoryName?>" name="menuSubCategoryName" class="form-control"  required>
			<span class="text-danger" id="err"></span>
		</div>
	</div>
	<div class="form-group">
		<div class="custom-control custom-checkbox mr-sm-2">
			<div class="custom-control custom-checkbox">
				<?php 
					if($row->isActive == "1"){
						echo "<input type='checkbox' value='1' class='custom-control-input' id='isActive' name='isActive' checked>
						<label class='custom-control-label' for='isActive'>Active</label>";
					}else{ 
						echo "<input type='checkbox' value='1' class='custom-control-input' id='isActive' name='isActive'>
						<label class='custom-control-label' for='isActive'>Active</label>";
					}
				?>
			</div>
		</div>
	</div>
	<input type="hidden" id="code" name="code" value="<?=$row->code?>" class="form-control" readonly>
	<div class="text-xs-right">	
		<button type="button" class="btn btn-success" id="updateMenuSubCategory">Submit</button>
		<button type="button" class="btn btn-reset" id="backToAdd">Add New</button>
	</div>
	<?php } ?>
</form>