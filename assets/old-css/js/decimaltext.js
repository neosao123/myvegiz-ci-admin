function validateFloatKeyPress(el, evt, b_dec, a_dec) {
			//alert(el.value);
			var charCode = (evt.which) ? evt.which : event.keyCode;
			//alert(charCode);
			if (a_dec >= 0) {
				if (charCode != 46 && charCode > 31 && (charCode < 48 || charCode > 57)) {
					return false;
				}
			} else {
				if (charCode < 48 || charCode > 57) {
					return false;
				}
			}
			var number = el.value.split('.');
			//just one dot
			if (number.length > 1 && charCode == 46) {
				//alert('Already pressed');
				return false;
			}
			//get the carat position
			var caratPos = getSelectionStart(el);
			var dotPos = el.value.indexOf(".");
			//alert(dotPos);
			//No dot still limit upto 2 digits but allow . after 2 digits
			if (caratPos > dotPos && dotPos == -1 && (number[0].length > b_dec) && charCode != 46) {
				return false;
			}
			//allow 3 digits after .
			if (caratPos > dotPos && dotPos > -1 && (number[1].length > a_dec)) {
				return false;
			}
			//Do not exceed length of 2, if digits before . altered 
			if (caratPos < dotPos + 1 && dotPos > -1 && (number[0].length > b_dec)) {
				return false;
			}
			return true;
		}
		function getSelectionStart(o) {
			if (o.createTextRange) {
				var r = document.selection.createRange().duplicate()
				alert(r);
				r.moveEnd('character', o.value.length)
				if (r.text == '')
					return o.value.length
				return o.value.lastIndexOf(r.text)
			} else {
				//alert(o.selectionStart); current position
				return o.selectionStart
			}
		}