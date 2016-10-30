<?php
/*
File directly used from my following project
https://github.com/vs7342/CMH_PointOfSale/blob/master/DL/DLException.php
*/

class DLException extends Exception
{
	function __construct($logInfo)
	{
		//just calls the log function which logs all the exception details to a file
		$this->logException($logInfo);
	}
	
	//function which creates/updates the log file with the details of error encountered
	function logException($logInfo)
	{
		$file = fopen("logs/DLExceptionLog.txt","a+");
		
		foreach($logInfo as $key=>$value)
		{
			fwrite($file,$key." : ".$value."\n");
		}
		fwrite($file,"=========================================================================\n");
		fclose($file);
	}
}