<!DOCTYPE html>
<html>
<?php
	$title = "Customer Selection";
	require_once("project-header.php");
?>
<body onload='populateCustomerData()'>
	<div class='container-fluid'>
		<div class='page-header'>
			<h3>Select a customer</h3>
		</div>
		<div id='userMessages'></div>
		<div class='col-xs-12'>
			<div class='search-customer'>
				<div class="input-group col-xs-4">
					<span class="input-group-addon">Search Customer</span>
					<input id='customerName' type="text" class="form-control" placeholder='Enter Customer Name' onkeyup='populateCustomerData()'>
				</div>
			</div>
			<div class='add-customer-btn'>
				<button type='button' class='btn btn-primary col-xs-2' id='btnAddCustomer' data-toggle="modal" data-target="#addEditCustomerModal">Add Customer</button>
			</div>
		</div>
		<br/><br/><br/>
		<div class='custom-table'>
			<table id='customer-table' class="table table-hover">
				<thead>
					<tr>
						<th class='col-xs-3'>Name</th>
						<th class='col-xs-2'>VAT / CST</th>
						<th class='col-xs-2'>City</th>
						<th class='col-xs-2'>Pincode</th>
						<th class='col-xs-1'>Edit</th>
						<th class='col-xs-1'>Delete</th>
						<th class='col-xs-1'>Select</th>
					</tr>
				</thead>
				<tbody>
					<!--Data will be populated as per populateCustomerData() function-->
					<tr>
						<td scope='row' colspan='7'>No Customers Found. Please try again.</td>
						<td>
							<form method='post' action='dataEnter.php'>
								<input type='hidden' id='customerId' name='customerId' value='56'/>
								<input type='submit' name='selectCustomer' value='Submit' class='btn btn-primary col-xs-10'/>
							</form>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
		<div class='custom-modal'>
			<div class="modal fade" id="addEditCustomerModal" tabindex="-1" role="dialog" aria-labelledby="addEditCustomerModalLabel" aria-hidden="true">
				<div class="modal-dialog" role="document">
					<div class="modal-content">
					
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							 <span aria-hidden="true">&times;</span>
							</button>
							<h4 class="modal-title" id="addEditCustomerModalLabel">Add a Customer</h4>
						</div>
						
						<div class="modal-body">
							<div class='row'>
								<div class='custom-form col-xs-12'>
									<form id='customer-form' method='post' onSubmit='return false;'>
										<div class='form-group'>
											<label for='custName'>Name</label>
											<input type='text' class='form-control' id='custName' name='custName' placeholder='Customer Name' required='true'>
										</div>
										<div class='form-group'>
											<label for='vat'>VAT / CST</label>
											<input type='text' class='form-control' id='vat' name='vat' placeholder='Customer VAT / CST' required='true'>
										</div>
										<div class='form-group'>
											<label for='addr1'>Address Line 1</label>
											<input type='text' class='form-control' id='addr1' name='addr1' placeholder='Address Line 1'>
										</div>
										<div class='form-group'>
											<label for='addr2'>Address Line 2</label>
											<input type='text' class='form-control' id='addr2' name='addr2' placeholder='Address Line 2'>
										</div>
										<div class='form-group'>
											<label for='city'>City</label>
											<input type='text' class='form-control' id='city' name='city' placeholder='City'>
										</div>
										<div class='form-group'>
											<label for='pincode'>Pin Code</label>
											<input type='text' class='form-control' id='pincode' name='pincode' placeholder='Pin Code'>
										</div>
										<div class='form-group'>
											<label for='pan'>PAN Number</label>
											<input type='text' class='form-control' id='pan' name='pan' placeholder='ABCDE1234Z'>
										</div>
										<div class='form-group'>
											<input type='submit' class='btn btn-primary col-xs-3 custom-btn' id='addCustomer' name='addCustomer' value='Save Customer' onclick="saveCustomer()">
											<input type='reset' class='btn btn-primary col-xs-3 custom-btn' id='addCustomer' name='addCustomer' value='Clear Form'>
											<input type='hidden' id='hfCustomerId' value=''>
										</div>
									</form>
								</div>
							</div>
						</div>
						
					</div>
				</div>
			</div>
		</div>
	</div>
</body>
<?php
	require_once("project-footer.php");
?>
<script>
	function populateCustomerData(){
		
		//create new http request
		var xhttp = new XMLHttpRequest();
		
		//Actual function to populate the customer table
		xhttp.onreadystatechange = function() {
			if (this.readyState == 4 && this.status == 200) {
				
				var res = this.responseText;
				
				if(res != 'null'){
					//customer(s) found with provided name
					
					//convert response to json object array
					var customerJson = JSON.parse(res);
				
					//re-setting the variable
					var customerList="";
					
					//Adding the customer to the search data div
					for(var i=0; i<customerJson.length; i++){
						
						//Adding a table row for the customer
						customerList += "<tr>";
						customerList += "<td scope='row'>"+customerJson[i].name+"</td>";
						customerList += "<td>"+customerJson[i].vat+"</td>";
						//customerList += "<td>"+customerJson[i].cst+"</td>";
						customerList += "<td>"+customerJson[i].city+"</td>";
						customerList += "<td>"+customerJson[i].pincode+"</td>";
						customerList += "<td>";
							customerList += "<button type='button' class='btn btn-warning col-xs-10' onClick='loadCustomer("+customerJson[i].cid+")'>Edit</button>";
						customerList += "</td>";
						customerList += "<td>";
							customerList += "<button type='button' class='btn btn-danger col-xs-10' onClick='deleteCustomer("+customerJson[i].cid+")'>Delete</button>";
						customerList += "</td>";
						customerList += "<td>";
							customerList += "<form method='post' action='bill-details.php'>";
								customerList += "<input type='hidden' id='hfCid' name='hfCid' value='"+customerJson[i].cid+"'/>";
								customerList += "<input type='submit' name='selectCustomer' value='Select' class='btn btn-primary col-xs-10'/>";
							customerList += "</form>";
						customerList += "</td>";
						customerList += "</tr>";
					}
				}
				else{
					//no customers found
					customerList = "<tr><td scope='row' colspan='8'>No Customers Found. Please try again.</td></tr>";
				}
				var table = document.getElementById('customer-table');
				var tableBody = table.getElementsByTagName('tbody')[0];
				tableBody.innerHTML = customerList;
			}
		};
		
		var customerName = document.getElementById("customerName").value;
		
		xhttp.open("GET", "http://localhost/printer/customer?name=" + customerName, true);
		
		xhttp.setRequestHeader("Authorization", "UserTokenWillGoInHere");
		
		xhttp.send();
	}
	
	function loadCustomer(cid){
		//setting the hidden field value to the cid
		document.getElementById('hfCustomerId').value = cid;
		
		//create new http request
		var xhttp = new XMLHttpRequest();
		
		//Actual function to populate the modal with customer details
		xhttp.onreadystatechange = function() {
			if (this.readyState == 4 && this.status == 200) {
				
				var res = this.responseText;
				
				if(res != 'null'){
					//customer found with provided cid
					
					//convert response to json object array
					var customerJson = JSON.parse(res);
					
					//fill the modal with customer details
					document.getElementById("vat").value = customerJson.vat;
					//document.getElementById("cst").value = customerJson.cst;
					document.getElementById("custName").value = customerJson.name;
					document.getElementById("addr1").value = customerJson.addressline1;
					document.getElementById("addr2").value = customerJson.addressline2;
					document.getElementById("city").value = customerJson.city;
					document.getElementById("pincode").value = customerJson.pincode;
					document.getElementById("pan").value = customerJson.pan;
					
					//trigger the modal
					$('#addEditCustomerModal').modal();
					
					//setting the modal title to 'Edit Customer'
					document.getElementById('addEditCustomerModalLabel').innerText = "Edit Customer details";
				}
				else{
					//no customer found
					displayTemporaryMessage("Failure","Customer could not be loaded. Please try again.");
				}
			}
		};
		
		//get request with cid
		xhttp.open("GET", "http://localhost/printer/customer?cid=" + cid, true);
		
		//adding auth header
		xhttp.setRequestHeader("Authorization", "UserTokenWillGoInHere");
		
		//sending the request
		xhttp.send();
	}
	
	function deleteCustomer(cid){
		if(confirm('Are you sure you want to delete this customer?'))
		{
			//create new http request
			var xhttp = new XMLHttpRequest();
			
			//Actual function to post a new customer
			xhttp.onreadystatechange = function() {
				if (this.readyState == 4 && this.status == 200) {
					
					var res = this.responseText;
					
					if(res != 'null'){
						//convert response to json object array
						var responseJson = JSON.parse(res);
						
						if(responseJson.success.toUpperCase()=="y".toUpperCase())
						{
							displayTemporaryMessage("Success","Customer deleted Successfully.");
							
							//refresh the table
							populateCustomerData();
						}
						else{
							displayTemporaryMessage("Failure","Something went wrong. Please contact admin.");
						}
					}
					else{
						displayTemporaryMessage("Failure","Something went wrong. Please try again.");
					}
				}
			};
			
			//delete request with cid={cid}
			xhttp.open("DELETE", "http://localhost/printer/customer", true);
			
			//Adding request headers
			xhttp.setRequestHeader("Authorization", "UserTokenWillGoInHere");
			xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
			
			//sending the request
			xhttp.send("cid="+cid);
		}
	}
	
	function saveCustomer(){
		
		//If Hidden field (hfCustomerId) is empty, then POST, Else PUT
		var isAdd;
		var cidVal = document.getElementById('hfCustomerId').value;
		if(cidVal=="")
			isAdd = true;
		else
			isAdd = false;
		
		//create new http request
		var xhttp = new XMLHttpRequest();
		
		//Actual function to post a new customer
		xhttp.onreadystatechange = function() {
			if (this.readyState == 4 && this.status == 200) {
				
				var res = this.responseText;
				
				if(res != 'null'){
					//convert response to json object array
					var responseJson = JSON.parse(res);
					
					if(responseJson.success.toUpperCase()=="y".toUpperCase())
					{
						displayTemporaryMessage("Success","Customer Saved Successfully.");
						
						//refresh the table
						populateCustomerData();
						
						//clear the form fields
						clearModal();
						
						//Dismiss the modal
						$('#addEditCustomerModal').modal('hide');
					}
					else{
						displayTemporaryMessage("Failure","Something went wrong. Please contact admin.");
					}
				}
				else{
					displayTemporaryMessage("Failure","Something went wrong. Please try again.");
				}
			}
		};
		
		if(isAdd)
			xhttp.open("POST", "http://localhost/printer/customer", true);
		else
			xhttp.open("PUT", "http://localhost/printer/customer", true);
		
		//Adding request headers
		xhttp.setRequestHeader("Authorization", "UserTokenWillGoInHere");
		xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		
		//preparing the POST/PUT data to be sent to the API
		var formData = "";
		
		//Fetching the form data values
		var vat = document.getElementById("vat").value.trim();
		var cst = document.getElementById("vat").value.trim(); //work around - when dad said VAT/CST will be same!
		var name = document.getElementById("custName").value.trim();
		var addr1 = document.getElementById("addr1").value.trim();
		var addr2 = document.getElementById("addr2").value.trim();
		var city = document.getElementById("city").value.trim();
		var pincode = document.getElementById("pincode").value.trim();
		var pan = document.getElementById("pan").value.trim();
		
		//Adding the required fields
		formData = "vat="+vat;
		formData+= "&cst="+cst;
		formData+= "&name="+name;
		
		//Adding the optional fields
		formData+= "&addressline1="+((addr1=="")?'':addr1);
		formData+= "&addressline2="+((addr2=="")?'':addr2);
		formData+= "&city="+((city=="")?'':city);
		formData+= "&pincode="+((pincode=="")?'0':pincode);
		formData+= "&pan="+((pan=="")?'':pan);
		
		//Adding the cid form data if put request
		if(!isAdd)
			formData += "&cid="+cidVal;
		
		//sending the request
		xhttp.send(formData);
	}
	
	function clearModal(){
		//clearing the individual fields of customer form
		document.getElementById("vat").value = "";
		//document.getElementById("cst").value = "";
		document.getElementById("custName").value = "";
		document.getElementById("addr1").value = "";
		document.getElementById("addr2").value = "";
		document.getElementById("city").value = "";
		document.getElementById("pincode").value = "";
		document.getElementById("pan").value = "";
	}
	
	//document ready function
	$(document).ready(function() {
		
		//attaching an event on closing a modal
		$('#addEditCustomerModal').on('hidden.bs.modal', function(){
			
			//clear the modal
			clearModal();
			
			//also clearing the hidden field (since PUT/POST request is decided based on the value of this hidden field)
			document.getElementById('hfCustomerId').value = "";
			
			//resetting the modal title to 'Add a customer'
			document.getElementById('addEditCustomerModalLabel').innerText = "Add a Customer";
		});
	});
</script>
</html>