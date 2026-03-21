<div 
<div class="page-wrapper">
 
    <div class="page-breadcrumb">
        <div class="row">
            <div class="col-12 align-self-center">
                <h4 class="page-title">User Access</h4>
                <div class="d-flex align-items-center">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active" aria-current="page">User Access</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
    
	
    <div id="load-container" class="container-fluid col-md-12">
    
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">User Access</h4>
                <hr/>
				
                <form class="needs-validation"  novalidate>
					<label><b>User Code : <?= $this->uri->segment(3);?></b></label>
					<input type="hidden" id="userCode" name="userCode" value="<?= $this->uri->segment(3);?>">
					<div id="userAccessTable"></div>
					
					<hr/>
					
					 <div class="text-xs-right">
                        <button type="button" id="btnSubmit" class="btn btn-success" onclick="page_isPostBack=true;">Submit</button>
                        <button type="reset" class="btn btn-reset">Back</button>
					</div>
					
                </form>
            </div>
        </div>
		
		<div class="loadermodal"></div>
		
    </div>

	
	<script>
	
		$('document').ready(function(){
			
			var ModulesData=[];
			var ModulesData1=[];
			var ModulesDataRes={};
			var subModules=[];
			var subModulesRes={};
			var obj;
			var moduleCode,subModuleCode='';
			var status=true;
			var id,code,moduleName,moduleIcon,displayUrl,routeUrl,sequence,type,subStatus='';
			var subModuleName,subModuleIcon='';
							
			var userCode=$('#userCode').val();
			
			var container = $("#load-container");
			
			container.addClass("loading");
			$.ajax({
				url:base_path+"Usermaster/getUserAccessList",
				type:'GET',
				success:function(data){
					$('#userAccessTable').html(data);
					container.removeClass("loading");
					checkAll();
				},
				complete:function(){
				   $.ajax({
						url:base_path+"Usermaster/getUserAccessEditList",
						type:'GET',
						data:{'userCode':userCode},
						success:function(json){
                            if(json!=false){
								var obj=JSON.parse(json);
								//console.log(userRights.userAction);
								//obj=$.parseJSON(userRights.userRights); 
								for(var i=0;i<obj['ModulesData'].length;i++){
									moduleCode=obj['ModulesData'][i]['code'];
									$('#chkModule'+moduleCode).prop('checked','checked');
									if(obj['ModulesData'][i]['subStatus']==true){
										for(var j=0;j<obj['ModulesData'][i]['subModules'].length;j++){
											subModuleCode=obj['ModulesData'][i]['subModules'][j]['code'];
											$('#chkSubModule'+subModuleCode).prop('checked','checked'); 
										}
									}
								} // End for loop
						    }
					    }, 
						complete:function(){
							makeStructure();
						} 
					}); 
				} // End. Complete 1st
				
			});// End Ajax 1st
			
			
		function makeStructure()
		{
			$.ajax({
				url:base_path+"Usermaster/getAllPrivileges",
				type:'POST',
				success:function(json){ 
					//alert(json);
				},
				complete:function(data){
					debugger;
					$('#btnSubmit').click(function(){
						debugger;
						container.addClass("loading");
						//console.log(data.responseText);
						var privelegeData=JSON.parse(data.responseText);
						var ModulesData=privelegeData.ModulesData;
						var finalResult={};
						var resultArray=[];
						finalResult['status']=true;
							for(var i=0;i<ModulesData.length;i++)
						{
							var modulearr={};
							if($('#chkModule'+ModulesData[i].code).is(":checked")){
								modulearr['id']=ModulesData[i].id;
								modulearr['code']=ModulesData[i].code;
								modulearr['moduleName']=ModulesData[i].moduleName;
								modulearr['moduleIcon']=ModulesData[i].moduleIcon;
								modulearr['displayUrl']=ModulesData[i].displayUrl;
								modulearr['routeUrl']=ModulesData[i].routeUrl;
								modulearr['sequence']=ModulesData[i].sequence;
								modulearr['type']=ModulesData[i].type;
								modulearr['subStatus']=ModulesData[i].subStatus;
								
								if(ModulesData[i].subStatus)
								{
									var res2=[];
									
									var subModulesData=ModulesData[i].subModules;
									
									for(var j=0;j<subModulesData.length;j++)
									{
										
										var submodulearr={};
										if($('#chkSubModule'+subModulesData[j].code).is(":checked")){
											submodulearr['id']=subModulesData[j].id;
											submodulearr['code']=subModulesData[j].code;
											submodulearr['subModuleName']=subModulesData[j].subModuleName;
											submodulearr['subModuleIcon']=subModulesData[j].subModuleIcon;
											submodulearr['displayUrl']=subModulesData[j].displayUrl;
											submodulearr['routeUrl']=subModulesData[j].routeUrl;
											submodulearr['sequence']=subModulesData[j].sequence;
											res2.push(submodulearr);
											
										}
									}
									
									modulearr['subModules']=res2;
									
								}
								resultArray.push(modulearr);
								
							}
							
						}
						finalResult['ModulesData']=resultArray;
						debugger;
						storeRights(JSON.stringify(finalResult));
					});
					
				}
			});
		}
		
		function storeRights(userRights,userAction){
			debugger;
			var userCode=$("#userCode").val();
			$.ajax({
				url:base_path+"Usermaster/saveRights",
				type:'POST',
				data:{
					'userCode':userCode,
				    'userrights':userRights
				},
				success:function(json){
					debugger;
					console.log(json);
					var obj=JSON.parse(json);
					container.removeClass("loading");
					if(obj.status){
						 swal({
							title: "Success",
							text: obj.message,
							type: "success"
					    });
					
					}else{
						 swal({
						    title: "Success",
							text: obj.message,
							type: "success"
						});
					}
				}
			});
		}
			
			
		function checkAll()
		{
			$('#checkAll_module').change(function() {
				if(this.checked) {
					$('.mainmodules').prop('checked',true);
				}
				else
				{
					$('.mainmodules').prop('checked',false);
				}
			});
			
			$('#checkAll_submodule').change(function() {
				if(this.checked) {
					$('.submodules').prop('checked',true);
					$('.mainmodules').prop('checked',true);
				}
				else
				{
					$('.submodules').prop('checked',false);
					$('.mainmodules').prop('checked',false);
				}
			});
			
			$('#checkAll_submoduleaction').change(function() {
				if(this.checked) {
					$('.submodules').prop('checked',true);
					$('.mainmodules').prop('checked',true);
					$('.submodulesaction').prop('checked',true);
				}
				else
				{
					$('.submodules').prop('checked',false);
					$('.mainmodules').prop('checked',false);
					$('.submodulesaction').prop('checked',false);
				}
			});
		}
			
		}); // End Ready
		
	</script>
	
	
	
	<script>
	
	  // Example starter JavaScript for disabling form submissions if there are invalid fields
		(function() {
			'use strict';
			window.addEventListener('load', function() {
				// Fetch all the forms we want to apply custom Bootstrap validation styles to
				var forms = document.getElementsByClassName('needs-validation');
				// Loop over them and prevent submission
				var validation = Array.prototype.filter.call(forms, function(form) {
					form.addEventListener('submit', function(event) {
						if (form.checkValidity() === false) {
							event.preventDefault();
							event.stopPropagation();
						}
						form.classList.add('was-validated');
					}, false);
				});
			}, false);
		})();
		
    </script>







