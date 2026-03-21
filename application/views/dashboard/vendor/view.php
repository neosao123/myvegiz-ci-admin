<div class="page-wrapper">
		
	<div class="page-breadcrumb">
		<div class="row">
			<div class="col-12 align-self-center">
				<h4 class="page-title">Vendor</h4>
				<div class="d-flex align-items-center">
					<nav aria-label="breadcrumb">
						<ol class="breadcrumb">
							<li class="breadcrumb-item"><a href="#">Home</a></li>
							<li class="breadcrumb-item active" aria-current="page">View Vendor</li>
						</ol>
					</nav>
				</div>
			</div>
		</div>
	</div>
		
	<div class="container-fluid col-md-8">
		<div class="card">
			<div class="card-body">
				<h4 class="card-title">View Vendor</h4>
				<hr/>
				
				<form class="needs-validation" method="post" id="myForm" enctype="multipart/form-data" action="<?php echo base_url().'index.php/Vendor/update';?>" novalidate>
					<?php foreach($query->result() as $row){  ?>
						
					<div class="form-row">
						<div class="col-md-4 mb-3">
							<label for="firstName">First Name :</label>
							<input type="text" id="firstName" name="firstName" value="<?=$row->firstName?>" class="form-control-line" required readonly>
						</div>
						
						<div class="col-md-4 mb-3">
							<label for="middleName">Middle Name : </label>
							<input type="text" id="middleName" name="middleName" value="<?=$row->middleName?>" class="form-control-line" readonly>
						</div>
						
						<div class="col-md-4 mb-3">
							<label for="lastName">Last Name :</label>
							<input type="text" id="lastName" name="lastName" value="<?=$row->lastName?>" class="form-control-line" readonly required>
						</div>
					</div>

					<div class="form-row">
						<div class="col-md-12 mb-3">
							<label for="entityName">Entity Name : </label>
							<input type="text" id="entityName" name="entityName" value="<?=$row->entityName?>" class="form-control-line" readonly  required>
						</div>
					</div>
										
					<div class="form-row">
						<div class="col-md-12 mb-3">
							<label for="address">Address : </label>
							<input type="text" id="address" name="address" value="<?=$row->address?>" class="form-control-line" readonly>
						</div>
					</div>
					
					<div class="form-row">
						<div class="col-md-4 mb-3">
							<label for="ownerContact">Owner Contact :</label>
							<input type="text" id="ownerContact" name="ownerContact" value="<?=$row->ownerContact?>" class="form-control-line" required readonly>
						</div>
						
						<div class="col-md-4 mb-3">
							<label for="entityContact">Entity Contact : </label>
							<input type="text" id="entityContact" name="entityContact" value="<?=$row->entityContact?>" class="form-control-line" readonly>
						</div>
						
						<div class="col-md-4 mb-3">
							<label for="entitycategoryCode">Entity Category :</label>
							<input type="text" value="<?=$entitycategory->result_array()[0]['entityCategoryName']?>" class="form-control-line" readonly required>
						</div>
					</div>
					 
					<div class="form-row">
						<div class="col-md-6 mb-3">
							<label for="fssaiNumber"> FSSAI Number : </label>
							<input type="text" id="fssaiNumber" name="fssaiNumber" value="<?=$row->fssaiNumber?>" class="form-control-line" readonly required>
						</div>
						
						<div class="col-md-6 mb-3">
							<label for="gstNumber"> GST Number :</label>
							<input type="text" id="gstNumber" name="gstNumber" value="<?=$row->gstNumber?>" class="form-control-line" readonly>
						</div>
					</div>
					
					<div class="form-row">
						<div class="col-md-6 mb-3">
							<label for="latitude"> Latitude : </label>
							<input type="text" id="latitude" name="latitude" value="<?=$row->latitude?>" class="form-control-line" readonly>
						</div>
						
						<div class="col-md-6 mb-3">
							<label for="longitude"> Longitude :</label>
							<input type="text" id="longitude" name="longitude" value="<?=$row->longitude?>" class="form-control-line" readonly>
						</div>
						
					</div>
					<div class="form-row">
					    <div class="col-md-12 mb-3">
                            <label for="cuisineCode"> Type of cuisines served :</label></br>					            
					        <?php
					            if($cuisineslines)
					            {
					               foreach($cuisineslines->result_array() as $c)
					               {
					                    echo '<span class="btn btn-success btn-sm mr-2">'.$c['cuisineName'].'</span>';
					               }
					            }
					        ?>
						</div>
					</div>
					<div class="form-row">
						<?php if($row->entityImage!=""){ ?>
						<div class="col-md-4 mb-3 text-center">
							<label for="entityImage"> Entity Image :</label>
							<div class="controls">
							<img src="<?php echo base_url().'uploads/vendor/'.$row->code.'/'.$row->entityImage;?>" id="entityImageShow" alt="Entity Image" height="120" width="120">
							</div>
						</div>
						<?php } ?>
						<?php if($row->fssaiImage!=""){ ?>
						<div class="col-md-4 mb-3 text-center">
							<label for="fssaiImage"> FSSAI Image :</label>
							<div class="controls">
							<img src="<?php echo base_url().'uploads/vendor/'.$row->code.'/'.$row->fssaiImage;?>" id="fssaiImageShow" alt="FSSAI Image" height="120" width="120">
							</div>
						</div>
						<?php } ?>
						<?php if($row->gstImage!=""){ ?>
						<div class="col-md-4 mb-3 text-center">
							<label for="gstImage"> GST Image :</label>
							<div class="controls">
							<img src="<?php echo base_url().'uploads/vendor/'.$row->code.'/'.$row->gstImage;?>" id="gstImageShow" alt="GST Image" height="120" width="120">
							</div>
						</div>
						<?php } ?>
					</div>  
					<h4> Configuration Settings </h4>
					<div class="row">
						<div class="col-md-7 mb-3">
							<label for="address">Delivery Packaging Type : <b style="color:red">*</b></label>
							<div class="custom-control custom-radio">
								<input type="radio" id="productPacking" name="packagingType" value="PRODUCT" <?=$row->packagingType=="PRODUCT"?'checked':''?> class="custom-control-input">
								<label class="custom-control-label" for="productPacking">Product Wise</label>
							</div>
							<div class="custom-control custom-radio">
								<input type="radio" id="cartPacking" name="packagingType" value="CART" <?=$row->packagingType=="CART"?'checked':''?> class="custom-control-input">
								<label class="custom-control-label" for="cartPacking">Cart Wise</label>
							</div>
						</div>
						<div class="col-md-5 mb-3" id="cartPack" style="display:<?=$row->packagingType=="CART" ? 'block': 'none' ?>">
							<label for="ownerContact">Delivery Packing Price</label>
							<input type="text" id="cartPackagingPrice" maxlength="3" name="cartPackagingPrice" class="form-control-line" readonly value="<?=$row->cartPackagingPrice?>">
							<div class="invalid-feedback">
								Required Field!
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-7 mb-3">
							<label>GST Applicable Type : </label>
							<div class="custom-control custom-radio">
								<input type="radio" id="gstApplicableNo" readonly name="gstApplicable" value="NO" <?=$row->gstApplicable=="NO"?'checked':''?> class="custom-control-input">
								<label class="custom-control-label" for="gstApplicableNo">No (Not Applicable)</label>
							</div>	
							<div class="custom-control custom-radio">
								<input type="radio" id="gstApplicableYes" readonly name="gstApplicable" value="YES" <?=$row->gstApplicable=="YES"?'checked':''?> class="custom-control-input">
								<label class="custom-control-label" for="gstApplicableYes">Yes (Applicable)</label>
							</div>											
						</div>
						<div class="col-md-5 mb-3">
							<label for="gstPercent">GST (%)</label>
							<input type="text" id="gstPercent" maxlength="3" name="gstPercent" class="form-control-line" readonly value="<?=$row->gstPercent<0 || $row->gstPercent==null  ? 0 .' %':$row->gstPercent. ' %'?>">
						</div>
					</div>
					<?php
				        $bankDetails= $row->bankDetails;
				        $beneficiaryName = $bankName = $accountNumber = $ifscCode ="";
				        if($bankDetails!="")
				        {
				            $bankDetails = json_decode($bankDetails);
				            $beneficiaryName = $bankDetails->beneficiaryName;
				            $bankName = $bankDetails->bankName;
				            $accountNumber = $bankDetails->accountNumber;
				            $ifscCode =$bankDetails->ifscCode;
				        }
				    ?> 
				    <h4> Bank Details </h4>
					<div class="form-row">
						<div class="col-md-6 mb-3">
							<label for="beneficiaryName"> Beneficiary Name : </label>
							<input type="text" id="beneficiaryName" name="beneficiaryName" value="<?=$beneficiaryName?>" class="form-control-line" readonly>
						</div>
						
						<div class="col-md-6 mb-3">
							<label for="bankName"> Name of Bank :</label>
							<input type="text" id="bankName" name="bankName" value="<?=$bankName?>" class="form-control-line" readonly>
						</div>
						
						<div class="col-md-6 mb-3">
							<label for="accountNumber"> Account Number :</label>
							<input type="number" id="accountNumber" name="accountNumber" value="<?=$accountNumber?>" class="form-control-line" readonly>
						</div>
						
						<div class="col-md-6 mb-3">
							<label for="ifscCode"> IFSC Code :</label>
							<input type="text" id="ifscCode" name="ifscCode" value="<?=$ifscCode?>" class="form-control-line" readonly>
						</div>
					</div>
					<div class="form-group">
						<div class="custom-control custom-checkbox mr-sm-2">
							<label for="ownerContact">Active Status</label>
							<?php 
								if($row->isActive == "1"){
									echo "<span class='label label-sm label-success'>Active</span>";
								}else{ 
									echo "<span class='label label-sm label-warning'>Inactive</span>";
								}
							?>
						</div>
						<div class="custom-control custom-checkbox mr-sm-2">
							<label for="ownerContact">Manually Servicable</label>
							<?php 
								if($row->manualIsServiceable == "1"){
									echo "<span class='label label-sm label-success'>Yes</span>";
								}else{ 
									echo "<span class='label label-sm label-warning'>No</span>";
								}
							?>
						</div>
						<div class="custom-control custom-checkbox mr-sm-2">
							<label for="ownerContact">Popular</label>
							<?php 
								if($row->isPopular == "1"){
									echo "<span class='label label-sm label-success'>Yes</span>";
								}else{ 
									echo "<span class='label label-sm label-warning'>No</span>";
								}
							?>
						</div>
					</div>
						<?php 
						if($tags->num_rows()>0){ 
						$str1='';
							if($row->tagCode==NULL || $row->tagCode==''){
								$str1='checked';
							}
						?>
					<div class="form-group">
						<div class="row">
							<div class="col-sm-2 mb-1">
								<label for="testType1"> Tags:</label>	
							</div>
						</div>
						<div class="row" style="margin-left:35px;">
							<div class="col-sm-3 mb-3">
								 <div class="custom-control custom-radio custom-control-inline">
									<input type="radio" class="custom-control-input" id="tagSection" <?= $str1?> name="tagCode" value="1">
									<label class="custom-control-label"  for="tagSection"><b>No Tag</b></label>
								</div>
							</div>
							<?php 
								foreach($tags->result_array() as $tag){
									$str = '';
									if($tag['code']==$row->tagCode){
										$str = 'checked';
									}
							?>
							<div class="col-sm-3 mb-3">
								 <div class="custom-control custom-radio custom-control-inline">
									<input type="radio" disabled class="custom-control-input" id="tagSection<?= $tag['code']?>" <?= $str?> name="tagCode" value="<?= $tag['code']?>">
									<label class="custom-control-label" style="color:<?= $tag['tagColor']?>" for="tagSection<?= $tag['code']?>"><b><?= $tag['tagTitle']?></b></label>
								</div>
							</div>
							<?php }?>
						</div>
					</div>
						<?php }?>
					<?php }?>
				</form>
			</div>
		</div>
	</div>
</div>