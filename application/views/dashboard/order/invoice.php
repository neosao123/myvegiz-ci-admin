<?php 
$minOrder=$company->result()[0]->minOrder;
echo '<input type="hidden" id="minOrder" name="minOrder"value="'.$minOrder.'">'?>
<div class="page-wrapper">
 <script src="https://kendo.cdn.telerik.com/2018.3.1017/js/jszip.min.js"></script>
    <script src="https://kendo.cdn.telerik.com/2018.3.1017/js/kendo.all.min.js"></script>

    <div class="page-breadcrumb">
        <div class="row">
            <div class="col-4 align-self-center">
                <h4 class="page-title">Retail Invoice/Bill</h4>
                <div class="d-flex align-items-center">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active" aria-current="page">View</li>
                        </ol>
                    </nav>
                </div>
            </div>
			<div class="col-7 align-self-center">
				<div class="d-flex no-block justify-content-end align-items-center">					
					<div class=""><a class="btn btn-primary" id="printButton"  style="color:white" onclick="$('#invoiceContent').printArea()">Print</a></div>
				</div>
            </div>
        </div>
    </div>
    
	
    <div class="container-fluid col-md-12">
        <div class="card">
            <div class="card-body" id="invoiceContent">
			<table width="1000" align="center" border="0">
				<tr>
					<td>
						<img src="<?php echo base_url().'assets/images/header/favicon/favicon-96x96.png';?>" alt="My Vegiz Logo" style="header:40px;width:40px">
					</td>
					<td colspan="6"><h2 class="mt-2"><strong>My Vegiz</strong></h2></td>
				</tr>
				<tr>
					<td colspan="7"><div align="center"><h3><strong>Retail Invoice Bill </strong></h3></div></td>
				</tr>
				 
  <?php foreach($company->result() as $cmp) { ?>
  <tr>
	 <td colspan="5"><strong>Sold By :&nbsp; </strong><?=$cmp->companyName?></td>
     <td colspan="2"><div align="right"><strong>Invoice Date :&nbsp; </strong>
       <?=date('d/m/Y')?></div></td>
     </tr>
  <tr style="border-bottom:1pt solid black;">
    <td colspan="7"><strong>Address : </strong><?= $cmp->shippingAddress.','.$cmp->shippingPlace.','.$cmp->shippingTaluka.','.$cmp->shippingDistrict.','.$cmp->shippingState.','.$cmp->shippingPinCode?> </td>
 </tr>
 
  	<?php	}
	foreach($query->result() as $row) { ?> 
  <tr>
    <td colspan="3"><div align="center"><strong>Order Code</strong></div></td>
    <td colspan="4"><div align="center"><strong>Shipping Address </strong></div></td>
  </tr>
  <tr  style="border-bottom:1pt solid black;">
    <td height="55" colspan="3"><div align="center"><?=$row->code?></div></td>
    <td colspan="4"><div align="center"><?=$clientName.' '.$row->address.' <br/>'.$mobile?></div></td>
	
  </tr>
  
  
  <tr>
  <td width="6">&nbsp;</td>
    <td width="93"><strong>
      <div align="center">Sr.No  </div> </strong></td>
    <td width="269"><strong>
      <div align="center">Product Name </div></strong></td>
    <td width="122"><strong>
      <div align="center">Weight  </div></strong></td>
    <td width="147"><strong>
      <div align="center">Unit Price</div></strong></td>
    <td width="136"><strong>
      <div align="center">Quantity</div></strong></td>
    <td width="173"><strong><div align="center">Sub Total</div></strong></td>
	 <td width="20">&nbsp;</td>
  </tr>
  <?= $lineData?>
  
  <tr style="border-bottom:1pt solid black;">
    <td colspan="7">&nbsp;</td>
  </tr>
 
  <tr>
    <td colspan="6"><div align="right"><strong>Total Price  </strong></div></td>
    <td><div align="center"><label id="totalPrice"><?=$row->totalPrice?></label></div></td>
  </tr>
  <tr>
    <td colspan="6"><div align="right"><strong>Shipping Charges </strong></div></td>
    <td><div align="center"><label  id="shipping"><?=$company->result()[0]->deliveryCharge?></label></div></td>
  </tr>
  <tr>
    <td colspan="6" style="border-bottom:1pt solid black;"><div align="right"><strong>Discount </strong></div></td>
    <td style="border-bottom:1pt solid black;"><div align="center"><label  id="discount"></label></div></td>
  </tr>
   <tr style="border-bottom:1pt solid black;">
    <td colspan="6"><div align="right"><strong>Net Payable </strong></div></td>
    <td ><div align="center" ><label  id="payable"></label></div></td>
  </tr>
  <tr style="border-bottom:1pt solid black;">
    <td colspan="6"><div align="left" ><strong>Amount In Rupees:- </strong><label  id="inwords"  align="center"></label></div></td>
  </tr>
  <?php }?>
  <tr>
    <td colspan="5"><div align="left"> *This is a computer generated invoice.<br />
    </div></td>
    <td colspan="2"><div align="center">CompanyName</div></td>
  </tr>
  <tr>
    <td colspan="7">&nbsp;</td>
  </tr>
  <tr>
    <td colspan="5">&nbsp;</td>
    <td colspan="2"><div align="center">(Authorised Signatory)</div></td>
  </tr>
</table>
		
			</div>
                  
					
              
				</div> <!--cardBody-->
            </div>
        </div>


	
 <script src="https://cdnjs.cloudflare.com/ajax/libs/jQuery.print/1.6.0/jQuery.print.js" integrity="sha256-y1bcuJgVgLha8+mzcg7TB2OHNDC2ZQvaJZmoXM0hxx0=" crossorigin="anonymous"></script>
		
	<script>
	
	
		 $( document ).ready(function() {
			 var payable=0;
			 var totalPrice=$('#totalPrice').text();
			 var shipping=$('#shipping').text();
			 var discount=$('#discount').text();
			 var minOrder=$('#minOrder').val();
			 
			if(totalPrice<minOrder){
				payable=parseFloat(totalPrice)+parseFloat(shipping);
				$('#payable').text(payable);
				$('#inwords').text(convertNumberToWords(payable)+'Rupees');
				$('#discount').text('0');
				//alert(payable);
			}else{
				payable=parseFloat(totalPrice);
				$('#payable').text(payable);
				$('#inwords').text(convertNumberToWords(payable)+'Rupees');
				$('#discount').text(shipping);
			}
			
						 
			
			loadTable();
			   
			   function loadTable()
			   {
				   if ($.fn.DataTable.isDataTable("#datatableOrderDetails")) {
					  $('#datatableOrderDetails').DataTable().clear().destroy();
					}
			var orderCode=$('#orderCode').val();		
	   
	   var dataTable = $('#datatableOrderDetails').DataTable({  
			"processing":true,  
           "serverSide":true,  
           "order":[],
		   "searching": false,
           "ajax":{  
                url: base_path+"Order/getOrderDetails",  
                type:"GET" , 
				data:{'orderCode':orderCode,
				'noPic':1},
           "complete": function(response) {
			$(".blue").click(function(){
			 var code=$(this).data('seq');
			// alert(code)
			 $.ajax({
					url:'<?php echo site_url('Blog/view'); ?>',
					method:"GET",
					data:{code:code},
					datatype:"text",
					success: function(data)
					{
						$(".modal-body").html(data);
						
					}
				});
			});
				//delete
	                             
		  }
		   }
      });
	 }
	     $('#datatableOrderDetails_info').hide(); 
	     $('#datatableOrderDetails_paginate').hide(); 
		 $('#datatableOrderDetails_length').hide(); 
   }); // End Ready
   
   
		// Page Leave Yes / No
			
		var page_isPostBack = false;
		
		function windowOnBeforeUnload()
		{
			if ( page_isPostBack == true )
				return; // Let the page unload

			if ( window.event )
				window.event.returnValue = 'Are you sure?'; 
			else
				return 'Are you sure?'; 
		}
	
		window.onbeforeunload = windowOnBeforeUnload;
		
		// End Page Leave Yes / No
		
		setTimeout(function()
		{
			$('#error_message').hide('fast');
			
		},4000); // End Set Time Function

		
		
		
	
	
	
	
		
    </script>







