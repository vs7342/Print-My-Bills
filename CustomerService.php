<?php

require_once("util.php");
require_once("GenericRestService.php");
require_once("classes/BLCustomer.php");

class CustomerService extends GenericRestService
{
	public function __construct($request)
	{
		//calling the parent constructor
		//Initializes(or extracts) various request parameters
		parent::__construct($request);
	}
	
	// endpoint = /customer
	protected function customer($args)
	{
		// method   = GET
		// params   = {name}
		if($this->requestMethod=="GET" && count($args)>0 && array_key_exists("name", $args))
		{
			$customers = new BLCustomer();
			$customers->setName($args["name"]);
			$customers = $customers->getCustomersByName();
			
			if($customers!==null)
			{
				foreach($customers as $customer)
				{
					$singleCustomer["name"]=$customer->getName();
					$singleCustomer["cid"]=$customer->getCid();
					$customersArray[] = $singleCustomer;
				}
				return $customersArray;
			}
			else
				return null;
		}
		
		// method   = GET
		// params   = {cid}
		else if($this->requestMethod=="GET" && count($args)>0 && array_key_exists("cid", $args))
		{
			$customer = new BLCustomer();
			$customer->setCid($args["cid"]);
			$customer = $customer->getCustomerByCid();
			
			if($customer!==null)
			{
					$singleCustomer["vat"]=$customer->getVat();
					$singleCustomer["cst"]=$customer->getCst();
					$singleCustomer["name"]=$customer->getName();
					$singleCustomer["addressline1"]=$customer->getAddressline1();
					$singleCustomer["addressline2"]=$customer->getAddressline2();
					$singleCustomer["city"]=$customer->getCity();
					$singleCustomer["pincode"]=$customer->getPincode();
					
					return $singleCustomer;
			}
			else
				return null;
		}
		
		// method   = POST
		// params   = {customer}
		else if($this->requestMethod=="POST")
		{
			//adding an @ symbol since required fields are vat and cst only as of this time
			//Also required field check in done in data layer
			@$customerObj = new BLCustomer(
				null,
				$args["vat"],
				$args["cst"],
				$args["name"],
				$args["addressline1"],
				$args["addressline2"],
				$args["city"],
				$args["pincode"]
			);
			if($customerObj->insert())
			{
				return getResponseArray(true, "Customer inserted successfully.");
			}
			else
			{
				return getResponseArray(false, "Could not insert customer. Kindly contact the admin.");
			}
		}
		
		// method   = DELETE
		// params   = {cid}
		else if($this->requestMethod=="DELETE" && array_key_exists("cid", $args))
		{
			$customer = new BLCustomer($args["cid"]);
			
			if($customer->delete())
				return getResponseArray(true, "Customer deleted successfully.");
			else
				return getResponseArray(false, "Customer does not exists.");
		}
		
		// method   = PUT
		// params   = {customer}
		else if($this->requestMethod=="PUT")
		{
			if(array_key_exists("cid", $args))
			{
				//Adding an @ symbol since only one column might need an update
				@$customerObj = new BLCustomer(
					$args["cid"],
					$args["vat"],
					$args["cst"],
					$args["name"],
					$args["addressline1"],
					$args["addressline2"],
					$args["city"],
					$args["pincode"]
				);
				
				if($customerObj->update()>0)
				{
					return getResponseArray(true, "Customer updated successfully.");
				}
				else
				{
					return getResponseArray(false, "Customer not found.");
				}
			}
			else
			{
				return getResponseArray(false, "Customer ID is required[cid]");
			}
		}
		
		//provide documentation regarding the service provided
		//currently it just lists the functionality provided by the 'customer' service
		//need to add description for each service
		else
		{
			$basicArray["info"] = getResponseArray(false, "Requested Resource is not available");
			
			$basicArray["Only following operations are possible with the given service"] = Array(
				Array(
					"Method"=>"GET",
					"Params"=>"cid"
				),
				Array(
					"Method"=>"GET",
					"Params"=>"name"
				),
				Array(
					"Method"=>"POST",
					"Params"=>"customer form data"
				),
				Array(
					"Method"=>"PUT",
					"Params"=>"customer form data"
				),
				Array(
					"Method"=>"DELETE",
					"Params"=>"cid"
				)
			);
			
			return $basicArray;
		}
	}
}

try
{
	//Check if user is authorized - 'Authorization' header value == userToken
	if(checkUserAuthHeader())
	{
		$custApi = new CustomerService($_REQUEST["request"]);
		
		echo $custApi->processAPI();
	}
	else
	{
		echo json_encode(getResponseArray(false, "User Not Authorized."));
	}
}
catch(Exception $e)
{
	echo json_encode(getResponseArray(false, $e->getMessage()));
}

?>