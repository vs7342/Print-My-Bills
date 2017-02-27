<?php

if(isset($_POST['bill-print']))
{
	//Set cid variable (Based on selected customer) - Passed from select-customer.php to bill-details.php to this page
	$cid = $_POST['cid'];
	
	//General Bill Details
	$taxInvNum = $_POST["taxInvNum"];
	$taxInvDate = $_POST["taxInvDate"];
	$orderNum = $_POST["orderNum"];
	$orderDate = $_POST["orderDate"];
	$challanNum = $_POST["challanNum"];
	$challanDate = $_POST["challanDate"];
	$rrlrNum = $_POST["rrlrNum"];
	$rrlrDate = $_POST["rrlrDate"];
	$carrierName = $_POST["carrierName"];
	$paymentTerms = $_POST["paymentTerms"];
}
else
	die("<h1>Operation not allowed!</h1>");

?>

<!DOCTYPE html>
<html>
<head>
	  <title>Take a print</title>
</head>
<body onload="loadPrices(); loadCustomerData(<?= $cid ?>);">
	<div id="printArea">
		<link rel="stylesheet" type="text/css" href="style/bill-print.css">
		<div align ="center" id="toPrint">
			<img id="billLogo" src="images/bill-logo.png"/>
			<h1 id="heading">{Invoice Heading}</h1>
			<h3 id="subheading">{Invoice subheading}</h3>
			<hr/>
			<p id="address">{User Address Line 1, Address Line 2, City, Pincode, Contact Details}</p>
			<p id="taxinvoice">&nbsp&nbsp&nbsp <strong>TAX INVOICE</strong> &nbsp&nbsp&nbsp</p>
			<div id="billData">
				<table>
					<col width="46%">
					<col width="34%">
					<col width="20%">
					<tr>
						<td>To,</td>
						<td>Tax Invoice No: <?= $taxInvNum ?></td>
						<td>Date: <?= $taxInvDate ?></td>
					</tr>
					<tr>
						<td><strong><span id='custName'></span></strong></td>
						<td>Order No: <?= $orderNum ?></td>
						<td>Date: <?= $orderDate ?></td>
					</tr>
					<tr>
						<td><span id='addr1'></span></td>
						<td>Challan No: <?= $challanNum ?></td>
						<td>Date: <?= $challanDate ?></td>
					</tr>
					<tr>
						<td><span id='addr2'></span></td>
						<td>R.R./L.R. No: <?= $rrlrNum ?></td>
						<td>Date: <?= $rrlrDate ?></td>
					</tr>
					<tr>
						<td><span id='city'></span>, <span id='pincode'></span></td>
						<td colspan="2">Carrier's name: <?= $carrierName ?></td>
					</tr>
					<tr>
						<td>Customer PAN: <span id='pan'></span></td>
						<td colspan="2">Customer VAT/CST No.: <span id='vat'></span></td>
					</tr>
				</table>
			</div>
			<br/>
			<div id="itemData">
				<table>
					<col width="3%">
					<col width="36%">
					<col width="18%">
					<col width="9%">
					<col width="10%">
					<col width="7%">
					<col width="12%">
					<tr>
						<th rowspan="2">No</th>
						<th rowspan="2">Item Particulars</th>
						<th rowspan="2">Pkg</th>
						<th rowspan="2">Qty</th>
						<th colspan="2">Rate</th>
						<th rowspan="2">Amt</th>
					</tr>
					<tr>
						<th>Rs.</th>
						<th>Per Unit</th>
					</tr>
					<?php
						/*
						 * Calculate Bill Amount - Item Total - Discount - VAT/CST - Round Off
						 */
						
						//Item Total
						$itemQty = intval($_POST["hfItemQty"]);
						$itemTotal = 0;
						$singleItemPrices = [];
						for($i=0; $i<$itemQty; $i++){
							
							$singleItemRate = $_POST["rate"][$i];
							$singleItemPer = $_POST["per"][$i];
							$singleItemQty = $_POST["quantity"][$i];
							
							$singleItemParticulars = $_POST["particulars"][$i];
							$singleItemPackage = $_POST["package"][$i];
							$singleItemUnit = $_POST["unit"][$i];
							
							//$singleItemAmount = number_format(($singleItemRate / $singleItemPer * $singleItemQty), 2, ".", ",");
							$singleItemAmount = round(($singleItemRate / $singleItemPer * $singleItemQty), 2);
							
							$singleItemPrices[] = $singleItemAmount;
							$singleItemRates[] = $singleItemRate;
							
							echo "
								<tr class='rowItem'>
									<td>".($i+1)."</td>
									<td>$singleItemParticulars</td>
									<td>$singleItemPackage</td>
									<td>$singleItemQty $singleItemUnit</td>
									<td><span id='item_rate_$i'></span></td>
									<td>$singleItemPer $singleItemUnit</td>
									<td><span id='item_amount_$i'></span></td>
								</tr>
							";

							$itemTotal += $singleItemAmount;
						}
						
						$rowCountLeftover = $itemQty - 1;
						
						//Add the row for Item Total
						echo "
							<tr style='vertical-align: bottom;' id='itemTotalRow'>
								<td colspan='3' class='no-data-cell'></td>
								<td colspan='3'>Item Total</td>
								<td><span id='itemTotal'></td>
							</tr>
						";
						
						//Check for Discount and Apply accordingly
						$afterDiscount = $itemTotal;
						if(array_key_exists("discount",$_POST))
						{
							$discountAmt = round(($_POST["discount"]/100 * $itemTotal), 2);
							$afterDiscount = round(($itemTotal - $discountAmt), 2);
							
							echo "
								<tr>
									<td colspan='3' class='no-data-cell'></td>
									<td colspan='3' valign='top'>Less ".$_POST["discount"]."% Discount</td>
									<td><span id='discountAmt'></span><br/><span id='afterDiscount'></span></td>
								</tr>
							";
							
							$rowCountLeftover += 2;
						}
						
						//Apply VAT/CST
						$vatCstPercent = 0;
						$varOrCst = $_POST["radioVatCst"];
						if($varOrCst=="VAT")
						{
							//Vat is selected - It can be 6 or 13.5% - Based on "radioVat"
							$vatCstPercent = $_POST["radioVat"];
						}
						else
						{
							//Cst is selected - CST is 2%
							$vatCstPercent = 2;
						}
						$vatCstAmount = round(($vatCstPercent / 100 * $afterDiscount), 2);
						$afterVatCst = round(($afterDiscount + $vatCstAmount), 2);
						
						echo "
							<tr>
								<td colspan='3' class='no-data-cell' style='text-align: center;'>
									<div id='forIndustrialUse'>
										<strong>FOR INDUSTRIAL USE ONLY</strong>
										
										<div id='insideIndustrialUse'>
											Form: <strong>$vatCstPercent% $varOrCst &nbsp&nbsp&nbsp</strong>
											Payment Terms: <strong>".$paymentTerms."</strong>
										</div>
									</div>
								</td>
								<td colspan='3' valign='top'>Add $vatCstPercent% $varOrCst</td>
								<td><span id='vatCstAmount'></span><br/><span id='afterVatCst'></span></td>
							</tr>
						";
						
						//Calculate Grand Total and Round Off if any
						$grandTotal = round($afterVatCst);
						$roundOff = round(($grandTotal - $afterVatCst), 2);
						
						if($roundOff>0){
							echo "
								<tr>
									<td colspan='3' class='no-data-cell'></td>
									<td colspan='3' valign='top'>Add for R/O</td>
									<td><span id='roundOff'></span></td>
								</tr>
							";
							$rowCountLeftover++;
						}
						else if($roundOff<0){
							echo "
								<tr>
									<td colspan='3' class='no-data-cell'></td>
									<td colspan='3' valign='top'>Less for R/O</td>
									<td><span id='roundOff'></span></td>
								</tr>
							";
							$rowCountLeftover++;
						}
						
						$grandTotalText = "";
						
						//http://stackoverflow.com/questions/33876590/class-numberformatter-not-found-error-in-simple-php-program
						$f = new NumberFormatter("en", NumberFormatter::SPELLOUT); 
						$grandTotalThousands = $grandTotal;
						if($grandTotal>99999)
						{
							$lakh = (int)substr($grandTotal, 0, -5);
							$grandTotalText .= $f->format($lakh)." lakh ";
							$grandTotalThousands = $grandTotal % 100000;
						}
						
						$grandTotalText .= $f->format($grandTotalThousands);
						
						$grandTotalText = ucwords(strtolower($grandTotalText));
					?>
					<tr>
						<td colspan="6">Amount in words: Rupees <?= $grandTotalText ?> only.</td>
						<td><strong><span id='grandTotal'></span></strong></td>
					</tr>
				</table>
			</div>
			<div id="rb">
				<div id="vatCst">
					VAT TIN {USERVATTIN#} V w.e.f. 01-04-06
					<br/>
					CST TIN {USERCSTTIN#} C w.e.f. 01-04-06
					<br/>
					PAN No: {USERPAN#}
				</div>
			</div>
			<div id="declaration">
				"Declaration for user invoice. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed vel tortor risus. Nulla aliquet pulvinar lectus, congue cursus eros commodo sit amet. Cras aliquet est arcu, vitae sollicitudin erat pellentesque sit amet. Sed vel tortor risus. Nulla aliquet pulvinar lectus, congue cursus eros commodo sit amet. Cras aliquet est arcu, vitae sollicitudin erat pellentesque sitad amet sit amet. Declaration for user invoice."
			</div>
			<div id="terms">
				<strong>Terms:</strong>
				<ol>
					<li>Duis tempor convallis tellus, quis auc.</li>
					<li>Phasellus vel ultrices diam, eu finibus neque. Proin erat era.</li>
					<li>Maecenas non orci hendrerit, aliquet elit eu, commodo ipsum. Maecenas commodo efficitur semper. Proin erat era grradd.</li>
					<li>Sed auctor diam id risus volut.</li>
				</ol>
			</div>
			<div id="signature">
				For <strong>{Company Name}</strong>
				<br/><br/><br/>
				Proprieter
			</div>
		</div>
	</div>
	<button name='Print' onclick="printDiv()">Print</button>
</body>
<?php
	require_once("project-footer.php");
?>
<script>

	//Reference: http://stackoverflow.com/questions/12997123/print-specific-part-of-webpage
	function printDiv() {
		var printContent = document.getElementById("printArea");
		var WinPrint = window.open();//'', '', 'left=0,top=0,width=800,height=900,toolbar=0,scrollbars=0,status=0');
		WinPrint.document.write(printContent.innerHTML);
		WinPrint.document.close();
		WinPrint.focus();
		WinPrint.print();
		WinPrint.close();
	}
	
	function loadPrices(){
		//setting the item price JS array (using php values)
		var item_prices =<?php echo json_encode($singleItemPrices );?>;
		var item_rates =<?php echo json_encode($singleItemRates );?>;
		for (i = 0; i < item_prices.length; i++) {
			 document.getElementById('item_amount_'+i).innerHTML = getIndianFormatAmount(item_prices[i]);
			 document.getElementById('item_rate_'+i).innerHTML = getIndianFormatAmount(item_rates[i]);
		}
		
		//Setting Item Total
		document.getElementById('itemTotal').innerHTML = getIndianFormatAmount(<?= $itemTotal ?>);
		
		//Setting Discount if any
		var discountAmt = document.getElementById('discountAmt');
		var afterDiscount = document.getElementById('afterDiscount');
		if(discountAmt != undefined && discountAmt != null)
		{
			discountAmt.innerHTML = getIndianFormatAmount(<?= @$discountAmt ?>);
			afterDiscount.innerHTML = getIndianFormatAmount(<?= @$afterDiscount ?>);
		}
		
		//Setting VAT/CST
		document.getElementById('vatCstAmount').innerHTML = getIndianFormatAmount(<?= $vatCstAmount ?>);
		document.getElementById('afterVatCst').innerHTML = getIndianFormatAmount(<?= $afterVatCst ?>);
		
		//Setting Round off if any
		var roundOff = document.getElementById('roundOff');
		if(roundOff != undefined && roundOff != null)
		{
			roundOff.innerHTML = Math.abs(getIndianFormatAmount(<?= @$roundOff ?>));
		}
		
		//Setting the Grand Total
		document.getElementById('grandTotal').innerHTML = getIndianFormatAmount(<?= $grandTotal ?>);
		
		//Setting the height of item total row
		var rowCountLeftover = <?= $rowCountLeftover ?>;
		var itemTotalRowHeight = 375 - (23*rowCountLeftover);
		document.getElementById('itemTotalRow').style.height = itemTotalRowHeight+"px";
	}
	
	function getIndianFormatAmount(stringAmt){
		return parseFloat(parseFloat(stringAmt).toFixed(2)).toLocaleString('hi', {minimumFractionDigits: 2});
	}
	
	function loadCustomerData(cid){		
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
					
					//fill the table with customer details
					document.getElementById("vat").innerHTML = customerJson.vat;
					//document.getElementById("cst").value = customerJson.cst;
					document.getElementById("custName").innerHTML = customerJson.name;
					document.getElementById("addr1").innerHTML = customerJson.addressline1;
					document.getElementById("addr2").innerHTML = customerJson.addressline2;
					document.getElementById("city").innerHTML = customerJson.city;
					document.getElementById("pincode").innerHTML = customerJson.pincode;
					document.getElementById("pan").innerHTML = customerJson.pan;
					
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
</script>
</html>