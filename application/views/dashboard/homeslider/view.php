<?php 
if($result) 
{ 
	$row = $result; 
?>
<div class="form-row">
	<div class="col-md-12 mb-3">
		<label><b> Code:</b> </label>
		<input type="text" value="<?= $row['code'] ?>" class="form-control-line"  readonly>
	</div> 
	<?php 
		$imagePath=$row['imagePath']; 
		if($imagePath!="")
		{
			$imagePath = 'uploads/homeslider/'.$imagePath;
			if(file_exists($imagePath)) $imagePath = base_url($imagePath);
			else  $imagePath =  base_url().'assets/admin/assets/images/users/1.jpg';
		} 
		else 
		{
			$imagePath =  base_url().'assets/admin/assets/images/users/1.jpg';
		}
	?>		
	<div class="col-md-12 mb-3">
		<label><b>Slider Image:</b></label><br/>
		<img src ="<?= $imagePath?>" alt="Profile Photo" class="img-fluid">						 
	</div>
	<?php  
		$activeStatus = $row['isActive']==1 ? '<label class="label label-success">Active</label>' :'<label class="label label-danger">IN-Active</label>' ; 
	?>
	<div class="form-group"> <label> Status </label><br/><?= $activeStatus ?></div>
</div>
<?php 
}
?>
 