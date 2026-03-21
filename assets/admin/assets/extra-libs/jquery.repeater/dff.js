//contract operation rates
var room = 1;
var officeId = 1;
var personId = 1;
var bankId = 1;
var purchaseId = 1;
var addressId = 1;
//contract operation rates end
function education_fields(count,flag) {
    if (flag == 'edit') {
        if (room == 1) {
            alert(count);
            room = count;
        }
    }
    $.ajax({
        url: base_path + "operationRates/getWorkOrderData",
        type: 'GET',
        success: function(data) {
            var operationhtml = "";
            var obj = JSON.parse(data);
            for (i = 0; i < obj.operationmaster.length; i++) {
                operationhtml += '<option value="' + obj.operationmaster[i].code + '">' + obj.operationmaster[i].operationName + '</option>';
            }
            room++;
            var objTo = document.getElementById('education_fields')
            var divtest = document.createElement("div");
            divtest.setAttribute("class", "form-group removeclass" + room);
            var rdiv = 'removeclass' + room;
            var htmlrow = $("#ratesAdd").html();
            if (flag == 'edit') {
                divtest.innerHTML = '<div class="row"> <div class="col-sm-2"> <div class="form-group"> <select  class="form-control" id="operationCodeAdd" name="operationCodeAdd[]" > <option value="">Select Operation</option> ' + operationhtml + ' </select> </div> </div> <div class="col-sm-3"> <div class="form-group"> <input type="text" class="form-control" id="hoursAdd" name="hoursAdd[]" placeholder="Guaranteed Hour for 1000 Tonnes"> </div> </div> <div class="col-sm-3"> <div class="form-group"> <input type="text" class="form-control" id="ratePerHourAdd" name="ratePerHourAdd[]" placeholder="Rate Per Hour(In Rs.)"> </div> </div> <div class="col-sm-3"> <div class="form-group"> <input type="text" class="form-control" id="toatalAmtAdd" name="totalAmtAdd[]" placeholder="Total Amount In(Rs.)"> </div> </div> <div class="col-sm-1"> <div class="form-group"> <button class="btn btn-danger" type="button" onclick="remove_education_fields(' + room + ');"> <i class="fa fa-minus"></i> </button> </div> </div></div>';
            } else {
                divtest.innerHTML = '<div class="row"> <div class="col-sm-2"> <div class="form-group"> <select  class="form-control" id="operationCode" name="operationCode[]" > <option value="">Select Operation</option> ' + operationhtml + ' </select> </div> </div> <div class="col-sm-3"> <div class="form-group"> <input type="text" class="form-control" id="hours" name="hours[]" placeholder="Guaranteed Hour for 1000 Tonnes"> </div> </div> <div class="col-sm-3"> <div class="form-group"> <input type="text" class="form-control" id="ratePerHour" name="ratePerHour[]" placeholder="Rate Per Hour(In Rs.)"> </div> </div> <div class="col-sm-3"> <div class="form-group"> <input type="text" class="form-control" id="toatalAmt" name="totalAmt[]" placeholder="Total Amount In(Rs.)"> </div> </div> <div class="col-sm-1"> <div class="form-group"> <button class="btn btn-danger" type="button" onclick="remove_education_fields(' + room + ');"> <i class="fa fa-minus"></i> </button> </div> </div></div>';
            }
            objTo.appendChild(divtest)
        },
        error: function(xhr, ajaxOptions, thrownError) {
            var errorMsg = 'Ajax request failed: ' + xhr.responseText;
            alert(errorMsg);
            console.log("Ajax Request for patient data failed : " + errorMsg);
        }
    });
}
function remove_education_fields(rid, flag, code) {
    if (flag == 'edit') {
        $.ajax({
            url: base_path + "OperationRates/deleteLineRecord",
            data: {
                'code': code
            },
            type: 'POST',
            success: function(data) {
                if (data == 'true') {
                    $('.removeclass' + rid).remove();
                    new PNotify({
                        title: 'Removed',
                        text: "Operation Rate Successfully Removed",
                        type: 'success',
                        hide: false,
                        styling: 'bootstrap3'
                    });
                } else {
                    new PNotify({
                        title: 'Failed',
                        text: "Failed To Remove Operation Rate",
                        type: 'error',
                        hide: false,
                        styling: 'bootstrap3'
                    });
                }
            },
            error: function(xhr, ajaxOptions, thrownError) {
                var errorMsg = 'Ajax request failed: ' + xhr.responseText;
                alert(errorMsg);
                console.log("Ajax Request for patient data failed : " + errorMsg);
            }
        });
    } else {
        $('.removeclass' + rid).remove();
    }
}
// contract controller end
// contract controller
function contract_fields(count, flag) {
    if (room == 1) {
        room = count;
    }
    $.ajax({
        url: base_path + "Contract/getWorkOrderData",
        // url: "http://sgm-pc/wf-rocktech/index.php/contract/getWorkOrderData",
        type: 'GET',
        success: function(data) {
            var uomhtml = "";
            var obj = JSON.parse(data);
            for (i = 0; i < obj.uommaster.length; i++) {
                uomhtml += '<option value="' + obj.uommaster[i].uomSName + '">' + obj.uommaster[i].uomName + '</option>';
            }
            var itemcategoryhtml = "";
            var obj = JSON.parse(data);
            for (i = 0; i < obj.itemsBasedOnCategory.length; i++) {
                itemcategoryhtml += '<option value="' + obj.itemsBasedOnCategory[i].code + '">' + obj.itemsBasedOnCategory[i].name + '</option>';
            }
            room++;
            var objTo = document.getElementById('contract_fields')
            var divtest = document.createElement("div");
            divtest.setAttribute("class", "form-group removeclass" + room);
            var rdiv = 'removeclass' + room;
            var htmlrow = $("#div1").html();
            if (flag == 'edit') {
                divtest.innerHTML = '<div class="row"> <span><h4>Target :</h4></span> <div class="col-sm-3"> <div class="form-group"> <select  class="form-control" id="itemCodeAdd" name="itemCodeAdd[]"> <option>Select Item Category</option> ' + itemcategoryhtml + '</select> </div> </div> <div class="col-sm-2"> <div class="controls"> <input  id="weightAdd" name="weightAdd[]" placeholder="Enter Weight" class="form-control" > </div> </div> <div class="col-sm-2"> <div class="form-group"> <select  class="form-control" id="uomAdd" name="uomAdd[]" > <option value="">select Uom </option> ' + uomhtml + ' </select> </div> </div> <div class="col-sm-1"> <div class="form-group"> <button class="btn btn-danger" type="button" onclick="remove_contract_fields(' + room + ');"> <i class="fa fa-minus"></i> </button> </div> </div></div></div> ';
            } else {
                divtest.innerHTML = '<div class="row"> <span><h4>Target :</h4></span> <div class="col-sm-3"> <div class="form-group"> <select  class="form-control" id="itemCategoryCode" name="itemCategoryCode[]"> <option>Select Item Category</option> ' + itemcategoryhtml + '</select> </div> </div> <div class="col-sm-2"> <div class="controls"> <input  id="weight" name="weight[]" placeholder="Enter Weight" class="form-control" > </div> </div> <div class="col-sm-2"> <div class="form-group"> <select  class="form-control" id="uom" name="uom[]" > <option value="">select Uom </option> ' + uomhtml + ' </select> </div> </div> <div class="col-sm-1"> <div class="form-group"> <button class="btn btn-danger" type="button" onclick="remove_contract_fields(' + room + ');"> <i class="fa fa-minus"></i> </button> </div> </div></div></div> ';
            }
            objTo.appendChild(divtest)
        },
        error: function(xhr, ajaxOptions, thrownError) {
            var errorMsg = 'Ajax request failed: ' + xhr.responseText;
            alert(errorMsg);
            console.log("Ajax Request for patient data failed : " + errorMsg);
        }
    });
}
function remove_contract_fields(rid, flag, code) {
    if (flag == 'edit') {
        $.ajax({
            url: base_path + "Contract/deleteLineRecord",
            data: {
                'code': code
            },
            type: 'POST',
            success: function(data) {
                if (data == 'true') {
                    $('.removeclass' + rid).remove();
                    new PNotify({
                        title: 'Removed',
                        text: "Work Item Successfully Removed",
                        type: 'success',
                        hide: false,
                        styling: 'bootstrap3'
                    });
                } else {
                    new PNotify({
                        title: 'Failed',
                        text: "Failed To Remove Work Item",
                        type: 'error',
                        hide: false,
                        styling: 'bootstrap3'
                    });
                }
            },
            error: function(xhr, ajaxOptions, thrownError) {
                var errorMsg = 'Ajax request failed: ' + xhr.responseText;
                alert(errorMsg);
                console.log("Ajax Request for patient data failed : " + errorMsg);
            }
        });
    } else {
        $('.removeclass' + rid).remove();
    }
}
// contract controller end
// office contact field 
function office_fields(count,flag) {
   
		if(flag=='edit')
		{
	      if (officeId == 1) {
              officeId = count;
			}
			
		}
    
    var objTo = document.getElementById('office_fields')
    var divtest = document.createElement("div");
    divtest.setAttribute("class", "form-group removeclass" + officeId);
    var rdiv = 'removeclass' + officeId;
	$(".addOffice").remove();
    var htmlrow = $("#divContactOffice").html();
    if (flag == 'edit') 
	{
        divtest.innerHTML = '<span> <h4>Office Contact </h4> </span> 		<div class="form-row" > 			<div class="col-md-6 mb-3"> 				<label for="contactNo">Contact No:<b style="color:red">*</b> </label> 	 				<div class="controls"> 					<input type="tel" id="contactNo'+officeId+'" name="contactNoAdd[]" class="form-control required"  placeholder="Enter text here" > 				</div> 			</div> 			<div class="col-md-6 mb-3"> 				<label for="website">Website:</label> 				<div class="controls"> 					<input type="url" id="website'+officeId+'" name="websiteAdd[]" class="form-control"  placeholder="Enter text here"> 				</div> 			</div> 		</div> 		<div class="form-row"> 			<div class="col-md-6 mb-3"> 				<label for="facebook">Facebook: </label> 				<input type="url" id="facebook'+officeId+'" name="facebookAdd[]" class="form-control"  placeholder="Enter text here">  			</div> 			<div class="col-md-6 mb-3"> 				<label for="googlePlus">Google Plus: </label> 				<div class="controls"> 					<input type="url" id="googlePlus'+officeId+'" name="googlePlusAdd[]" class="form-control"  placeholder="Enter text here"> 				</div> 			</div> 		</div> 		<div class="form-row"> 			<div class="col-md-6 mb-3"> 				<label for="twitter">Twitter: </label> 				<div class="controls"> 					<input type="url" id="twitter'+officeId+'" name="twitterAdd[]" class="form-control"  placeholder="Enter text here" > 				</div> 			</div> 			<div class="col-md-6 mb-3"> 				<label for="linkedIn">Linked In: </label> 				<div class="controls"> 					<input type="url" id="linkedIn'+officeId+'" name="linkedInAdd[]" class="form-control"  placeholder="Enter text here" > 				</div> 			</div> 		</div> 		<div class="form-row"> 			<div class="col-md-6 mb-3"> 				<label for="instagaram">Instagram: </label> 				<input type="url" id="instagram'+officeId+'" name="instagramAdd[]" class="form-control"  placeholder="Enter text here" >  			</div> 			<div class="col-md-6 mb-3"> 				<label for="email">Email:<b style="color:red">*</b></label> 				<div class="controls"> 					<input type="email" id="email'+officeId+'" name="emailAdd[]" class="form-control"  placeholder="Enter text here" > 				</div> 			</div> 		</div> 		<div class="form-row"> 			<div class="col-md-6 mb-3"> 				<label for="purchaseEmail">Purchase Email:<b style="color:red">*</b> </label> 				<input type="email" id="purchaseEmailAdd'+officeId+'" name="purchaseEmailAdd[]" class="form-control"  placeholder="Enter text here" >  			</div> 			<div class="col-md-6 mb-3"> 				<label for="salesEmail">Sales Email: <b style="color:red">*</b></label> 				<div class="controls"> 					<input type="email" id="salesEmailAdd'+officeId+'" name="salesEmailAdd[]" class="form-control"  placeholder="Enter text here" > 				</div> 			</div> 		</div> 		<div class="form-row"> 			<div class="col-sm-2"> 				<button class="btn btn-danger" type="button" onclick="remove_office_fields(' + officeId + ');"> Delete </button> 			</div> 		</div>';
    } else {
        divtest.innerHTML = '<span> <h4>Office Contact </h4> </span> <div class="form-row" > <div class="col-md-6 mb-3"> <label for="contactNo">Contact No:<b style="color:red">*</b> </label>	<div class="controls"> <input type="tel" id="contactNo'+officeId+'" name="contactNo[]" class="form-control required"  placeholder="Enter text here" > </div> </div> <div class="col-md-6 mb-3"> <label for="website">Website: </label> <div class="controls"> <input type="url" id="website'+officeId+'" name="website[]" class="form-control"  placeholder="Enter text here"> </div> </div> </div> <div class="form-row"> <div class="col-md-6 mb-3"> <label for="facebook">Facebook: </label> <input type="url" id="facebook'+officeId+'" name="facebook[]" class="form-control"  placeholder="Enter text here"> </div> <div class="col-md-6 mb-3"> <label for="googlePlus">Google Plus: </label> <div class="controls"> <input type="url" id="googlePlus'+officeId+'" name="googlePlus[]" class="form-control"  placeholder="Enter text here"> </div> </div> </div> <div class="form-row"> <div class="col-md-6 mb-3"> <label for="twitter">Twitter: </label> <div class="controls"> <input type="url" id="twitter'+officeId+'" name="twitter[]" class="form-control"  placeholder="Enter text here" > </div>  </div> <div class="col-md-6 mb-3"> <label for="linkedIn">Linked In: </label> <div class="controls"> <input type="url" id="linkedIn'+officeId+'" name="linkedIn[]" class="form-control"  placeholder="Enter text here" > </div> </div> </div> <div class="form-row"> <div class="col-md-6 mb-3"> <label for="instagaram">Instagram: </label> <input type="url" id="instagram'+officeId+'" name="instagram[]" class="form-control"  placeholder="Enter text here" >  </div> <div class="col-md-6 mb-3"> <label for="email">Email:<b style="color:red">*</b></label> <div class="controls"> <input type="email" id="email'+officeId+'" name="email[]" class="form-control required"  placeholder="Enter text here" > </div> </div></div>  <div class="form-row"> <div class="col-md-6 mb-3"> <label for="purchaseEmail">Purchase Email:<b style="color:red">*</b> </label> <input type="email" id="purchaseEmail'+officeId+'" name="purchaseEmail[]" class="form-control required"  placeholder="Enter text here" >  </div> <div class="col-md-6 mb-3"> <label for="salesEmail">Sales Email: <b style="color:red">*</b></label> <div class="controls"> <input type="email" id="salesEmail'+officeId+'" name="salesEmail[]" class="form-control required"  placeholder="Enter text here" > </div> </div> </div><div class="form-row"> <div class="col-sm-2"><button class="btn btn-danger" type="button" onclick="remove_office_fields(' + officeId + ');"> Delete  </button></div></div>';
    }
     objTo.appendChild(divtest);
	 
	 var addButton = '<div><button class="btn btn-success" type="button" onclick="office_fields('+count+',\'edit\');"><i class="fa fa-plus"></i></button></div>';
	 $("table tbody").append(addButton);
    officeId++;
	
}
function remove_office_fields(rid, flag, code) {
    if (flag == 'edit') {
        $.ajax({
            url: base_path + "office/deleteLineRecordOfficeContact",
            data: {
                'code': code
            },
            type: 'POST',
            success: function(data) {
                if (data == 'true') {
                    $('.removeclass' + rid).remove();
                    new PNotify({
                        title: 'Removed',
                        text: "Work Office Contact Successfully Removed",
                        type: 'success',
                        hide: false,
                        styling: 'bootstrap3'
                    });
                } else {
                    new PNotify({
                        title: 'Failed',
                        text: "Failed To Remove Office Contact",
                        type: 'error',
                        hide: false,
                        styling: 'bootstrap3'
                    });
                }
            },
            error: function(xhr, ajaxOptions, thrownError) {
                var errorMsg = 'Ajax request failed: ' + xhr.responseText;
                alert(errorMsg);
                console.log("Ajax Request for patient data failed : " + errorMsg);
            }
        });
    } else {
        $('.removeclass' + rid).remove();
    }
} //end office contact fields
//start person fields
function person_fields(count, flag) {
    if(flag=='edit')   
	   if (personId == 1) {
			personId = count;
		}
      
    
    var objTo = document.getElementById('person_fields')
    var divtest = document.createElement("div");
    divtest.setAttribute("class", "form-group removeclass" + personId);
    var rdiv = 'removeclass' + personId;
    var htmlrow = $("#divPersonAdd").html();
     // $(".addOffice").remove();
    if (flag == 'edit') {
        divtest.innerHTML = ' <span><h4>Contact Person </h4></span> <div class="form-row"> <div class="col-md-6 mb-3"> <label for="personName">Person Name: </label> <input type="text" id="personName'+personId+'" name="personNameAdd[]" class="form-control"  placeholder="Enter text here">  </div> <div class="col-md-6 mb-3"> <label for="personDesignation">Person Designation: </label>  <input type="text" id="personDesignation'+personId+'" name="personDesignationAdd[]" class="form-control"  placeholder="Enter text here" >  </div> </div> <div class="form-row"> <div class="col-md-6 mb-3"> <label for="personDepartment">Person Department: </label> <input type="text" id="personDepartment'+personId+'" name="personDepartmentAdd[]" class="form-control"  placeholder="Enter text here" >  </div> <div class="col-md-6 mb-3"> <label for="personContact">Person Contact: </label> <div class="controls"> <input type="tel" id="personContact'+personId+'" name="personContactAdd[]" class="form-control" placeholder="Enter text here" > </div> </div> </div> <div class="form-row"> <div class="col-md-6 mb-3"> <label for="personContact2">Person Contact 2: </label> <div class="controls"> <input type="tel" id="personContact2'+personId+'" name="personContact2Add[]" class="form-control" placeholder="Enter text here" > </div>  </div> <div class="col-md-6 mb-3"> <label for="personEmail"> Person Email: </label> <div class="controls"> <input type="email" id="personEmail'+personId+'" name="personEmailAdd[]" class="form-control" placeholder="Enter text here" > </div> </div> </div> <div class="form-row"> <div class="col-md-6 mb-3"> <label for="personPhoto">Person Photo: </label> <input type="file" id="personPhoto'+personId+'" name="personPhotoAdd[]" class="form-control" >  </div> </div></div> <div class="form-row"><div class="col-sm-2"> <button class="btn btn-danger" type="button" onclick="remove_person_fields(' + personId + ');"> Delete</button> </div></div>';
    } else {
        divtest.innerHTML = ' <span><h4>Contact Person </h4></span> <div class="form-row"> <div class="col-md-6 mb-3"> <label for="personName">Person Name: </label> <input type="text" id="personName'+personId+'" name="personName[]" class="form-control"  placeholder="Enter text here">  </div> <div class="col-md-6 mb-3"> <label for="personDesignation">Person Designation: </label>  <input type="text" id="personDesignation'+personId+'" name="personDesignation[]" class="form-control"  placeholder="Enter text here" >  </div> </div> <div class="form-row"> <div class="col-md-6 mb-3"> <label for="personDepartment">Person Department: </label> <input type="text" id="personDepartment'+personId+'" name="personDepartment[]" class="form-control"  placeholder="Enter text here" >  </div> <div class="col-md-6 mb-3"> <label for="personContact">Person Contact: </label> <div class="controls"> <input type="tel" id="personContact'+personId+'" name="personContact[]" class="form-control" placeholder="Enter text here" > </div> </div> </div> <div class="form-row"> <div class="col-md-6 mb-3"> <label for="personContact2">Person Contact 2: </label> <div class="controls"> <input type="tel" id="personContact2'+personId+'" name="personContact2[]" class="form-control" placeholder="Enter text here" > </div>  </div> <div class="col-md-6 mb-3"> <label for="personEmail"> Person Email: </label> <div class="controls"> <input type="email" id="personEmail'+personId+'" name="personEmail[]" class="form-control" placeholder="Enter text here" > </div> </div> </div> <div class="form-row"> <div class="col-md-6 mb-3"> <label for="personPhoto">Person Photo: </label> <input type="file" id="personPhoto'+personId+'" name="personPhoto[]" class="form-control" >  </div> </div></div> <div class="form-row"><div class="col-sm-2"> <button class="btn btn-danger" type="button" onclick="remove_person_fields(' + personId + ');"> Delete</button> </div></div>';
    }
    objTo.appendChild(divtest);
	personId++;
	 //var addButton = '<div><button class="btn btn-success" type="button" onclick="office_fields('+count+',\'edit\');"><i class="fa fa-plus"></i></button></div>';
	 //$("table tbody").append(addButton);
	
}    
function remove_person_fields(rid, flag, code) {
    if (flag == 'edit') {
        $.ajax({
            url: base_path + "office/deleteLineRecordOfficePersonContact",
            data: {
                'code': code
            },
            type: 'POST',
            success: function(data) {
                if (data == 'true') {
                    $('.removeclass' + rid).remove();
                    new PNotify({
                        title: 'Removed',
                        text: " Office Contact Person Successfully Removed",
                        type: 'success',
                        hide: false,
                        styling: 'bootstrap3'
                    });
                } else {
                    new PNotify({
                        title: 'Failed',
                        text: "Failed To Remove Office Person Contact",
                        type: 'error',
                        hide: false,
                        styling: 'bootstrap3'
                    });
                }
            },
            error: function(xhr, ajaxOptions, thrownError) {
                var errorMsg = 'Ajax request failed: ' + xhr.responseText;
                alert(errorMsg);
                console.log("Ajax Request for patient data failed : " + errorMsg);
            }
        });
    } else {
        $('.removeclass' + rid).remove();
    }
}
//end person fields
//start address fields
function address_fields(count, flag) {
    if (flag == "edit") {
        if (addressId == 1) {
            addressId = count;
        }
    }
    var objTo = document.getElementById('address_fields')
    var divtest = document.createElement("div");
    divtest.setAttribute("class", "form-group removeclass" + addressId);
    var rdiv = 'removeclass' + addressId;
    var htmlrow = $("#divAddressAdd").html();
    if (flag == 'edit') {
        divtest.innerHTML = '<span><h4>Address </h4></span> <div class="form-row"> <div class="col-md-12 mb-3"> <label for="address">Address: </label> <input type="text" id="address' + addressId + '" name="addressAdd[]" class="form-control" placeholder="Enter text here" >  </div> </div> <div class="form-row"> <div class="col-md-4 mb-3"> <label for="pinCode">Pin Code: </label> <div class="controls"> <input type="number" id="pinCode' + addressId + '" name="pinCodeAdd[]" class="form-control" list="pinCodeList' + addressId + '"><datalist id="pinCodeList' + addressId + '"></datalist> </div> </div> <div class="col-md-4 mb-3"> <label for="place">Place: </label> <input type="text" id="place' + addressId + '" name="placeAdd[]" class="form-control"  placeholder="Enter text here">  </div> <div class="col-md-4 mb-3"> <label for="taluka">Taluka: </label> <input type="text" id="taluka' + addressId + '" name="talukaAdd[]" class="form-control" placeholder="Enter text here" list="talukaList' + addressId +'"><datalist id="talukaList' + addressId + '"></datalist> </div> </div> <div class="form-row"> <div class="col-md-4 mb-3"> <label for="district">District: </label> <div class="controls"> <input type="text" id="district' + addressId + '" name="districtAdd[]" class="form-control"  placeholder="Enter text here" list="districtList' + addressId +'"><datalist id="districtList' + addressId + '"></datalist> </div> </div> <div class="col-md-4 mb-3"> <label for="state">State: </label> <div class="controls"> <input type="text" id="state' + addressId + '" name="stateAdd[]" class="form-control" placeholder="Enter text here" list="stateList' + addressId +'"><datalist id="stateList' + addressId + '"></datalist></div>  </div> <div class="col-md-4 mb-3"> <label for="country"> country: </label> <div class="controls"> <input type="text" id="country' + addressId + '" name="countryAdd[]" class="form-control" placeholder="Enter text here" > </div> </div> </div>  <div class="form-row"><div class="col-sm-2"> <button class="btn btn-danger" type="button" onclick="remove_address_fields(' + addressId + ');"> Delete </button></div></div>';
    } else {
        divtest.innerHTML = '<span><h4>Address </h4></span> <div class="form-row"> <div class="col-md-12 mb-3"> <label for="address">Address: </label> <input type="text" id="address' + addressId + '" name="address[]" class="form-control" placeholder="Enter text here" >  </div> </div> <div class="form-row"> <div class="col-md-4 mb-3"> <label for="pinCode">Pin Code: </label> <div class="controls"> <input type="number" id="pinCode' + addressId + '" name="pinCode[]" class="form-control pinCodes" list="pinCodeList' + addressId + '" srno="' + addressId + '"><datalist id="pinCodeList' + addressId + '"></datalist> </div> </div> <div class="col-md-4 mb-3"> <label for="place">Place: </label> <input type="text" id="place' + addressId + '" name="place[]" class="form-control"  placeholder="Enter text here">  </div> <div class="col-md-4 mb-3"> <label for="taluka">Taluka: </label> <input type="text" id="taluka' + addressId + '" name="taluka[]" class="form-control" placeholder="Enter text here" list="talukaList' + addressId +'" ><datalist id="talukaList' + addressId + '"></datalist>  </div> </div> <div class="form-row"> <div class="col-md-4 mb-3"> <label for="district">District: </label> <div class="controls"> <input type="text" id="district' + addressId + '" name="district[]" class="form-control"  placeholder="Enter text here" list="districtList' + addressId +'"><datalist id="districtList' + addressId + '"></datalist></div> </div> <div class="col-md-4 mb-3"> <label for="state">State: </label> <div class="controls"> <input type="text" id="state' + addressId + '" name="state[]" class="form-control" placeholder="Enter text here" list="stateList' + addressId +'" ><datalist id="stateList' + addressId + '"></datalist></div>  </div> <div class="col-md-4 mb-3"> <label for="country"> country: </label> <div class="controls"> <input type="text" id="country' + addressId + '" name="country[]" class="form-control" placeholder="Enter text here" > </div> </div> </div>  <div class="form-row"><div class="col-sm-2"> <button class="btn btn-danger" type="button" onclick="remove_address_fields(' + addressId + ');"> Delete </button></div></div>';
    }
    var id = addressId++;
    objTo.appendChild(divtest)
      
	//for dynamic
	
	 //For Taluka data
	
			$("#taluka"+id).keyup(function() {
        if ($(this).val().length > 4)
        {
            var taluka_name = $(this).val();
            $.ajax({
                url: base_path + 'addressInfo/getAllDataTaluka',
                method: "GET",
                data: {
                    taluka:taluka_name
                },
                datatype: "text",
                success: function(data) {
                    $("#talukaList"+id).html(data);
                }
            });
        }
    });
	
	//For Distric data
			$("#district"+id).keyup(function() {
        if ($(this).val().length > 4)
        {
            var district_name = $(this).val();
            $.ajax({
                url: base_path + 'addressInfo/getAllDataDistrict',
                method: "GET",
                data: {
                    district:district_name
                },
                datatype: "text",
                success: function(data) {
                    $("#districtList"+id).html(data);
                }
            });
        }
    });
	
	 //For State data
			$("#state"+id).keyup(function() {
        if ($(this).val().length > 4)
        {
            var state_name = $(this).val();
            $.ajax({
                url: base_path + 'addressInfo/getAllDataState',
                method: "GET",
                data: {
                   state:state_name
                },
                datatype: "text",
                success: function(data) {
                    $("#stateList"+id).html(data);
                }
            });
        }
    });
    $("#pinCode"+id).keyup(function() {
        if ($(this).val().length > 3)
        {
            var pin_code = $(this).val();
            $.ajax({
                url: base_path + 'addressInfo/getAllData',
                method: "GET",
                data: {
                    pinCode: pin_code
                },
                datatype: "text",
                success: function(data) {
                    $("#pinCodeList" + id).html(data);
                }
            });
        }
    });
    //From Pin Code get Address data from DB
    $("#pinCode" + id).change(function() {
        var pin_code = $(this).val();
        // alert(pin_code);
        $.ajax({
            url: base_path + 'addressInfo/getAddressFromPin',
            method: "GET",
            data: {
                pinCode: pin_code
            },
            datatype: "text",
            success: function(data) {
                var res = $.parseJSON(data);
                $("#place" + id).val(res.place);
                $("#taluka" + id).val(res.taluka);
                $("#district" + id).val(res.district);
                $("#state" + id).val(res.state);
                $("#country" + id).val(res.country);
            }
        });
    });
}
function remove_address_fields(rid, flag, code) {
    if (flag == 'edit') {
        $.ajax({
            url: base_path + "office/deleteLineRecordOfficeAddress",
            data: {
                'code': code
            },
            type: 'POST',
            success: function(data) {
                // alert(data);
                if (data == 'true') {
                    $('.removeclass' + rid).remove();
                    new PNotify({
                        title: 'Removed',
                        text: "Office Address Successfully Removed",
                        type: 'success',
                        hide: false,
                        styling: 'bootstrap3'
                    });
                } else {
                    new PNotify({
                        title: 'Failed',
                        text: "Failed To Remove Office Address",
                        type: 'error',
                        hide: false,
                        styling: 'bootstrap3'
                    });
                }
            },
            error: function(xhr, ajaxOptions, thrownError) {
                var errorMsg = 'Ajax request failed: ' + xhr.responseText;
                alert(errorMsg);
                console.log("Ajax Request for patient data failed : " + errorMsg);
            }
        });
    } else {
        $('.removeclass' + rid).remove();
    }
}
//end address fields
//start address fields
function bank_fields(count, flag) {
 if(flag=='edit')
 {
    if (bankId == 1) {
        bankId = count;
    }
 }
    var objTo = document.getElementById('bank_fields')
    var divtest = document.createElement("div");
    divtest.setAttribute("class", "form-group removeclass" + room);
    var rdiv = 'removeclass' + room;
    var htmlrow = $("#divBankAdd").html();
    if (flag == 'edit') {
        divtest.innerHTML = '<span ><h4>Bank Document </h4></span> <div class="form-row"> <div class="col-md-6 mb-3"> <label for="bankAccountHolderNameAdd">Bank Name: </label> <div class="controls"> <input type="text" id="bankName' + (bankId) + '" name="bankNameAdd[]" class="form-control" placeholder="Enter text here" > </div> </div> <div class="col-md-6 mb-3"> <label for="bankBranchNameAdd">Bank Branch Name: </label> <div class="controls"> <input type="text" id="bankBranchName' + (bankId) + '" name="bankBranchNameAdd[]" class="form-control" placeholder="Enter text here" > </div>  </div> </div> <div class="form-row"> <div class="col-md-6 mb-3"> <label for="bankAccountNumberAdd">Bank Account Number: </label> <input type="text" id="bankAccountNumber' + (bankId) + '" name="bankAccountNumberAdd[]" class="form-control" placeholder="Enter text here" >  </div> <div class="col-md-6 mb-3"> <label for="bankAcofficeIdHolderName">Bank Account Holder Name: </label> <div class="controls"> <input type="text" id="bankAccountHolderName' + (bankId) + '" name="bankAccountHolderNameAdd[]" class="form-control" placeholder="Enter text here" > </div> </div> </div> <div class="form-row"> <div class="col-md-6 mb-3"> <label for="bankIfscCodeAdd">Bank IFSC Code: </label> <div class="controls"> <input type="text" id="bankIfscCode' + (bankId) + '" name="bankIfscCodeAdd[]" class="form-control" placeholder="Enter text here" > </div> </div> <div class="col-md-6 mb-3"> <label for="bankMICRCodeAdd"> Bank MICR Code: </label> <div class="controls"> <input type="text" id="bankMicrCode' + (bankId) + '" name="bankMicrCodeAdd[]" class="form-control" placeholder="Enter text here" > </div> </div> </div> <div class="form-row"> <div class="col-md-6 mb-3"> <label for="bankPassbookFileAdd">Bank Passbook File: </label> <input type="file" id="bankPassbookFile' + (bankId) + '" name="bankPassbookFileAdd[]" class="form-control" >  </div> </div> <div class="form-row"><div class="col-sm-2"> <button class="btn btn-danger" type="button" onclick="remove_bank_fields(' + bankId + ');"> Delete </button></div></div>';
    } else {
        divtest.innerHTML = '<span ><h4>Bank Document </h4></span> <div class="form-row"> <div class="col-md-6 mb-3"> <label for="bankAcofficeIdHolderName">Bank Name: </label> <div class="controls"> <input type="text" id="bankName' + (bankId) + '" name="bankName[]" class="form-control" placeholder="Enter text here" > </div> </div> <div class="col-md-6 mb-3"> <label for="bankBranchName">Bank Branch Name: </label> <div class="controls"> <input type="text" id="bankBranchName' + (bankId) + '" name="bankBranchName[]" class="form-control" placeholder="Enter text here" > </div>  </div> </div> <div class="form-row"> <div class="col-md-6 mb-3"> <label for="bankAccountNumber">Bank Account Number: </label> <input type="text" id="bankAccountNumber' + (bankId) + '" name="bankAccountNumber[]" class="form-control" placeholder="Enter text here" >  </div> <div class="col-md-6 mb-3"> <label for="bankAcofficeIdHolderName">Bank Account Holder Name: </label> <div class="controls"> <input type="text" id="bankAccountHolderName' + (bankId) + '" name="bankAccountHolderName[]" class="form-control" placeholder="Enter text here" > </div> </div> </div> <div class="form-row"> <div class="col-md-6 mb-3"> <label for="bankIfscCode">Bank IFSC Code: </label> <div class="controls"> <input type="text" id="bankIfscCode' + (bankId) + '" name="bankIfscCode[]" class="form-control" placeholder="Enter text here" > </div> </div> <div class="col-md-6 mb-3"> <label for="bankMICRCode"> Bank MICR Code: </label> <div class="controls"> <input type="text" id="bankMicrCode' + (bankId) + '" name="bankMicrCode[]" class="form-control" placeholder="Enter text here" > </div> </div> </div> <div class="form-row"> <div class="col-md-6 mb-3"> <label for="bankPassbookFile">Bank Passbook File: </label> <input type="file" id="bankPassbookFile' + (bankId) + '" name="bankPassbookFile[]" class="form-control" >  </div> </div> <div class="form-row"><div class="col-sm-2"> <button class="btn btn-danger" type="button" onclick="remove_bank_fields(' + bankId + ');"> Delete </button></div></div>';
    }
    objTo.appendChild(divtest);
	bankId++;
}
function remove_bank_fields(rid, flag, code) {
    if (flag == 'edit') {
        $.ajax({
            url: base_path + "office/deleteLineRecordOfficeBank",
            data: {
                'code': code
            },
            type: 'POST',
            success: function(data) {
                //alert(data);
                if (data == 'true') {
                    $('.removeclass' + rid).remove();
                    new PNotify({
                        title: 'Removed',
                        text: "Office Bank Contact Successfully Removed",
                        type: 'success',
                        hide: false,
                        styling: 'bootstrap3'
                    });
                } else {
                    new PNotify({
                        title: 'Failed',
                        text: "Failed To Remove Office Bank Contact",
                        type: 'error',
                        hide: false,
                        styling: 'bootstrap3'
                    });
                }
            },
            error: function(xhr, ajaxOptions, thrownError) {
                var errorMsg = 'Ajax request failed: ' + xhr.responseText;
                alert(errorMsg);
                console.log("Ajax Request for patient data failed : " + errorMsg);
            }
        });
    } else {
        $('.removeclass' + rid).remove();
    }
}
function purchase_fields(count, flag) {
    if (room == 1) {
        room = count;
    }
    $.ajax({
        url: base_path + "PurchaseRequisition/getWorkOrderData",
        type: 'GET',
        success: function(data) {
            var purchasehtml = "";
            var obj = JSON.parse(data);
            for (i = 0; i < obj.itemmaster.length; i++) {
                purchasehtml += '<option value="' + obj.itemmaster[i].code + '">' + obj.itemmaster[i].vendor + '</option>';
            }
            room++;
            var objTo = document.getElementById('purchase_fields')
            var divtest = document.createElement("tr");
            divtest.setAttribute("class", "form-group removeclass" + room);
            var rdiv = 'removeclass' + room;
            var htmlrow = $("#ratesAdd").html();
            $(".add").remove();
            if (flag == 'edit') {
                divtest.innerHTML = '<td><input type="text"  class="form-control" list="itemCodeList" id="itemCode' + (purchaseId) + '" name="itemCode"><datalist id="itemCodeList' + (purchaseId) + '"></datalist></td><td><input type="text" id="itemName' + (purchaseId) + '" name="itemName" list="itemNameList" class="form-control" ><datalist id="itemNameList' + (purchaseId) + '"></datalist></td><td><input type="text"class="form-control" id="itemQuantity' + (purchaseId) + '" name="itemQuantity" ></td><td> <input type="text" class="form-control" id="itemUom' + (purchaseId) + '" name="itemUom"></td><td><input type="text"class="form-control date-inputmask" id="deliveryDate' + (purchaseId) + '" placeholder="dd/mm/yyyy"name="deliveryDate"></td><td><select type="text" class="form-control" id="siteCode' + (purchaseId) + '" name="siteCode"><option value="" selected>Select site</option></option></select></td><td> <select type="text" class="form-control" id="departmentName' + (purchaseId) + '" name="departmentName"><option value="" selected>Select dept</option></select></td><td><input type="text"class="form-control" id="storageName' + (purchaseId) + '" name="storageName"></td><td><input type="text" class="form-control" id="storageSection' + (purchaseId) + '" name="storageSection"></td><td><select type="text"class="form-control"  id="vendordrop' + (purchaseId) + '" name="vendorName"><option value="" selected>Select vender</option> ' + purchasehtml + '</select></td><td><input type="number" class="form-control" id="itemPrice' + (purchaseId) + '" name="itemPrice"></td><td><button class="btn btn-danger" type="button" onclick="remove_purchase_fields(' + room + ');"><i class="fa fa-minus"></i></button></td>';
            } else {
                divtest.innerHTML = '<td><input type="text"  class="form-control" list="itemCodeList" id="itemCode' + (purchaseId) + '" name="itemCode"><datalist id="itemCodeList' + (purchaseId) + '"></datalist></td><td><input type="text" id="itemName' + (purchaseId) + '" name="itemName" list="itemNameList" class="form-control" ><datalist id="itemNameList' + (purchaseId) + '"></datalist></td><td><input type="text"class="form-control" id="itemQuantity' + (purchaseId) + '" name="itemQuantity" ></td><td> <input type="text" class="form-control" id="itemUom' + (purchaseId) + '" name="itemUom"></td><td><input type="text"class="form-control date-inputmask" id="deliveryDate' + (purchaseId) + '" placeholder="dd/mm/yyyy"name="deliveryDate"></td><td><select type="text" class="form-control" id="siteCode' + (purchaseId) + '" name="siteCode"><option value="" selected>Select site</option></option></select></td><td> <select type="text" class="form-control" id="departmentName' + (purchaseId) + '" name="departmentName"><option value="" selected>Select dept</option></select></td><td><input type="text"class="form-control" id="storageName' + (purchaseId) + '" name="storageName"></td><td><input type="text" class="form-control" id="storageSection' + (purchaseId) + '" name="storageSection"></td><td><select type="text"class="form-control"  id="vendordrop' + (purchaseId) + '" name="vendorName"><option value="" selected>Select vender</option> ' + purchasehtml + '</select></td><td><input type="number" class="form-control" id="itemPrice' + (purchaseId) + '" name="itemPrice"></td><td><button class="btn btn-danger" type="button" onclick="remove_purchase_fields(' + room + ');"><i class="fa fa-minus"></i></button></td>';
            }
            objTo.appendChild(divtest)
            var addButton = '<tr><td><button class="btn btn-success add" type="button" onclick="purchase_fields(' + count + ',"edit")"><i class="fa fa-plus"></i></button></td></tr>';
            $("table tbody").append(addButton);
            purchaseId++;
            console.log(purchaseId);
        },
        error: function(xhr, ajaxOptions, thrownError) {
            var errorMsg = 'Ajax request failed: ' + xhr.responseText;
            alert(errorMsg);
            console.log("Ajax Request for patient data failed : " + errorMsg);
        }
    });
}
function remove_purchase_fields(rid, flag, code) {
    if (flag == 'edit') {
        $.ajax({
            url: base_path + "PurchaseRequisition/deleteLineRecord",
            data: {
                'code': code
            },
            type: 'POST',
            success: function(data) {
                if (data == 'true') {
                    $('.removeclass' + rid).remove();
                    new PNotify({
                        title: 'Purchase Requisition',
                        text: "Record Data Successfully Removed",
                        type: 'success',
                        hide: false,
                        styling: 'bootstrap3'
                    });
                } else {
                    new PNotify({
                        title: 'Purchase Requisition',
                        text: "Failed To Remove Record Data ",
                        type: 'error',
                        hide: false,
                        styling: 'bootstrap3'
                    });
                }
            },
            error: function(xhr, ajaxOptions, thrownError) {
                var errorMsg = 'Ajax request failed: ' + xhr.responseText;
                alert(errorMsg);
                console.log("Ajax Request for patient data failed : " + errorMsg);
            }
        });
    } else {
        $('.removeclass' + rid).remove();
    }
}
//end purchase_fields