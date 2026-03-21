						<?php foreach($offerdata->result() as $row) { ?>
						
						<div class="row"> 
							<div class="col-md-6 mb-3">
								<b>For City : </b>
								<p><?= $city ?> </p>
							</div>
							<div class="col-md-6 mb-3">
								<b>Title:</b>
								<p><?= $row->offerTitle?></p>
							</div> 
							<div class="col-md-6 mb-3">
								<b>Start Date : </b>
								<p><?= date('d/m/Y',strtotime($row->startDate))?> </p>
							</div>
							<div class="col-md-6 mb-3">
								<b>End Date :</b>
								<p> <?= date('d/m/Y',strtotime($row->expireDate))?> </p>
							</div>

							<div class="col-md-6 mb-3"> 
								<b>Offer Image: </b>
								<br>
								<?php 
									if($row->image!=""){
										echo '<img src="'.base_url().'uploads/offer/'.$row->image.'" style="height:100px;width:100px;">';
									}
								?>
							</div> 
							<div class="col-md-12 mb-3">
								<b>Description : </b>
								<p><?= $row->description?></p>
							</div>
					 
							<div class="col-md-12 mb-3">
								<b for="offetTerms_condi">Terms & Conditions: </b>
								<p><?= $row->termsCondition?></p>
							</div>
							
							 			
							<div class="form-group">  
								<div class="custom-control custom-checkbox mr-sm-2">
									<div class="custom-control custom-checkbox">
										<input type="checkbox"  value="1" class="custom-control-input" id="isActive" name="isActive" <?php if($row->isActive==1) echo 'checked'; ?>>
										<label class="custom-control-label" for="isActive">Active</label>
									</div>
								</div>
							</div>  
					
						</div>
						<?php } ?>
						  