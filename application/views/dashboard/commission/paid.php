<?php
foreach($userdata->result() as $r){
	echo '<h4>'.$r->firstName.' '.$r->lastName.'</h4>';
}
if($commissionData){
if($commissionData->num_rows()>0){
?>
<table class="table table-bordered">
	<thead>
		<tr>
			<th>Sr. No.</th>
			<th>Points</th>
			<th>Month</th>
			<th>Year</th>
			<th>Date</th>
		</tr>
	</thead>
	<tbody>
		<?php
		$srno=1;
		
			foreach($commissionData->result() as $rw){
				?>
				<tr>
					<td><?= $srno ?></td>
					<td><?= $rw->points ?></td>
					<td><?= date('M',strtotime($rw->month)) ?></td>
					<td><?= $rw->year ?></td>
					<td><?= date('d/m/Y h:i A', strtotime($rw->entryDate)) ?></td>
				</tr>
				<?php
				$srno++;
			}	
		?>
	</tbody>
</table>
<?php } }else {
	echo 'No records found...';
}
	
?>