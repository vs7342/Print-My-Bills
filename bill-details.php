<!DOCTYPE html>
<html>
<?php
	$title = "Bill Details";
	require_once("project-header.php");
?>
<body>
	<?php
		//Check if customer ID is posted from previous page
		if(isset($_POST['hfCid']) && $_POST['hfCid']!=null){
			$cid = $_POST['hfCid'];
			
			//Log into JS console.
			echo "<script>console.log('cid='+$cid)</script>";
		}
		else{
			die('<h3>Kindly select customer from Customer Select screen.</h3>');
		}
	?>
	
	<div class='container-fluid'>
		
		<div class='page-header'>
			<h3>Enter Bill Details</h3>
		</div>
		
		<div id='userMessages'></div>
		
		<div class='custom-form'>
			<form action='bill-print.php' method='post'>
				
				<div id='billDetails'>
					<input type='hidden' name='cid' value='<?= $cid ?>'/>
					<div class='form-group row'>
						<label for='taxInvNum' class='col-sm-1'>Tax Invoice No.</label>
						<div class='col-sm-2'>
							<input type="text" class='form-control' id='taxInvNum' name='taxInvNum' placeholder='PCE/XXX/16-17'>
						</div>
						<label for='taxInvDate' class='col-sm-1'>Tax Invoice Date</label>
						<div class='col-sm-2'>
							<input type="text" class='form-control' id='taxInvDate' name='taxInvDate' placeholder='DD/MM/YYYY'>
						</div>
						<label for='orderNum' class='col-sm-1'>Order No.</label>
						<div class='col-sm-2'>
							<input type="text" class='form-control' id='orderNum' name='orderNum' placeholder='Verbal'>
						</div>
						<label for='orderDate' class='col-sm-1'>Order Date</label>
						<div class='col-sm-2'>
							<input type="text" class='form-control' id='orderDate' name='orderDate' placeholder='DD/MM/YYYY'>
						</div>
					</div>
					<div class='form-group row'>
						<label for='challanNum' class='col-sm-1'>Challan No.</label>
						<div class='col-sm-2'>
							<input type="text" class='form-control' id='challanNum' name='challanNum' placeholder='XXX'>
						</div>
						<label for='challanDate' class='col-sm-1'>Challan Date</label>
						<div class='col-sm-2'>
							<input type="text" class='form-control' id='challanDate' name='challanDate' placeholder='DD/MM/YYYY'>
						</div>
						<label for='rrlrNum' class='col-sm-1'>R.R./L.R. No.</label>
						<div class='col-sm-2'>
							<input type="text" class='form-control' id='rrlrNum' name='rrlrNum' placeholder='XXX'>
						</div>
						<label for='rrlrDate' class='col-sm-1'>R.R./L.R. Date</label>
						<div class='col-sm-2'>
							<input type="text" class='form-control' id='rrlrDate' name='rrlrDate' placeholder='DD/MM/YYYY'>
						</div>
					</div>
					<div class='form-group row'>
						<label for='carrierName' class='col-sm-1'>Carriers Name</label>
						<div class='col-sm-2'>
							<input type="text" class='form-control' id='carrierName' name='carrierName' placeholder='Tempo / Bhiwandi / etc'>
						</div>
						<label for='paymentTerms' class='col-sm-1'>Payment Terms</label>
						<div class='col-sm-2'>
							<input type="text" class='form-control' id='paymentTerms' name='paymentTerms' placeholder='Payment Terms'>
						</div>
					</div>
				</div>
				
				<div id='itemDetails'>
					
				</div><!--end of entire item list-->
				
				<div class='form-group clearfix'>
					<button type='button' id='btnAddItem' class='btn btn-secondary col-xs-2' onClick='addItem()'>Add Item</button>
					<input type='hidden' id='hfItemQty' name='hfItemQty' value='0'/>
					<label class='col-xs-2'></label>
					<label class='col-xs-2' id='lblItemTotal' style='text-align: right;'></label>
				</div>
				
				<div id='billCalculations'>
				
					<div class='form-group clearfix'>
						<div class="form-check col-xs-2">
						  <label class="form-check-label">
								<input class="form-check-input" type="checkbox" onchange="toggleDiscount(this)" id="chkBoxDiscount">
								Apply Discount Percent
						  </label>
						</div>
						<div class='col-xs-2'>
							<input type="number" step="0.001" min="0.001" class='form-control' id='discount' name='discount' placeholder='Enter Discount %' disabled>
						</div>
						<label class='col-xs-2' id='lblAfterDiscount' style='text-align: right;'></label>
					</div>
					<div>
						<div class='form-group clearfix'>
							<div class="form-check col-xs-2">
							  <label class="form-check-label">
									<input class="form-check-input" type="radio" name="radioVatCst" id="radioVatCst" value="VAT" checked onClick='toggleVatCst(this);'>
									VAT %
							  </label>
							</div>
							<div class="form-check col-xs-2">
								<div class='col-xs-5 vatRadioGrp'>
									<input class="form-check-input" type="radio" name="radioVat" id="radioVat6" value="6" checked>6
								</div>
								<div class='col-xs-5 vatRadioGrp'>
									<input class="form-check-input" type="radio" name="radioVat" id="radioVat135" value="13.5">13.5
								</div>
							</div>
							<label class='col-xs-2' id='lblAfterVatCst' style='text-align: right;'></label>
						</div>
						<div class='form-group clearfix'>
							<div class="form-check col-xs-2">
							  <label class="form-check-label">
									<input class="form-check-input" type="radio" name="radioVatCst" id="radioVatCst" value="CST" onClick='toggleVatCst(this);'>
									CST %
							  </label>
							</div>
							<div class='col-xs-2'>
								<input type="number" step="0.01" min="0.01" class='form-control' id='cst' name='cst' value='2' disabled style='display:none'>
							</div>
						</div>
					</div>
					<div class='form-group clearfix'>
						<label for='rndOff' class='col-xs-2'>Round off</label>
						<div class='col-xs-2'>
								<input type="number" step="0.01" min="0.01" class='form-control' id='rndOff' name='rndOff' disabled value='-0.00'>
						</div>
						<label class='col-xs-2' id='lblGrandTotal' style='text-align: right;'></label>
					</div>
					<div class='form-group clearfix'>
						<div class='col-xs-2'>
							<input type='button' value='Calculate Amt' class='btn btn-secondary col-md-12' onClick='calculateGrandTotal();'/>
						</div>
						<div class='col-xs-2'>
							<input type='submit' value='Generate Bill' class='btn btn-secondary col-md-12' name='bill-print'/>
						</div>
					</div>
				</div>
				
			</form>
		</div>
		
	</div>
</body>
<?php
	require_once("project-footer.php");
?>
<script>
	function calculateGrandTotal(){
		var grandTotal = 0;
		
		//check item nums and calculate total amount for items
		var itemNums = parseInt(document.getElementById('hfItemQty').value);
		var itemTotal = 0;
		for(var i=1; i<=itemNums; i++){
			itemTotal += parseFloat(document.getElementById('amount_' + i).value);
		}
		document.getElementById('lblItemTotal').innerHTML = "Item Total = " + parseFloat(parseFloat(itemTotal).toFixed(2)).toLocaleString('hi') + " INR";
		
		//check for discount checkbox
		var afterDiscountVal = itemTotal; //Assuming no discount to be applied
		if(document.getElementById('chkBoxDiscount').checked){
			var discount = document.getElementById('discount').value;
			afterDiscountVal = (1 - discount/100) * itemTotal;
			document.getElementById('lblAfterDiscount').innerHTML = "After discount = " + parseFloat(parseFloat(afterDiscountVal).toFixed(2)).toLocaleString('hi') + " INR";
		}
		else{
			document.getElementById('lblAfterDiscount').innerHTML = "";
		}
		
		//check for VAT/CST
		var afterVatCst;
		if(document.getElementsByName('radioVatCst')[0].checked){
			//vat is selected
			
			//now check for 6 or 13.5% and add % accordingly
			if(document.getElementById('radioVat6').checked){
				//Add 6%
				afterVatCst = (1 + 6/100) * afterDiscountVal;
			}
			else{
				//Add 13.5%
				afterVatCst = (1 + 13.5/100) * afterDiscountVal;
			}
		}
		else{
			//cst is selected - Add 2%
			afterVatCst = (1 + 2/100) * afterDiscountVal;
		}
		afterVatCst = parseFloat(parseFloat(afterVatCst).toFixed(2));
		document.getElementById('lblAfterVatCst').innerHTML = "After VAT/CST = " + afterVatCst.toLocaleString('hi') + " INR";
		
		//round off
		var grandTotal = Math.round(afterVatCst);
		var roundOff = grandTotal - afterVatCst;
		document.getElementById('rndOff').value = roundOff.toFixed(2);
		
		//set grand total
		document.getElementById('lblGrandTotal').innerHTML = "Grand Total = " + grandTotal.toLocaleString('hi') + " INR";
	}

	/*
	 * Function to calculate amount for a single item
	 * @itemId is passed as a parameter
	 */
	function calculateAmount(itemId){
		
		//Fetch the needed document elements
		var quantity = document.getElementById('quantity_' + itemId).value;
		var rate = document.getElementById('rate_' + itemId).value;
		var per = document.getElementById('per_' + itemId).value;
		var amountElement = document.getElementById('amount_' + itemId);
		var amountStrElement = document.getElementById('amount_str_' + itemId);
		
		//Calculate amount and set amount field
		amountElement.value = rate / per * quantity;
		amountStrElement.value = parseFloat(parseFloat(amountElement.value).toFixed(2)).toLocaleString('hi');
	}
	
	function saveItem(itemId){
		
		calculateAmount(itemId);
		
		var itemSummaryNameQtyElement = document.getElementById('itemSummary_nameQty_'+itemId);
		var itemSummaryAmtElement = document.getElementById('itemSummary_amount_'+itemId);
		var itemName = document.getElementById('particulars_'+itemId).value;
		var itemQty = document.getElementById('quantity_'+itemId).value;
		var itemUnit = document.getElementById('unit_'+itemId).value;
		var itemAmount = document.getElementById('amount_str_'+itemId).value;
		
		itemSummaryNameQtyElement.innerHTML = "Name/Qty: "+itemName + ' / ' + itemQty + itemUnit;
		itemSummaryAmtElement.innerHTML = "Amount: "+ itemAmount + ' INR';
		
		$('#itemModal_'+itemId).modal('hide');
	}
	
	function addItem(){
		
		var itemId = parseInt(document.getElementById('hfItemQty').value) + 1;
		
		var client = new XMLHttpRequest();
		client.open('GET', 'single-item.html');
		client.onreadystatechange = function() {
			if (this.readyState == 4 && this.status == 200){
				var singleItemHtml= client.responseText;
				singleItemHtml = singleItemHtml.replace(/uniqueItemId/g, itemId);
				
				document.getElementById('itemDetails').innerHTML += singleItemHtml;
				document.getElementById('hfItemQty').value = itemId;
				
				if(itemId==4)
				{
					//3 items are added. Hide the add item button.
					var btnAddItem = document.getElementById('btnAddItem');
					btnAddItem.disabled = true;
				}
			}
		}
		client.send();
	}
	
	function toggleDiscount(chkBox){
		var discountElement = document.getElementById('discount');
		if(chkBox.checked){
			discountElement.disabled = false;
		}
		else{
			discountElement.disabled = true;
			discountElement.value = "";
			document.getElementById('lblAfterDiscount').innerHTML = "";
		}
	}
	
	function toggleVatCst(radioVatCst){
		if(radioVatCst.value=='CST'){
			document.getElementsByClassName('vatRadioGrp')[0].style.display = 'none';
			document.getElementsByClassName('vatRadioGrp')[1].style.display = 'none';
			document.getElementById('cst').style.display = 'block';
			document.getElementById('radioVat6').checked = false;
			document.getElementById('radioVat135').checked = false;
		}
		if(radioVatCst.value=='VAT'){
			document.getElementsByClassName('vatRadioGrp')[0].style.display = 'block';
			document.getElementsByClassName('vatRadioGrp')[1].style.display = 'block';
			document.getElementById('cst').style.display = 'none';
			document.getElementById('radioVat6').checked = true;
			document.getElementById('radioVat135').checked = false;
		}
	}
	
</script>
</html>