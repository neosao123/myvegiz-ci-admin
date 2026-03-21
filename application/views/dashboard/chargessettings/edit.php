 <div class="page-wrapper">
		
		<div class="page-breadcrumb">
			<div class="row">
				<div class="col-12 align-self-center">
					<h4 class="page-title">Charges Settings</h4>
					<div class="d-flex align-items-center">
						<nav aria-label="breadcrumb">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="#">Home</a></li>
								<li class="breadcrumb-item active" aria-current="page">Charges Settings</li>
							</ol>
						</nav>
					</div>
				</div>
			</div>
		</div>
		
		<div class="container-fluid col-md-6">
		
			<div class="card">
				<div class="card-body">
					<h4 class="card-title">Edit Charges Settings</h4>
					<hr/>
					
					<form class="needs-validation" method="post" id="myForm" enctype="multipart/form-data" action="<?php echo base_url().'chargessettings/update';?>" novalidate>
				      <?php foreach($query->result() as $row) {?>
					  
						  
						<?php
						echo "<div class='text-danger text-center' id='error_message'>";
						if (isset($error_message)) {
							echo '<b>' . $error_message . '</b>';
						}
						echo "</div>";
						
						?>
						<div class="form-row">
							<div class="col-md-12 mb-3 d-none">
								<label for="code">Code :</label>
								<input type="text" id="code" name="code" value="<?=$row->code?>" class="form-control" readonly>
								<div class="invalid-feedback">
									Required Field!
								</div>
							</div>
						</div>
						<div class="form-row">
							<div class="col-md-12 mb-3">
								<label >City Name:</label>
							    <input type="text" id="cityName" name="cityName" value="<?=$row->cityName?>" class="form-control" readonly>
																
							</div>
							
							<div class="col-md-12 mb-3">
							    <?php if($row->forWhichService=="customer_vegee_grocery" || $row->forWhichService=="customer_food"){ ?>
								<label>Delivery Executive Charge :</label>
								<?php } ?>
								 <?php if($row->forWhichService=="deliveryboyr_commission_vegee" || $row->forWhichService=="deliveryboyr_commission_food"){ ?>
								<label>Delivery Executive Commission :</label>
								<?php } ?>
								<input type="text" id="service" name="service" value="<?=ucwords(str_replace('_',' ', $row->forWhichService))?>" class="form-control" readonly>
	
							</div>
						 </div>
						<div class="col-md-7 mb-3">
								<label>Is Fixed Charges : <b style="color:red">*</b></label>
								<div class="custom-control custom-radio">
									<input type="radio" id="isFixedChargesYes" name="isFixedCharges" value="1" <?= $row->isFixedChargesFlag == "1" ? 'checked' : '' ?> class="custom-control-input">
									<label class="custom-control-label" for="isFixedChargesYes">Yes</label>
								</div>
								<div class="custom-control custom-radio">
									<input type="radio" id="isFixedChargesNo" name="isFixedCharges" value="0" <?= $row->isFixedChargesFlag == "0" ? 'checked' : '' ?> class="custom-control-input">
									<label class="custom-control-label" for="isFixedChargesNo">No</label>
								</div>
							</div>
						<div class="form-row" id="showFixedChargesYes" style="display:<?= $row->isFixedChargesFlag == "1" ? 'block' : 'none' ?>">
							<div class="col-md-12 mb-3">
								<label >Minimum Order Amount For Fixed Charge :<b style="color:red">*</b></label>
								<input type="number" id="minOrderAmountForFixedCharge" name="minOrderAmountForFixedCharge" value="<?=$row->minOrderAmountForFixedCharge?>" class="form-control" <?= $row->isFixedChargesFlag == "1" ? 'required' : '' ?>>
								
							</div>
							<div class="col-md-12 mb-3">
								<label >Fixed Charges or Commission:<b style="color:red">*</b></label>
								<input type="number" id="fixedChargesOrCommission" name="fixedChargesOrCommission" value="<?=$row->fixedChargesOrCommission?>" class="form-control" <?= $row->isFixedChargesFlag == "1" ? 'required' : '' ?>>
								
							</div>
					    </div>
						<div class="form-row" id="showFixedChargesNo" style="display:<?= $row->isFixedChargesFlag == "0" ? 'block' : 'none' ?>">
							<div class="col-md-12 mb-3">
								<label >Minimum Km for Fixed Charges:<b style="color:red">*</b></label>
								<input type="number" id="minimumKmForFixedCharges" name="minimumKmForFixedCharges" value="<?=$row->minimumKmForFixedCharges?>" class="form-control" <?= $row->isFixedChargesFlag == "0" ? 'required' : '' ?>>
								
							</div>
							<div class="col-md-12 mb-3">
								<label >Minimum Charges for Fixed Km:<b style="color:red">*</b></label>
								<input type="number" id="minimumChargesForFixedKm" name="minimumChargesForFixedKm" value="<?=$row->minimumChargesForFixedKm?>" class="form-control" <?= $row->isFixedChargesFlag == "0" ? 'required' : '' ?>>
								
							</div>
							<div class="col-md-12 mb-3">
								<label >Per Km Charges:<b style="color:red">*</b></label>
								<input type="number" id="perKmCharges" name="perKmCharges" value="<?=$row->perKmCharges?>" class="form-control" <?= $row->isFixedChargesFlag == "0" ? 'required' : '' ?>>
								
							</div>
						</div>		
						<div class="form-group">
							<div class="custom-control custom-checkbox mr-sm-2">
								<div class="custom-control custom-checkbox">
									
								
										<?php 
											if($row->isActive == "1"){
												echo "<input type='checkbox'   value='1' class='custom-control-input' id='isActive' name='isActive' checked>
												<label class='custom-control-label' for='isActive'>Active</label>";
											}
											else{ 
												echo "<input type='checkbox' value='1' class='custom-control-input' id='isActive' name='isActive'>
												<label class='custom-control-label' for='isActive'>Active</label>";
											}
										?>
								
								</div>
							</div>
						</div>
					  <?php } ?>
						<div class="text-xs-right">
							
							<button type="submit" class="btn btn-success" onclick="page_isPostBack=true;">Update</button>
							
						</div>
					</form>
				</div>
			</div>
		</div>
	
	</div>

<script type="text/javascript">	
$(document).ready(function(e) {
	$("body").on("change", "input[name=isFixedCharges]", function(e) {
		if ($(this).is(":checked")) {
			var thisVal = $(this).val();
			if (thisVal == "1") {
				$("#showFixedChargesYes").show();
				$("#showFixedChargesNo").hide();
				$("#minOrderAmountForFixedCharge").attr("required", true);
				$("#fixedChargesOrCommission").attr("required", true);
				$("#minimumKmForFixedCharges").removeAttr("required");
				$("#minimumChargesForFixedKm").removeAttr("required");
				$("#perKmCharges").removeAttr("required");
			} else {
				$("#showFixedChargesYes").hide();
				$("#showFixedChargesNo").show();
				$("#minimumKmForFixedCharges").attr("required", true);
				$("#minimumChargesForFixedKm").attr("required", true);
				$("#perKmCharges").attr("required", true);
				$("#minOrderAmountForFixedCharge").removeAttr("required");
				$("#fixedChargesOrCommission").removeAttr("required");
			}
		}
	});
});
</script>










