		<footer class="footer text-center" style="margin-left:16rem;">
			All Rights Reserved by Myvegiz. Designed and Developed by <a href="https://neosao.com/">Neosao Services Private Limited</a>.
		</footer>
		</div>
		<div id="permission_div"></div>  
		<!-- ============================================================== -->
		<!-- End Page wrapper  -->
		<!-- ============================================================== -->
		</div>
		<!-- ============================================================== -->
		<!-- End Main Wrapper -->
		<!-- ============================================================== -->
		<!-- ============================================================== -->
		<!-- customizer Panel -->
		<!-- ============================================================== -->
		<!-- ============================================================== -->
		<!-- All Jquery -->
		<!-- ============================================================== -->
		<script type="text/javascript" src="<?php echo base_url() . 'assets/admin/assets/extra-libs/maxlength-master/jquery.plugin.js'; ?>"></script>
		<script type="text/javascript" src="<?php echo base_url() . 'assets/admin/assets/extra-libs/maxlength-master/jquery.maxlength.js'; ?>"></script>
		<!-- Bootstrap tether Core JavaScript -->
		<script src="<?php echo base_url() . 'assets/admin/assets/libs/popper.js/dist/umd/popper.min.js'; ?>"></script>
		<script src="<?php echo base_url() . 'assets/admin/assets/libs/bootstrap/dist/js/bootstrap.min.js'; ?>"></script>
		<!-- apps -->
		<script src="<?php echo base_url() . 'assets/admin/dist/js/app.min.js'; ?>"></script>
		<script src="<?php echo base_url() . 'assets/admin/dist/js/app.init.js'; ?>"></script>
		<!-- slimscrollbar scrollbar JavaScript -->
		<script src="<?php echo base_url() . 'assets/admin/assets/libs/perfect-scrollbar/dist/perfect-scrollbar.jquery.min.js'; ?>"></script>
		<script src="<?php echo base_url() . 'assets/admin/assets/extra-libs/sparkline/sparkline.js'; ?>"></script>
		<!--Wave Effects -->
		<script src="<?php echo base_url() . 'assets/admin/dist/js/waves.js'; ?>"></script>
		<!--Menu sidebar -->
		<script src="<?php echo base_url() . 'assets/admin/dist/js/sidebarmenu.js'; ?>"></script>
		<!--Custom JavaScript -->
		<script src="<?php echo base_url() . 'assets/admin/dist/js/custom.min.js'; ?>"></script>
		<script src="<?php echo base_url() . 'assets/admin/assets/extra-libs/jqbootstrapvalidation/validation.js'; ?>"></script>
		<!--Tables page plugins -->
		<script src="<?= base_url() . 'assets/admin/assets/libs/datatables.net-bs4/css/jquery.dataTables.min.js' ?>" type="text/javascript"></script>
		<script src="<?= base_url() . 'assets/admin/assets/libs/datatables.net-bs4/css/dataTables.buttons.min.js' ?>" type="text/javascript"></script>
		<script src="<?= base_url() . 'assets/admin/assets/libs/datatables.net-bs4/css/jszip.min.js' ?>" type="text/javascript"></script>
		<script src="<?= base_url() . 'assets/admin/assets/libs/datatables.net-bs4/css/pdfmake.min.js' ?>" type="text/javascript"></script>
		<script src="<?= base_url() . 'assets/admin/assets/libs/datatables.net-bs4/css/vfs_fonts.js' ?>" type="text/javascript"></script>
		<script src="<?= base_url() . 'assets/admin/assets/libs/datatables.net-bs4/css/buttons.print.min.js' ?>" type="text/javascript"></script>
		<script src="<?= base_url() . 'assets/admin/assets/libs/datatables.net-bs4/css/buttons.html5.min.js' ?>" type="text/javascript"></script>
		<script src="<?= base_url() . 'assets/admin/assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.js' ?>" type="text/javascript"></script>
		<script src="<?= base_url() . 'assets/admin/assets/libs/datatables.net-bs4/css/dataTables.responsive.min.js' ?>" type="text/javascript"></script>
		<!-- Datepicker JS -->
		<script src="<?php echo base_url() . 'assets/admin/assets/libs/sweetalert2/dist/sweet-alert.min.js'; ?>"></script>
		<script src="<?php echo base_url() . 'assets/admin/assets/libs/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js'; ?>"></script>
		<script src="<?php echo base_url() . 'assets/admin/assets/libs/summernote/dist/summernote-bs4.min.js'; ?>"></script>
		<!-- toastr alert -->
		<script src="<?php echo base_url() . 'assets/admin/assets/libs/toastr/build/toastr.min.js'; ?>"></script>
		<script src="https://www.gstatic.com/firebasejs/8.2.0/firebase-app.js"></script>
		<script src="https://www.gstatic.com/firebasejs/8.2.0/firebase-messaging.js"></script>
		<script>
			! function(window, document, $) {
				"use strict";
				$("input,select,textarea").not("[type=submit]").jqBootstrapValidation();
			}(window, document, jQuery); 
			
			$(function() {
                $("body").click(function(){
                    document.getElementById('noti').muted = false;
                    audio.play();
                    playnotify();
                })
				$('[data-toggle="tooltip"]').tooltip();
			});  
			var base_path = "<?= base_url() ?>";
			var fireToken = "";
			var notifyGranted = false;
			var firebaseConfig = {
				apiKey: "AIzaSyCfGyCTfYKVkPY9Md9KIPkDszKWzFY08eg",
				authDomain: "myvegiz-82e06.firebaseapp.com",
				databaseURL: "https://myvegiz-82e06.firebaseio.com",
				projectId: "myvegiz-82e06",
				storageBucket: "myvegiz-82e06.appspot.com",
				messagingSenderId: "923183195433",
				appId: "1:923183195433:web:552677860bf99989498338",
				measurementId: "G-BRB8P9M67X"
			};
			// [START get_messaging_object]
			// Retrieve Firebase Messaging object.
			firebase.initializeApp(firebaseConfig);
			const messaging = firebase.messaging();
			// [END get_messaging_object] 

			// IDs of divs that display registration token UI or request permission UI.
			const tokenDivId = 'token_div';
			const permissionDivId = 'permission_div';

			function resetUI() {
				// [START get_token]
				// Get registration token. Initially this makes a network call, once retrieved
				// subsequent calls to getToken will return from cache.
				messaging.getToken().then((currentToken) => {
					if (currentToken) {
						saveFireBaseId_fo_sess(currentToken);
						sendTokenToServer(currentToken);
						updateUIForPushEnabled(currentToken);
						fireToken = currentToken;

					} else {
						// Show permission request.
						console.log('No registration token available. Request permission to generate one.');
						// Show permission UI.
						updateUIForPushPermissionRequired();
						setTokenSentToServer(false);
					}
				}).catch((err) => {
					console.log('An error occurred while retrieving token. ', err);
					setTokenSentToServer(false);
					showHideDiv(permissionDivId, true)
				});
				// [END get_token]
			}

			function sendTokenToServer(currentToken) {
				if (!isTokenSentToServer()) {
					console.log('Sending token to server...');
					// TODO(developer): Send the current token to your server.
					setTokenSentToServer(true);
				} else {
					console.log('Token already sent to server so won\'t send it again ' +
						'unless it changes');
				}

			}

			function isTokenSentToServer() {
				return window.localStorage.getItem('sentToServer') === '1';
			}

			function setTokenSentToServer(sent) {
				window.localStorage.setItem('sentToServer', sent ? '1' : '0');
			}

			function showHideDiv(divId, show) {}

			function requestPermission() {
				console.log('Requesting permission...');
				// [START request_permission]
				messaging.requestPermission(function() {
					if (messaging.permission === 'granted') {
						showHideDiv(permissionDivId, false);
						getRegToken();
					} else if (messaging.permission === 'denied') {
						showHideDiv(permissionDivId, true);
					} else {
						showHideDiv(permissionDivId, true);
						console.log('Unable to get permission to notify.');
					}
				});
				Notification.requestPermission().then((permission) => {
					if (messaging.permission === 'granted') {
						showHideDiv(permissionDivId, false);
						getRegToken();
						resetUI();
					} else if (messaging.permission === 'denied') {
						showHideDiv(permissionDivId, true);
					} else {
						showHideDiv(permissionDivId, true);
						console.log('Unable to get permission to notify.');
					}
				});
			} 
			function updateUIForPushEnabled(currentToken) {
				showHideDiv(tokenDivId, true);
				showHideDiv(permissionDivId, false); 
			} 
			function updateUIForPushPermissionRequired() {
				showHideDiv(tokenDivId, false);
				showHideDiv(permissionDivId, true);
			}

			resetUI();

			function getRegToken() {
				// Get Instance ID token. Initially this makes a network call, once retrieved 
				messaging.getToken()
					.then((currentToken) => {
						if (currentToken) {
							debugger;
							fireToken = currentToken;
							saveFireBaseId_fo_sess(fireToken); 
						} else {
							// Show permission request.
							showHideDiv(permissionDivId, true);
							setTokenSentToServer(true);
						}
					}).catch((err) => {
						//console.log('An error occurred while retrieving token. ', err);
						setTokenSentToServer(true);
					});
			}

			// show messagesho
			messaging.onMessage(function(payload) {
				console.log('Message received. ', payload);  
				$("#button").trigger("click");
				var title = payload.notification.title;
				var msg = payload.notification.body;
				toastr.success(msg, title, {
					"progressBar": true,
					"positionClass": "toast-top-center",
					"closeButton": true,
				});
				 
			}); 
			//save session token 
			function saveFireBaseId_fo_sess(currentToken) {
				$.ajax({
					url: base_path + "Authentication/updateFirebaseToken",
					method: 'post',
					data: { 'fireToken': currentToken },
					success: function(data) {
						if(data=='false'){
							console.log("Failed to update firebase token");
						} else {
							console.log("Firebase token Updated");
						} 
					}
				});
			}
		 
            const button = document.querySelector("#button");
            const icon = document.querySelector("#button > i");
            const audio = document.querySelector("audio"); 
            button.addEventListener("click", () => {
              playnotify();
              console.log("trigged");
            }); 
            function playnotify()
            { 
                //audio.muted = false;
                //if (audio.paused) {\
                //debugger;
                if (audio.muted) {
                    audio.volume = 0.2; 
                    //icon.classList.remove('fa-volume-up');
                    //icon.classList.add('fa-volume-mute');
                    button.classList.remove("text-primary");  
                    button.classList.add("text-danger");  
                    //audio.play();
                    audio.muted=false;
                } else { 
                    button.classList.remove("text-danger");  
                    button.classList.add("text-primary"); 
                    //icon.classList.remove('fa-volume-mute');
                    //icon.classList.add('fa-volume-up');
                    //audio.pause(); 
                    audio.muted=true;
                } 
            }
		</script>
		<!--For Validation Messages ends-->
  </body> 
</html>