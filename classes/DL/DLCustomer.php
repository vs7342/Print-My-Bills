<?php

//auto-loading necessary class files
function __autoload($file)
{
	@include_once("$file.php");
}

class DLCustomer
{
	/*Attributes*/
	private $cid;
	private $vat;
	private $cst;
	private $name;
	private $addressline1;
	private $addressline2;
	private $city;
	private $pincode;
	private $pan;
	
	/*Constructor*/
	function __construct(
		$cid=null,
		$vat=null,
		$cst=null,
		$name=null,
		$addressline1=null,
		$addressline2=null,
		$city=null,
		$pincode=null,
		$pan=null
	)
	{
		if($cid!==null)
			$this->cid=$cid;
		if($vat!==null)
			$this->vat=$vat;
		if($cst!==null)
			$this->cst=$cst;
		if($name!=null)
			$this->name=$name;
		if($addressline1!=null)
			$this->addressline1=$addressline1;
		if($addressline2!=null)
			$this->addressline2=$addressline2;
		if($city!=null)
			$this->city=$city;
		if($pincode!==null)
			$this->pincode=$pincode;
		if($pan!=null)
			$this->pan=$pan;
	}
	
	/*Getters and Setters*/
	function getCid(){
		return $this->cid;
	}
	function getVat(){
		return $this->vat;
	}
	function getCst(){
		return $this->cst;
	}
	function getName(){
		return $this->name;
	}
	function getAddressline1(){
		return $this->addressline1;
	}
	function getAddressline2(){
		return $this->addressline2;
	}
	function getCity(){
		return $this->city;
	}
	function getPincode(){
		return $this->pincode;
	}
	function getPan(){
		return $this->pan;
	}
	
	function setCid($cid){
		if($cid!==null)
			$this->cid = $cid;
	}
	function setVat($vat){
		if($vat!==null)
			$this->vat = $vat;
	}
	function setCst($cst){
		if($cst!==null)
			$this->cst = $cst;
	}
	function setName($name){
		if($name!=null)
			$this->name = $name;
	}
	function setAddressline1($addressline1){
		if($addressline1!=null)
			$this->addressline1 = $addressline1;
	}
	function setAddressline2($addressline2){
		if($addressline2!=null)
			$this->addressline2 = $addressline2;
	}
	function setCity($city){
		if($city!=null)
			$this->city = $city;
	}
	function setPincode($pincode){
		if($pincode!==null)
			$this->pincode = $pincode;
	}
	function setPan($pan){
		if($pan!=null)
			$this->pan = $pan;
	}
	
	/*Methods*/
	
	///<summary>
	///Inserts the customer into DB. 
	///If successfully inserted, cid value of current instance is set to inserted cid and true is returned.
	///Else false is returned
	///</summary>
	function insert(){
		//check for not null constraint violation for vat and cst
		if($this->getVat()===null || $this->getCst()===null){
			return false;
		}
		$query = "
			INSERT INTO CUSTOMERS 
			(VAT, CST, NAME, ADDRESSLINE1, ADDRESSLINE2, CITY, PINCODE, PAN)
			VALUES
			(?,?,?,?,?,?,?,?)
		";
		$paramVals = array(
			$this->getVat(),
			$this->getCst(),
			$this->getName(),
			$this->getAddressline1(),
			$this->getAddressline2(),
			$this->getCity(),
			$this->getPincode(),
			$this->getPan()
		);
		$paramTypes = array(
			'i','i','s','s','s','s','i','s'
		);
		
		try{
			$db = new DBPdo();
			$result = $db->setData($query, $paramVals, $paramTypes);
			if($result["InsertId"]!=null){
				$this->setCid($result["InsertId"]);
				return true;
			}
		}catch(DLException $dle){
			throw $dle;
		}
	}
	
	
	///<summary>
	///Returns a DLCustomer object based on customer CID
	///</summary>
	function getCustomerByCid(){
		$query = "SELECT * FROM CUSTOMERS WHERE CID = ?";
		$paramVals = array($this->getCid());
		$paramTypes = array('i');
		
		try{
			$db = new DBPdo();
			$result = $db->getData($query, $paramVals, $paramTypes, "DLCustomer");
			return $result[0];
		}catch(DLException $dle){
			throw $dle;
		}
	}
	
	///<summary>
	///Returns an array of DLCustomer objects based on customer name
	///</summary>
	function getCustomersByName(){
		$query = "SELECT * FROM CUSTOMERS WHERE NAME LIKE ?";
		$paramVals = array("%".$this->getName()."%");
		$paramTypes = array('s');
		
		try{
			$db = new DBPdo();
			$customers = $db->getData($query, $paramVals, $paramTypes, "DLCustomer");
			return $customers;
		}catch(DLException $dle){
			throw $dle;
		}
	}
	
	///<summary>
	///Updates customer details. Returns number of rows affected. Returns -1 otherwise.
	///</summary>
	function update(){
		
		if($this->getCid()===null)
			return -1;
		
		$query = "UPDATE CUSTOMERS SET ";
		$paramVals = array();
		$paramTypes = array();
		
		if($this->getVat()!==null){
			$query .= "VAT = ?,";
			array_push($paramVals, $this->getVat());
			array_push($paramTypes, 'i');
		}
		if($this->getCst()!==null){
			$query .= "CST = ?,";
			array_push($paramVals, $this->getCst());
			array_push($paramTypes, 'i');
		}
		if($this->getName()!=null){
			$query .= "NAME = ?,";
			array_push($paramVals, $this->getName());
			array_push($paramTypes, 's');
		}
		if($this->getAddressline1()!=null){
			$query .= "ADDRESSLINE1 = ?,";
			array_push($paramVals, $this->getAddressline1());
			array_push($paramTypes, 's');
		}
		if($this->getAddressline2()!=null){
			$query .= "ADDRESSLINE2 = ?,";
			array_push($paramVals, $this->getAddressline2());
			array_push($paramTypes, 's');
		}
		if($this->getCity()!=null){
			$query .= "CITY = ?,";
			array_push($paramVals, $this->getCity());
			array_push($paramTypes, 's');
		}
		if($this->getPincode()!==null){
			$query .= "PINCODE = ?,";
			array_push($paramVals, $this->getPincode());
			array_push($paramTypes, 'i');
		}
		if($this->getPan()!=null){
			$query .= "PAN = ?,";
			array_push($paramVals, $this->getPan());
			array_push($paramTypes, 's');
		}
		
		$query = rtrim($query, ",");
		
		$query .= " WHERE CID = ?";
		array_push($paramVals, $this->getCid());
		array_push($paramTypes, 'i');
		
		try{
			$db = new DBPdo();
			$result = $db->setData($query, $paramVals, $paramTypes);
			return $result["RowsAffected"];
		}catch(DLException $dle){
			throw $dle;
		}
	}
	
	///<summary>
	///Deletes a customer based on its CID. Returns true if record is found, false if not found
	///</summary>
	function delete(){
		$query = "DELETE FROM CUSTOMERS WHERE CID = ?";
		$paramVals = array($this->getCid());
		$paramTypes = array('i');
		
		try{
			$db = new DBPdo();
			$result = $db->setData($query, $paramVals, $paramTypes);
			if($result["RowsAffected"] > 0){
				return true; //record found
			}
			else{
				return false; //record not found
			}
		}catch(DLException $dle){
			throw $dle; //error encountered
		}
	}
	
	///<summary>
	///Checks customer table for an existing VAT entry. Returns true/false based on entries found or not.
	///</summary>
	function isVatPresent(){
		$query = "SELECT CID FROM CUSTOMERS WHERE VAT = ?";
		$paramVals = array($this->getVat());
		$paramTypes = array('i');
		
		try{
			$db = new DBPdo();
			$result = $db->getData($query, $paramVals, $paramTypes, "DLCustomer");
			if($result===null)
				return false;
			else
				return true;
		}catch(DLException $dle){
			throw $dle;
		}
	}
	
	///<summary>
	///Checks customer table for an existing CST entry. Returns true/false based on entries found or not.
	///</summary>
	function isCstPresent(){
		$query = "SELECT CID FROM CUSTOMERS WHERE CST = ?";
		$paramVals = array($this->getCst());
		$paramTypes = array('i');
		
		try{
			$db = new DBPdo();
			$result = $db->getData($query, $paramVals, $paramTypes, "DLCustomer");
			if($result===null)
				return false;
			else
				return true;
		}catch(DLException $dle){
			throw $dle;
		}
	}
	
	/*Helper Methods*/
	
	///<summary>
	///Magic function toString(for debugging purpose)
	///</summary>
	function __toString(){
		return "Object{Customer:
			CID=".$this->getCid().",
			Name=".$this->getName().",
			City=".$this->getCity()."
		}";
	}
}

?>