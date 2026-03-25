<?php foreach($query->result() as $row)
{
    if($row->isActive == "1")
    {
        $activeStatus='<span class="label label-sm label-success">Active</span>';
    }
    else 
    {
        $activeStatus='<span class="label label-sm label-warning">Inactive</span>';
    }
?>

<div class="form-row">
    <div class="col-md-3 mb-3">
        <label><b> Code:</b> </label>
        <input type="text" value="<?php echo $row->code?>" class="form-control-line" id="clientCode" readonly>
    </div>
    <div class="col-md-9 mb-3">
        <label><b> Client Name:</b> </label>
        <input type="text" value="<?php echo $row->name?>" class="form-control-line rpadding"  readonly>
    </div>	
    <div class="col-md-6 mb-3">
        <label><b> Mobile Number:</b></label>
        <input type="text" value="<?php echo $row->mobile?>" class="form-control-line rpadding"  readonly>
    </div>
    <div class="col-md-6 mb-3">
        <label><b>Email ID:</b> </label>
        <input type="text" value="<?php echo $row->emailId?>" class="form-control-line rpadding"  readonly>
    </div>
</div>

<div class="form-row">
                        
<?php 						
foreach($queryprofile->result() as $profileRow)

    {
    ?>

<div class="col-md-6 mb-3"><label><b> Local:</b> </label>
    <input type="text" value="<?php echo $profileRow->local?>" class="form-control-line rpadding"  readonly>
</div>
<div class="col-md-6 mb-3">
    <label><b> Flat :</b> </label>
    <input type="text" value="<?php echo $profileRow->flat?>" class="form-control-line rpadding"  readonly>
</div>
<div class="col-md-12 mb-3"><label><b> Landmark :</b> </label>
    <input type="text" value="<?php echo $profileRow->landMark?>" class="form-control-line rpadding"  readonly>
</div>
<div class="col-md-4 mb-3"><label> <b>City :</b> </label>
    <input type="text" class="form-control-line rpadding" value="<?php echo $cityname ?>"  readonly>
</div> 
<div class="col-md-4 mb-3"><label> <b>Pincode  :</b> </label>
    <input type="text" class="form-control-line rpadding" value="<?php echo $profileRow->pincode?>"  readonly>				
</div> 
<div class="col-md-4 mb-3"><label><b> State: </b></label>
    <input type="text" class="form-control-line rpadding" value="<?php echo $profileRow->state?>"  readonly>
</div>     
<div class="col-md-12 mb-3">
    <label><b> Status: </b></label>
    <div class="form-group"><?php echo $activeStatus?></div>
</div>						
</div>     
    <?php } } ?>
