	<div class="page-wrapper" style="display: block;">
            
		<div class="email-app">
				
			<!-- ============================================================== -->
			<!-- Left Part -->
			<!-- ============================================================== -->
			
			<div class="left-part">
				<a class="ti-menu ti-close btn btn-success show-left-part d-block d-md-none" href="javascript:void(0)"></a>
				<div class="scrollable ps-container ps-theme-default" style="height:100%;" data-ps-id="b24d36ee-84e6-9c97-e1d4-40383d7e1b6f">
					<div class="p-15"></div>
					
					<div class="divider"></div>
					<ul class="list-group">
						<li>
							<small class="p-15 grey-text text-lighten-1 db">Folders</small>
						</li>
						<li class="list-group-item">
							<a href="javascript:void(0)" data-seq="ALL" class="active list-group-item-action"><i class="mdi mdi-inbox"></i> All </a>
						</li>
						<li class="list-group-item">
							<a href="javascript:void(0)" data-seq="RFQ" class="list-group-item-action"><i class="fas fa-list"></i> &nbsp Request For Quote </a>
						</li>
						<li class="list-group-item">
							<a href="javascript:void(0)" data-seq="PO" class="list-group-item-action"> <i class="fas fa-dolly-flatbed"></i> &nbsp Purchase Order </a>
						</li>
						<li class="list-group-item">
							<a href="javascript:void(0)" data-seq="SO" class="list-group-item-action"> <i class="fas fa-dolly"></i> &nbsp Sales Order </a>
						</li>
						<li class="list-group-item">
							<a href="javascript:void(0)" data-seq="QFC" class="list-group-item-action"> <i class="fas fa-list-ol"></i> &nbsp Quotation For Customer </a>
						</li>
					</ul>
					<div class="ps-scrollbar-x-rail" style="left: 0px; bottom: 0px;">
						<div class="ps-scrollbar-x" tabindex="0" style="left: 0px; width: 0px;"></div>
					</div>
					<div class="ps-scrollbar-y-rail" style="top: 0px; right: 3px;">
						<div class="ps-scrollbar-y" tabindex="0" style="top: 0px; height: 0px;"></div>
					</div>
				</div>
			</div>
			
			<!-- ============================================================== -->
			<!-- Right Part -->
			<!-- ============================================================== -->
			
			<div class="right-part mail-list bg-white" id="tablePart">
				<div class="p-15 b-b">
					<div class="d-flex align-items-center">
						<div>
							<input type="hidden" id="limitNo" value="0">
							<input type="hidden" id="totalRows" value="0">
							<input type="hidden" id="mailCategory" value="ALL">
							<h4>Sentbox </h4>
							<span>Here is the list of mail</span>
						</div>
								
						<div class="m-t-10 ml-auto">
							<label>Show Mail :</label>
						</div>
								
						<div class="col-md-3 ml-auto">
							<select id="selectNo" name="selectNo" class="form-control">
								 <option value="10">10</option>
								 <option value="25">25</option>
								 <option value="50">50</option>
								 <option value="100">100</option>
							</select>
						</div>
								
						<div class="col-md-5 ml-auto">
							<input placeholder="Search Mail" id="txtSearch" type="text" class="form-control">
						</div>
								
					</div>
				</div>
				
				<!-- Action part -->
						
				<div class="bg-light p-15 d-flex align-items-center do-block"></div>
						
				<!-- Action part -->
						
				<!-- Mail list-->
						
				<div class="table-responsive">
					<table id="tableMail" class="table email-table no-wrap table-hover v-middle">
						<tbody id="mailList"></tbody>
					</table>
				</div>
				
				<div class="p-15">
					<label class="control-label" id="startMail"> </label>
					<label class="control-label"> - </label>
					<label class="control-label" id="endMail"> </label>
					<label class="control-label">of </label> <label class="control-label" id="totalMail"></label>
				</div>
				<div class="p-15">
					<button class="btn waves-effect waves-light btn-primary" id="btnPrevious">Previous</button>
					<button class="btn waves-effect waves-light btn-success" id="btnNext">Next</button>
				</div>
			</div>
					
			<div class="right-part mail-details bg-white" style="display: none;" id="rightPart">
				<div class="card-body bg-light">
					<button type="button" id="back_to_inbox" class="btn btn-outline-secondary font-18 m-r-10"><i class="mdi mdi-arrow-left"></i></button>
				</div>
				
				<div id="emailMsg"></div>
				
			</div>
		</div>
	</div>
		
		
		
	<script>
	
		$('document').ready(function() {
			
			var offset=$('#selectNo').val();
			var limitNo=$('#limitNo').val();
			$('#btnPrevious').attr('disabled','disabled');
			
			$('#selectNo').change(function(){
				
				commonData();
				
			});
			
			$.ajax({
				url: base_path+"MailSend/getMailList",
				type: 'POST',
				data:{'mailCategory':'ALL','limitNo':limitNo,'offset':offset},
				success: function(data)
				{
					var obj=JSON.parse(data);
					
					$('#mailList').html(obj['tableHtml']);
					$('#totalRows').val(obj['totalRows']);
					$('#totalMail').html(obj['totalRows']);
					if(obj['totalRows']>offset)
					{
						$('#startMail').html('1');
						$('#endMail').html(offset);
					}
					else
					{
						$('#startMail').html('1');
						$('#endMail').html(obj['totalRows']);
					}
				},
				complete:function()
				{
					
					$(".list-group-item-action").click(function(){
						
						$('#rightPart').hide();
						$('#tablePart').show();
						
						$(this).addClass("active");
						var mailCategorys=$(this).data('seq');
						$("a.active").removeClass("active");
						$('#mailCategory').val(mailCategorys);
						
						commonData();
						
					});
					
					getMsg();
					
				}
			});
			
			 // Search Mail
			 
			 $("#txtSearch").on("keyup", function() {
				var value = $(this).val().toLowerCase();
				$("#tableMail tr").filter(function() {
				  $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
				});
			  });
			  
			  
			  function commonData()
			  {
					$('#limitNo').val(0);
				
					var offset=$('#selectNo').val();
					var mailCategory=$('#mailCategory').val();
					
					$.ajax({	
						url: base_path+"MailSend/getMailList",
						type: 'POST',
						data:{'mailCategory':mailCategory,'limitNo':limitNo,'offset':offset},
						success: function(data)
						{
							var obj=JSON.parse(data);
							
							$('#mailList').html(obj['tableHtml']);
							$('#totalRows').val(obj['totalRows']);
							$('#totalMail').html(obj['totalRows']);
							if(obj['totalRows']>offset)
							{
								$('#startMail').html('1');
								$('#endMail').html(offset);
							}
							else
							{
								$('#startMail').html('1');
								$('#endMail').html(obj['totalRows']);
							}
						},
						complete:function()
						{
							getMsg();
						}
					});
			  }
			  
		});  // End ready
		
		
		$('#btnNext').click(function(){
			
			var offset=$('#selectNo').val();
			var remainingMail=(parseInt($('#totalRows').val())-parseInt($('#limitNo').val()));
			
			if(remainingMail>offset)
			{ 
				$('#btnPrevious').removeAttr('disabled','disabled');
				
				var nextNo=parseInt($('#limitNo').val())+(parseInt(offset));
				var subRemainingMail=parseInt($('#totalRows').val())-parseInt(nextNo);
				$('#startMail').html(parseInt(nextNo)+1);
				
				if(subRemainingMail>(parseInt(offset)-1))
				{
					$('#endMail').html(parseInt(nextNo)+(parseInt(offset)));
				}
				else
				{
					$('#endMail').html(parseInt(nextNo)+parseInt(subRemainingMail));
				}
				
				$('#limitNo').val(nextNo);
				
				commonAjaxAll();
				
			} 	
			
		});
			
		$('#btnPrevious').click(function(){
			
			var offset=$('#selectNo').val();
			
			if($('#limitNo').val()!=0)
			{
				var nextNo=parseInt($('#limitNo').val())-(parseInt(offset));
				if(nextNo!=0)
				{
					$('#startMail').html(parseInt(nextNo)+1);
					$('#endMail').html(parseInt(nextNo)+(parseInt(offset)));
				}
				else
				{
					$('#startMail').html('1');
					$('#endMail').html(offset);
				}
				
				$('#limitNo').val(nextNo);
			
				commonAjaxAll();
			}
			
		});
		
		
		function commonAjaxAll()
		{
			var offset=$('#selectNo').val();
			
			$.ajax({
				url: base_path+"MailSend/getMailList",
				type: 'POST',
				data:{'mailCategory':$('#mailCategory').val(),'limitNo':$('#limitNo').val(),'offset':offset},
				success: function(data)
				{
					var obj=JSON.parse(data);
					$('#mailList').html(obj['tableHtml']);
					$('#totalRows').val(obj['totalRows']);
					$('#totalMail').html(obj['totalRows']);
				},
				complete:function()
				{
					getMsg();
				}
			});
		}
		
		function getMsg()
		{
			$(".link").click(function(){
				var code=$(this).data('seq');
				$.ajax({
					url: base_path+"MailSend/getMsg",
					type: 'POST',
					data:{'code':code},
					success: function(data)
					{
						$('#tablePart').hide();
						$('#rightPart').show();
						$('#emailMsg').html(data);
					}
				});
			});
		} 
		
	</script>