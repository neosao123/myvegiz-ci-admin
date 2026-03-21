<?php 
$i=1;
$points = 0;
if($commissionData){
	
	//print_r($commissionData->result());
	?>
<table class="table table-sm table-bordered table-condensed table-responsive">
	<tr>
		<th>Sr.No.</th>
		<th>Payment Status</th>
		<th>Order Code</th>
		<th>Commission</th>
		<th>Order Price</th>
		<th>Return Price</th>
		<th>Date</th>
	</tr>
	</thead>
<?php 
$paidCount=0;
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
		<td><?= $r->commissionAmount ?></td>
		<td><?= $r->totalPrice ?></td>
		<td><?= ($r->totalPrice-$r->commissionAmount) ?></td>
		<td><?= date('d/m/Y h:i A',strtotime($r->addDate)) ?></td>
		</tr>
		<?php
		$points+= $r->commissionAmount; 
		$i++;
	}
?>
</table>

<h4  class="text-left float-left mt-1">Total Commission: <?= $points ?></h4>
<?php }  else {
	echo 'No Records Found';
}
?>
 
<script>
$(function(){
	 $('.paybtn').on("click", function() {
		var code=$(this).attr('data-seq');
		var dateSearch=$(this).attr('data-dateSearch');
		swal({
			title: "Are you sure?",
			text: "You want to confirm to pay commission of "+code,
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
						url: base_path+"Commission/save",
						type: 'POST',
						data:{
						  'code':code,'dateSearch':dateSearch
						},
						success: function(data) {
							if(data=='true'){
								swal("Successfull", "Commission Paid Successfully!", "success");
							} else {
								swal("Cancelled", "Some Error Occured! Please try again later..", "error");
							}
							location.reload();
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
