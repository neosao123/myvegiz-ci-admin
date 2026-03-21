 
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
					
					<div id="userAccessTable"></div>
                    
                </form>
            </div>
        </div>
		
		<div class="loadermodal"></div>
		
    </div>

	
	<script>
	
		$('document').ready(function(){
			
			var container = $("#load-container");
			
			container.addClass("loading");
			
			$.ajax({
				url:base_path+"Usermaster/getUserAccessList",
				type:'GET',
				success:function(data){
					debugger
					$('#userAccessTable').html(data);
					container.removeClass("loading");
				}
			});
			
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







