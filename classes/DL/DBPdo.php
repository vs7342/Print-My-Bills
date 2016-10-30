<?php
/*
File used from my previous project
https://github.com/vs7342/CMH_PointOfSale/blob/master/DL/DBPdo.php

Updates Done: 10/29/2016
Database connection parameters are now obtained from environment variables set in .htaccess files
*/

class DBPdo
{
	//db connection object
	private $pdo;
	
	//constructor to initialize pdo(make a connection).
	function __construct()
	{
		try
		{
			$this->pdo = new PDO(
				"mysql:host=".getenv('HOST').";dbname=".getenv('DB'),
				getenv('USER'), 
				getenv('PASSWORD')
			);
			
			//setting error mode so that pdo can throw exceptions whenever encountered
			$this->pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
		}
		catch(PDOException $pdoe)
		{
			//echo $pdoe->getMessage();//to be removed after testing the application
			$logInfo = $this->getBasicLogInfo();
			$logInfo["Error Name"] = "Connection Issue";
			$logInfo["Error Message"] = $pdoe->getMessage();
			throw new DLException($logInfo);
		}
	}
	
	
	//function to prepare the statement
	//returns a statement after preparing and binding the parameters
	function prepareStmt($query, $parameterValues, $parameterTypes)
	{
		try
		{
			//preparing the statement
			$statement = $this->pdo->prepare($query);
			
			//getting pdo parameter type constant from helper function getPDOParameterType
			$pdoParamType = array();
			foreach($parameterTypes as $singleParamType)
			{
				$pdoParamType[] = $this->getPDOParameterType(strtolower($singleParamType));
			}
			
			//binding the parameters with the corresponding values
			for($i=0; $i<count($parameterValues); $i++)
			{
				$statement->bindParam($i+1, $parameterValues[$i], $pdoParamType[$i]);
			}
			return $statement;
		}
		catch(PDOException $pdoe)
		{
			//echo $pdoe->getMessage();//to be removed after testing the application
			$logInfo = $this->getBasicLogInfo();
			$logInfo["Error Name"] = "Prepare Statement Issue";
			$logInfo["Error Message"] = $pdoe->getMessage();
			$logInfo["SQL Query"] = $query;
			throw new DLException($logInfo);
		}
	}
	
	
	//For update/insert/delete operation on DB
	//returns an associative array with 'rowsAffected' and 'insertId' values
	function setData($query, $parameterValues, $parameterTypes)
	{
		try
		{
			$statement = $this->prepareStmt($query, $parameterValues, $parameterTypes);
			$statement->execute();
			$rowsAffected = $statement->rowCount();
			$insertId = $this->pdo->lastInsertId();
			return array("RowsAffected"=>$rowsAffected,"InsertId"=>$insertId);
		}
		catch(PDOException $pdoe)
		{
			//echo $pdoe->getMessage();//to be removed after testing the application
			$logInfo = $this->getBasicLogInfo();
			$logInfo["Error Name"] = "Set Data Issue";
			$logInfo["Error Message"] = $pdoe->getMessage();
			$logInfo["SQL Query"] = $query;
			throw new DLException($logInfo);
		}
	}
	
	//Creates an array of objects of className passed
	//Sets the attributes of these objects as per the data retrieved
	//Returns null if no data retrieved.
	function getData($query, $parameterValues, $parameterTypes, $className)
	{
		try
		{
			$statement = $this->prepareStmt($query, $parameterValues, $parameterTypes);
			$statement->execute();
			$statement->setFetchMode(PDO::FETCH_CLASS,$className);
			
			$arrObjects = array();
			while($singleObj = $statement->fetch())
			{
				$arrObjects[] = $singleObj;
			}
			if(count($arrObjects)>0)
				return $arrObjects;
			else
				return null;
		}
		catch(PDOException $pdoe)
		{
			//echo $pdoe->getMessage();//to be removed after testing the application
			$logInfo = $this->getBasicLogInfo();
			$logInfo["Error Name"] = "Get Data Issue";
			$logInfo["Error Message"] = $pdoe->getMessage();
			$logInfo["SQL Query"] = $query;
			throw new DLException($logInfo);
		}
	}
	
	//Helper function
	//Takes a character input and returns the corresponding PDO Parameter type ('b'=Boolean, 'n'=null, 'i'=Int, 's'=String, 'l'=blob)
	function getPDOParameterType($char)
	{
		switch(strtolower($char))
		{
			case "i":
				return PDO::PARAM_INT;
			break;
			
			case "s":
				return PDO::PARAM_STR;
			break;
			
			case "b":
				return PDO::PARAM_BOOL;
			break;
			
			case "n":
				return PDO::PARAM_NULL;
			break;
			
			case "l":
				return PDO::PARAM_LOB;
			break;
			
			default:
				return PDO::PARAM_STR;
			break;
		}
	}
	
	//Helper Function
	//Returns an associative array with basic log info like current date/time, db connection parameters
	function getBasicLogInfo()
	{
		$info["Exception Logged at"] = date("d M Y - h:i A");
		$info["DB Host"] = getenv('HOST');
		$info["DB User"] = getenv('USER');
		$info["DB Name"] = getenv('DB');
		return $info;
	}
}
?>