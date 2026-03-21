<?php 
$i=1;
$points = 0;
if($commissionData){
	?>
<table class="table table-bordered table-sm table-condensed table-responsive">
	<tr>
		<th>Sr.No.</th>
		<th>Payment Status</th>
		<th>Order Code</th>
		<th>Order Date</th>
		<th>Total Order Amount</th>
		<th>Sub Total Amount</th> 
	    <th>Commission Amount</th>									
	    <th>Commission Percentage (%)</th>									
		<th>Vendor Amount</th>
		
	</tr>
	</thead>
<?php 
$paidCount=0;
$totalOrders = count($commissionData->result_array());
	foreach($commissionData->result() as $r){
		
		?>
		<tr>
		<td><?= $i ?></td>
		<?php 
			$paidStatus = '';
			if($r->isPaid == 1) {
				$paidCount++;
				$paidStatus = " <span class='label label-sm label-success'>Paid</span>";
			} else {
				$paidStatus = " <span class='label label-sm label-warning'>Unpaid</span>";
			}?>
		<td><?= $paidStatus ?></td>
		<td><?= $r->orderCode ?></td>
		<td><?= date('d/m/Y h:i A',strtotime($r->addDate)) ?></td>
		<td><?= $r->grandTotal ?></td>
		<td><?= $r->subTotal ?></td>
		<td><?= $r->comissionAmount;?></td>
		<td><?= $r->comissionPercentage;?>(%)</td>
		<td><?= $r->vendorAmount ?></td>
		
		</tr>
		<?php
		//if($r->isPaid==1){}else{
			$points+= $r->vendorAmount;       
		//}
		$i++;
	}
?>
</table>

<h4  class="text-left float-left mt-1">Total Vendor Commission: <?= $points ?></h4>
<?php if($paidCount==$totalOrders){}else{?>
	<button class="btn btn-primary float-right paybtn d-none" data-fromdate="<?=$fromDate?>" data-todate="<?=$toDate?>" data-seq="<?= $vendorCode ?>">Pay Now</button>
<?php } ?>
<?php }  else {
	echo 'No Records Found';
}
?>
 
<script>
$(function(){
	 $('.paybtn').on("click", function() {
		var vendorCode=$(this).attr('data-seq');
		var fromDate=$(this).attr('data-fromdate');
		var toDate=$(this).attr('data-todate');
		swal({
			title: "Are you sure?",
			text: "You want to confirm to pay commission of "+vendorCode,
			type: "warning",
			showCancelButton: !0,
			confirmButtonColor: "#DD6B55",
			confirmButtonText: "Yes, Pay Now!",
			cancelButtonText: "No, cancel it!",
			closeOnConfirm: !1,
			closeOnCancel: !1
			}, function(e) {
				//console.log(e);
				if(e)
				{
					$.ajax({ 
						url: base_path+"Vendorordercommission/save",
						type: 'POST',
						data:{
						  'vendorCode':vendorCode,
						  'fromDate':fromDate,
						  'toDate':toDate
						},
						success: function(data) {
							if(data=='true'){
								swal("Successfull", "Commission Paid Successfully!", "success");
							} else {
								swal("Cancelled", "Some Error Occured! Please try again later..", "error");
							}
							//location.reload();
						}
					});
				} 
				else
				{
					swal("Cancelled", "Your Commission Records are safe.)", "error");
				}
		});
	 });
});
</script>
