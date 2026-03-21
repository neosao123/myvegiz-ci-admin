<div class="page-wrapper"> 
	<div class="page-breadcrumb">
		<div class="row">
			<div class="col-12 align-self-center">
				<h4 class="page-title">Vendor Addon Item</h4>
				<div class="d-flex align-items-center">
					<nav aria-label="breadcrumb">
						<ol class="breadcrumb">
							<li class="breadcrumb-item"><a href="#">Home</a></li>
							<li class="breadcrumb-item active" aria-current="page">Customize Addons</li>
						</ol>
					</nav>
				</div>
			</div>
		</div>
	</div>
		
	<div class="container-fluid col-md-10">
		<div class="card">
			<div class="card-body">
				<h4 class="card-title">Item Details</h4>
				<hr/>				
				<?php if($query) {foreach($query->result() as $row){  ?>
				<div class="form-row">
					<div class="col-md-7 mb-3">
						<label for="itemName">Item Name :</label>
						<input type="text" id="itemName" name="itemName" value="<?=$row->itemName?>" class="form-control-line"  readonly>
						<input type="hidden" id="itemCode" name="itemCode" value="<?=$row->code?>" class="form-control-line"  readonly>
					</div>	 
					<div class="col-md-5 mb-3">
						<label for="salePrice">Sale Price:</label>
						<input type="text" id="salePrice" name="salePrice" value="<?=$row->salePrice?>" class="form-control-line"  readonly>
					</div> 
				</div> 
				<?php } }?>	
			</div>
		</div>
		<div class="card">
			<div class="card-body">
				<h4 class="card-title">Customize Addons</h4>
				<hr/>
				<div class="row">
				    <div class="col-sm-12">
				        <div class="row">
    						<div class="col-sm-5 mb-3">
    							<label for="categoryTitle">Addon Category:</label>
    							<input id="categoryTitle" name="categoryTitle" class="form-control">
    							<input readonly type="hidden" id="customizedCategoryCode" name="customizedCategoryCode">
    							</select>	
    						</div>
    						<div class="col-sm-5  mb-3"> 
    							<label for="categoryType">Addon/Choice:</label>
    							<select id="categoryType" name="categoryType" class="form-control">
    								<option value="">Select Type</option>
    								<option value="choice">Choice</option>
    								<option value="addon">Add On</option>
    							</select>	
    						</div>
    						<div class="col-sm-2  mb-3"> 
    							<label for="categoryType">Enabled:</label>
    							<div class="custom-control custom-checkbox"> 
    								<input type="checkbox" value="1" class="custom-control-input" id="isCateEnabled" name="isCateEnabled">
    								<label class="custom-control-label" for="isCateEnabled">Enabled</label>
    							</div>
    						</div>
    						<div class="col-sm-4  mb-3"> 
    							<button type="button" class="btn btn-info btn-sm" id="addCustomizedCategory"><I class="fa fa-plus"></i> Category</button>
    						</div>
    					</div>
				    </div>    
				    <div class="col-sm-12">   
                        <div class="row">
                            <div class="col-lg-4 col-xl-3">
                                <!-- Nav tabs -->
                                <div class="nav flex-column nav-pills"  id="v-pills-tab" role="tablist" aria-orientation="vertical">
                                    <?php
                                        if($categories)
                                        {
                                            foreach($categories->result_array() as $c)
                                            {
												if($c['categoryType']=='choice'){
													$categoryType = 'Choice';
												}else{
													$categoryType = 'Add On';
												}
                                                echo '<a class="nav-link" id="'.$c['code'].'-tab" data-toggle="pill" href="#'.$c['code'].'" role="tab" aria-controls="'.$c['code'].'" aria-selected="true">'.$c['categoryTitle'].' - '.$categoryType.'  <span class="mr-1 ml-2 btn btn-warning btn-xs deleteCustomizedCategory" id="deltab_'.$c['code'].'"><i class="fa fa-trash"></i></span><span class="mr-1 btn btn-danger btn-xs editCustomizedCategory" id="edttab_'.$c['code'].'"><i class="ti-pencil-alt"></i></span></a>';
                                            }
                                        }
                                    ?>
                                </div>
                            </div>
                            <div class="col-lg-8 col-xl-9">
                                <div class="tab-content" id="v-pills-tabContent"> 
                                    <?php
                                        if($categories)
                                        {
                                            foreach($categories->result_array() as $c)
                                            {
                                                echo '<div class="tab-pane fade" id="'.$c['code'].'" role="tabpanel" aria-labelledby="'.$c['code'].'-tab">';
                                                $html='<div class="row">
                                                    <div class="col-sm-5 mb-3">
                            							<label for="subTitle'.$c['code'].'">Addon Title:</label>
                            							<input id="subTitle'.$c['code'].'" name="subTitle" class="form-control subAddonTitle">  	
                            						</div>
                            						<div class="col-sm-5  mb-3"> 
                            							<label for="price'.$c['code'].'">Price:</label>
                            							<input type="number" id="price'.$c['code'].'" name="price" class="form-control subAddonPrice"> 	
                            						</div>
                            						<div class="col-sm-2  mb-3"> 
                            							<label for="categoryType">Enabled:</label>
                            							<div class="custom-control custom-checkbox"> 
                            								<input type="checkbox" value="1" class="custom-control-input subAddonEnabled" id="isEnabled'.$c['code'].'" name="isEnabled">
                            								<label class="custom-control-label" for="isEnabled'.$c['code'].'">Addon Enabled</label>
                            							</div>
                            						</div>
                            						<div class="col-sm-4  mb-3"> 
                            							<button type="button" class="btn btn-info btn-sm addSubCategory" data-id="'.$c['code'].'"><I class="fa fa-plus"></i> Sub Category</button>
                            						</div>
                                                </div>';
                                                echo $html;
                                                echo '<table style="width:100%" class="table table-bordered" id="tbl'.$c['code'].'"><thead><tr><th>Subtitle</th><th>Price</th><th>Enabled</th><th>#</th></tr></thead><tbody id="tbd'.$c['code'].'">';
                                                if($categoriesline)
                                                {
                                                    foreach($categoriesline->result_array() as $line)
                                                    {
                                                        if($line['customizedCategoryCode']==$c['code'])
                                                        {
                                                            $enabled = $line['isEnabled']==1?'Yes':'No';
                                                            if($enabled=='Yes') $enabled = '<span class="badge badge-success">'.$enabled.'</span>';
                                                            else  $enabled = '<span class="badge badge-danger">'.$enabled.'</span>';
                                                            $option = '<a class="btn btn-sm btn-danger text-white lineDelete" data-id="'.$line['code'].'"><i class="fa fa-trash"></i></a>';
                                                            $row = '<tr id="row_'.$line['code'].'"><td>'.$line['subCategoryTitle'].'</td><td>'.$line['price'].'</td><td>'.$enabled.'</td><td>'.$option.'</td></tr>';
                                                            echo $row;
                                                        }
                                                    }
                                                }
                                                echo '</tbody></table>';
                                                echo '</div>';
                                            }
                                        }
                                    ?>
                                </div>
                            </div>
                        </div>
				    </div>
				</div> 
			</div>
		</div>
	</div>
</div>
<script>
    function clearCategory()
    {
        $("#customizedCategoryCode").val("");
        $("#categoryTitle").val("");
        $("#categoryType").val(""); 
        $("#isCateEnabled").prop('checked',false);
    }
	
	function clearSubCategory()
    {
        $(".subAddonTitle").val("");
        $(".subAddonPrice").val(""); 
        $(".subAddonEnabled").prop('checked',false);
    }
	
	$("body").on('click',".deleteCustomizedCategory",function(e){
		var code = $(this).attr('id');
		code = code.substring(7);
		
		$.ajax({
			url: base_path+'index.php/Food/Vendoritem/deleteAddonCategory',
			data:{'code':code},
			type:'post',
			success:function(response)
			{   
				if(response==true)
				{
					toastr.success("Category deleted successfully", 'Delete Category', { "progressBar": true });  
					var prevAnchor = $('#'+code+'-tab').prev('a').attr('id');
					$("#"+prevAnchor).addClass('active');
					$("#"+prevAnchor).addClass('show');
					$("#"+code+"-tab").remove();
					$("#"+code).remove();
					$("#deltab_"+code).remove();
					
				}
				else
				{
					toastr.error("Failed to delete Category", 'Delete Category', { "progressBar": true });  
				}
			}
		});
	});

	
	$("body").on('click',".lineDelete",function(e){
		var urldelete = base_path+'index.php/Food/Vendoritem/deleteAddonLine'; 
		var code = $(this).data('id');    
		{
			$.ajax({
				url:urldelete,
				data:{'code':code},
				type:'post',
				success:function(response)
				{   
					if(response==true)
					{
						toastr.success("Add-on deleted successfully", 'Delete Add-on', { "progressBar": true });  
						$("#row_"+code).remove();
					}
					else
					{
						toastr.error("Failed to delete Add-on", 'Delete Add-on', { "progressBar": true });  
					}
				}
			});
		}
	});
	
	$("body").on('click',".addSubCategory",function(e){
		var cateCode = $(this).data('id');                
		var subTitle = $("#subTitle"+cateCode).val().trim();
		var price = $("#price"+cateCode).val().trim(); 
		var url = base_path+'index.php/Food/Vendoritem/addAddonLine'; 
		var isCateEnabled  = 0;
		if($("#isEnabled"+cateCode).is(':checked')) isCateEnabled = 1;
		if(subTitle !=''){
			if(price !=''){
				if(subTitle.length>2)
				{
					$.ajax({
						url:url,
						data:{'cateCode':cateCode,'subTitle':subTitle,'price':price,'isCateEnabled':isCateEnabled},
						type:'post',
						success:function(response)
						{  
							var res = JSON.parse(response);
							if(res.status=='true')
							{
								clearSubCategory();
								toastr.success(res.message, 'Add On', { "progressBar": true });  
								var enabled = '<span class="badge badge-danger">No</span>';
								if(isCateEnabled==1) enabled = '<span class="badge badge-success">Yes</span>'; 
								var option = '<a class="btn btn-sm text-white btn-danger lineDelete" data-id="'+res.code+'"><i class="fa fa-trash"></i></a>';
								var row = '<tr id="row_'+res.code+'"><td>'+subTitle+'</td><td>'+price+'</td><td>'+enabled+'</td><td>'+option+'</td></tr>';
								$("#tbd"+cateCode).append(row);  
							}
							else if(res.status=='false')
							{
								toastr.error(res.message, 'Vendor Item', { "progressBar": true });
							}
							else
							{
								toastr.info(res.message, 'Vendor Item', { "progressBar": true });
							}
						} 
					});
				}else{ 
					toastr.error('Valid Subtitle is required.', 'Vendor Item', { "progressBar": true });	
					$("#subTitle"+cateCode).val('');
					$("#subTitle"+cateCode).focus();
				}
			}else{
				toastr.error('Price is required', 'Vendor Item', { "progressBar": true });
				$("#price"+cateCode).focus();
			}
		}else {
		    toastr.error('Subtitle is required.', 'Vendor Item', { "progressBar": true });	
			$("#subTitle"+cateCode).focus();
		}
	});
	
	$("body").on('click',".editCustomizedCategory",function(e){
		var code = $(this).attr('id');
		code = code.substring(7);
		$.ajax({
			url: base_path+'index.php/Food/Vendoritem/getAddonCategoryData',
			data:{'code':code},
			type:'post',
			success:function(response)
			{   
				if(response != "")
				{
					var res = JSON.parse(response);
					// console.log(res.id);
					$("#customizedCategoryCode").val(res.code);
					$("#categoryTitle").val(res.categoryTitle);
					$("#categoryType").val(res.categoryType); 
					if(res.isEnabled == 1){
						$("#isCateEnabled").prop('checked',true);
					}else{
						$("#isCateEnabled").prop('checked',false);
					}
				}
				else
				{
					toastr.error("Something went Wrong.", 'Edit Category', { "progressBar": true });  
				}
			}
		});
	});
	
	$("body").on('click',"#addCustomizedCategory",function(e){ 
		var customizedCategoryCode = $("#customizedCategoryCode").val().trim();                
		var categoryTitle = $("#categoryTitle").val().trim();
		var categoryType = $("#categoryType").val().trim();
		var itemCode = $("#itemCode").val().trim();
		var url = base_path+'index.php/Food/Vendoritem/addAddonCategory';
		if(customizedCategoryCode!="" && customizedCategoryCode!=undefined) url = base_path+'index.php/Food/Vendoritem/updateAddonCategory';
		var isCateEnabled  = 0;
		if($("#isCateEnabled").is(':checked')) isCateEnabled = 1;
		if(categoryType=='choice'){
			var categoryTypeTitle = 'Choice';
		}else{
			var categoryTypeTitle = 'Add On';
		}
		if(categoryTitle.length>3)
		{
			if(categoryType!="" && categoryType!=undefined)
			{
			$.ajax({
				url:url,
				data:{'customizedCategoryCode':customizedCategoryCode,'vendorItemCode':itemCode,'categoryTitle':categoryTitle,'categoryType':categoryType,'isCateEnabled':isCateEnabled},
				type:'post',
				success:function(response) 
				{
					var res = JSON.parse(response);
					if(res.status=='true')
					{
						if(customizedCategoryCode!="" && customizedCategoryCode!=undefined){
							clearCategory();
							var updatedtitle = res.updatedtitle;
							$('#'+customizedCategoryCode+'-tab').html(updatedtitle+' - '+categoryTypeTitle+' <span class="mr-1 btn btn-warning btn-xs deleteCustomizedCategory" id="deltab_'+customizedCategoryCode+'"><i class="fa fa-trash"></i></span><span class="mr-1 btn btn-danger btn-xs editCustomizedCategory" id="edttab_'+customizedCategoryCode+'"><i class="ti-pencil-alt"></i></span>');
							toastr.success(res.message, 'Category', { "progressBar": true });
						}else{
							clearCategory();
							$(".nav-link").removeClass('active');
							$(".tab-pane").removeClass('active');
							
							toastr.success(res.message, 'Addon Category', { "progressBar": true });  
							var html = '<a class="nav-link active" id="'+res.code+'-tab" data-toggle="pill" href="#'+res.code+'" role="tab" aria-controls="'+res.code+'" aria-selected="true">'+categoryTitle+' - '+categoryTypeTitle +'<span class="mr-1 ml-2 btn btn-warning btn-xs deleteCustomizedCategory" id="deltab_'+res.code+'"><i class="fa fa-trash"></i></span><span class="mr-1 btn btn-danger btn-xs editCustomizedCategory" id="edttab_'+res.code+'"><i class="ti-pencil-alt"></i></span></a>';
							var inputSubHtml =  '<div class="row">';
							inputSubHtml += '<div class="col-sm-5 mb-3"><label for="subTitle'+res.code+'">Addon Title:</label><input id="subTitle'+res.code+'" name="subTitle" class="form-control subAddonTitle"></div>';
							inputSubHtml += '<div class="col-sm-5 mb-3"><label for="price'+res.code+'">Price:</label> <input type="number" id="price'+res.code+'" name="price" class="form-control subAddonPrice"></div>';
							inputSubHtml += '<div class="col-sm-2 mb-3"><label for="categoryType">Addon Enabled:</label><div class="custom-control custom-checkbox"><input type="checkbox" value="1" class="custom-control-input subAddonEnabled" id="isEnabled'+res.code+'" name="isEnabled"><label class="custom-control-label" for="isEnabled'+res.code+'">Enabled</label></div></div>';
							inputSubHtml += '<div class="col-sm-4 mb-3"><button type="button" class="btn btn-info btn-sm addSubCategory" data-id="'+res.code+'"><I class="fa fa-plus"></i> Sub Category</button></div>';
							inputSubHtml += '</div>';
							var table='<table style="width:100%" class="table table-bordered" id="tbl'+res.code+'"><thead><tr><th>Subtitle</th><th>Price</th><th>Enabled</th><th>#</th></tr></thead><tbody id="tbd'+res.code+'">';
							var html2 = '<div class="tab-pane fade show active" id="'+res.code+'" role="tabpanel" aria-labelledby="'+res.code+'-tab">'+inputSubHtml+table+'</div>';
							$(".nav-pills").append(html);  
							$("#v-pills-tabContent").append(html2);  
						}
					}
					else if(res.status=='false')
					{
						clearCategory();
						toastr.error(res.message, 'Vendor Item', { "progressBar": true });
					}
					else
					{
						clearCategory();
						toastr.info(res.message, 'Vendor Item', { "progressBar": true });
					}
				} 
			});
		  }else{
			toastr.error('Type is required.', 'Vendor Item', { "progressBar": true });  
		  }
		}
		else{ 
				toastr.error('Valid Category is required.', 'Vendor Item', { "progressBar": true });	
				$("#categoryTitle").val('');
				$("#categoryTitle").focus(); 
		}
	}); 
</script>