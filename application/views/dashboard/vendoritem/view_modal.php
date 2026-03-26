<?php if ($query) {
	foreach ($query->result() as $row) {  ?>
		<div class="form-row">
			<div class="col-md-7 mb-3">
				<label for="itemName">Item Name :</label>
				<input type="text" id="itemName" name="itemName" value="<?= $row->itemName ?>" class="form-control-line" readonly>
			</div>
			<div class="col-md-5 mb-3">
				<label for="vendorName">Vendor Name : </label>
				<input type="text" id="vendorName" name="vendorName" value="<?= $row->entityName ?>" class="form-control-line" readonly>
			</div>
		</div>
		<div class="form-row">
			<div class="col-md-4 mb-3">
				<label for="salePrice">Sale Price:</label>
				<input type="text" id="salePrice" name="salePrice" value="<?= $row->salePrice ?>" class="form-control-line" readonly>
			</div>

			<div class="col-md-4 mb-3">
				<label for="itemPackagingPrice">Packing Charges:</label>
				<input type="text" id="itemPackagingPrice" name="itemPackagingPrice" value="<?= $row->itemPackagingPrice ?>" class="form-control-line" readonly>
			</div>

			<div class="col-md-4 mb-3">
				<label for="maxOrderQty">Maximum Order Quantity:</label>
				<input type="text" id="maxOrderQty" name="maxOrderQty" value="<?= $row->maxOrderQty ?>" class="form-control-line" readonly>
			</div>

			<div class="col-md-4 mb-3">
				<label for="menuCategoryName">Menu Sub Category : </label>
				<input type="text" id="menuCategoryName" name="menuCategoryName" value="<?= $row->menuCategoryName ?>" class="form-control-line" readonly>
			</div>

			<div class="col-md-4 mb-3">
				<label for="menuSubCategoryName">Menu Sub Category :</label>
				<input type="text" id="menuSubCategoryName" name="menuSubCategoryName" value="<?= $row->menuSubCategoryName ?>" class="form-control-line" readonly>
			</div>
		</div>
		<div class="form-row">
			<div class="col-md-12 mb-3">
				<label for="itemDescription">Item Description : </label>
				<p class="border-bottom"><?= $row->itemDescription ?></p>
			</div>
		</div>
		<div class="row">
			<?php if ($row->itemPhoto != "") { ?>
				<div class="col-md-4 mb-3">
					<label for="entityImage"> Item Image :</label>
					<div class="controls">
						<img src="<?php echo base_url() . 'partner/uploads/' . $row->vendorCode . '/vendoritem/' . $row->itemPhoto; ?>" id="entityImageShow" alt="Entity Image" height="120" width="120">
					</div>
				</div>
			<?php } ?>
		</div>
		<div class="row">
			<div class="col-md-4">
				<label for="entityImage"> Approved :</label>
				<?php
				if ($row->isAdminApproved == "1") {
					echo "<span class='label label-sm label-success'>Yes</span>";
				} else {
					echo "<span class='label label-sm label-warning'>No</span>";
				}
				?>
			</div>
			<div class="col-md-4">
				<label for="entityImage"> Item Status :</label>
				<?php
				if ($row->itemActiveStatus == "1") {
					echo "<span class='label label-sm label-success'>Active</span>";
				} else {
					echo "<span class='label label-sm label-warning'>Inactive</span>";
				}
				?>
			</div>
			<div class="col-md-4">
				<label for="entityImage"> Active :</label>
				<?php
				if ($row->isActive == "1") {
					echo "<span class='label label-sm label-success'>Yes</span>";
				} else {
					echo "<span class='label label-sm label-warning'>No</span>";
				}
				?>
			</div>
		</div>
<?php }
} ?>
