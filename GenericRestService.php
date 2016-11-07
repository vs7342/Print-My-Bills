<?php
/*
 * Abstract class which handles the request received by the server.
 * It basically processes the request and calls the corresponding function implemented in the concrete class
 *
 * Reference Used- http://coreymaynard.com/blog/creating-a-restful-api-with-php/
 */
abstract class GenericRestService
{
	//request parameters
	protected $requestArgs = Array();
	protected $requestEndpoint = "";
	protected $optionalVerb = "";
	protected $requestMethod = "";
	
	//attribute used during 'PUT' and 'DELETE' requests
	protected $fileStr = null;
	
	//Used for debugging purpose. Dumps the request parameters in the response if set to true
	private $debugMode = false;
	
	/*
	 * The constructor initializes various request parameters :
	 * requestArgs - Arguments passed in the request
	 * requestEndpoint - Route
	 * requestMethod - GET/POST/PUT/DELETE
	 */
	public function __construct($request)
	{
		$this->requestArgs = explode('/', rtrim($request, '/'));
		
		$this->requestEndpoint = array_shift($this->requestArgs);
		
		if(
			array_key_exists(0, $this->requestArgs) &&
			!is_numeric($this->requestArgs[0])
		)
		{
			$this->optionalVerb = array_shift($this->requestArgs);
		}
		
		$this->requestMethod = $_SERVER["REQUEST_METHOD"];
		if(
			$this->requestMethod == "POST" &&
			array_key_exists("HTTP_X_HTTP_METHOD",$_SERVER)
		)
		{
			if ($_SERVER["HTTP_X_HTTP_METHOD"] == "DELETE")
			{
				$this->requestMethod = "DELETE";
			} 
			else if ($_SERVER["HTTP_X_HTTP_METHOD"] == "PUT") 
			{
				$this->requestMethod = "PUT";
			} 
			else 
			{
				throw new Exception("Unexpected Header");
			}
		}
		
		switch($this->requestMethod)
		{
			case "POST":
				$this->requestArgs = $this->sanitizeInputs($_POST);
				break;
				
			case "GET":
				$this->requestArgs = $this->sanitizeInputs($_GET);
				break;
			
			/*
				For DELETE and PUT requests, request arguments have to be read from request body
				php://input - read-only stream to read raw data from request body
				file_get_contents - reads a file into a string
				
				php://input is in the form "key1=value1&key2=value2"
				parse_str converts this type of string into an associative array(when 2nd argument is passed)
			*/
			
			case "DELETE":
				$this->fileStr = file_get_contents("php://input");
				parse_str($this->fileStr, $deleteArgs);
				$this->requestArgs = $this->sanitizeInputs($deleteArgs);
				break;
				
			case "PUT":
				$this->fileStr = file_get_contents("php://input");
				parse_str($this->fileStr, $putArgs);
				$this->requestArgs = $this->sanitizeInputs($putArgs);

				break;
				
			default:
				$this->response("Method Not Allowed", 405);
				break;
		}
		
		//for debugging purpose
		if($this->debugMode){
			echo "Request args = ";
			var_dump($this->requestArgs);
			echo "<br/>";
			echo "Request endpoint = ";
			var_dump($this->requestEndpoint);
			echo "<br/>";
			echo "Request method = ";
			var_dump($this->requestMethod);
			echo "<br/>";
		}
	}
	
	/*
	 * Function to sanitize the various inputs
	 * Uses trim and strip_tags in conjunction
	 * If an array is passed, values in the array are sanitized
	 */
	private function sanitizeInputs($data)
	{
		$sanitized = array();
		
		if(is_array($data))
		{
			foreach($data as $key => $value)
			{
				$sanitized[$key] = trim(strip_tags($value));
			}
		}
		else
		{
			$sanitized = trim(strip_tags($value));
		}
		
		return $sanitized;
	}
	
	/*
	 * Returns the status message corresponding to the status code
	 */
	private function requestStatus($code)
	{
		$possibleStatus = array(
			200=>"OK",
			404=>"Not Found",
			405=>"Method Not Allowed",
			500=>"Internal Server Error"
		);
		
		return ($possibleStatus[$code]) ? $possibleStatus[$code] : $possibleStatus[500];
	}
	
	/*
	 * Creates a response with JSON data and mentioned headers.
	 * $data would be mostly an associative array which can be encoded in json easily.
	 */
	private function response($data, $status = 200)
	{
		//Setting the response status
		header("HTTP/1.1 $status ".$this->requestStatus($status));
		
		//Response will be in json format
		header('Content-type: application/json');
		
		if($status != 200)
			die();
		
		//Form a json response to send
		return json_encode($data); 
	}
	
	
	/*
	 * The only public method which calls the method implemented in concrete class
	 * If method does not exists, 404 is returned
	 */
	public function processAPI()
	{
		if(method_exists($this, $this->requestEndpoint))
		{
			//Fetching the child function name and arguments
			$functionName = $this->requestEndpoint;
			$functionArgs = $this->requestArgs;
			
			//Variable Function
			$functionResponseData = $this->$functionName($functionArgs);
			
			return $this->response($functionResponseData);
		}
		else
		{
			return $this->response("No Endpoint found: {$this->requestEndpoint}", 404);
		}
	}
}

?>