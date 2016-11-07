<?php
/*
 * This file consists of all the utility functions used across the entire application
 */

/*
 * Function to check whether the request header consists of the 'Authorization' header with correct token value
 * Currently token is hardcoded. In future, tokens can be stored and fetched from user table in DB.
 */
function checkUserAuthHeader()
{
	//fetch all headers
	$headers = getallheaders();
	
	//fetch 'Authorization' header
	//adding @ symbol since this line will throw a warning when Authorization header is missing
	@$authHeader = $headers["Authorization"];
	
	//fetch auth token.. maybe from database(currently hardcoded).. and perform check
	if($authHeader=='UserTokenWillGoInHere')
	{
		return true;
	}
	else
	{
		return false;
	}
}


/*
 * Forms a basic associative array to specify the success of operation performed.
 *	---------------------------------------------------------------
 *	if true is passed as the $successFlag, json response would be:
 *	{
 *		"success":"y",
 *		"message":"Specified message"
 *	}
 *	---------------------------------------------------------------
 *	if false is passed as the $successFlag, json response would be:
 *	{
 *		"success":"n",
 *		"message":"Specified message"
 *	}
 *	---------------------------------------------------------------
 */
function getResponseArray($successFlag, $message)
{
	if($successFlag)
		$success = "y";
	else
		$success = "n";
	
	return Array(
		"success"=>$success,
		"message"=>$message
	);
}

?>