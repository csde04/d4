<?php



/* NEED AUTHENTICATION STUFF:
$header[] = 'Authorization: Basic ' . base64_encode("frank3:fnjpar3iii");
*/



class webRequest2
{
	private $socket;

	protected $finalURL;
	protected $rawContent;
	protected $rawHeader;
	protected $rawResponse;
	
	protected $caughtEarlyTerm;
	protected $chunkedLength;
	protected $chunkedTransfer;
	protected $cookies;
	protected $cookieStr;
	protected $errorFlag;
	protected $getList;
	protected $headers;
	protected $postList;
	protected $postStr;
	
	public $accept;
	public $authName;
	public $authPass;
	public $charSet;
	public $domain;
	public $debugLogFile;
	public $debugLogClearOnDispatch;
	public $debugMode;
	public $earlyTermStr;
	public $language;
	public $manualPostContent;
	public $method;
	public $port;
	public $postMode;
	public $proxy;
	public $redirect;
	public $referer;
	public $resultCode;
	public $timeout;
	public $url;
	public $userAgent;
	public $useSSL;
	
	// Event Handlers
	public $onFailure;
	public $onProxyRetry;
	public $onSuccess;

	// Protected and special functions
	function webRequest2()
	{
		$this->reset();
		preg_match('/^([0-9])/', phpversion(), $parts);
		$this->ancient = ($parts[1] < '5');
		$this->userAgent = 'Mozilla/5.0 (Macintosh; U; PPC Mac OS X; en) AppleWebKit/417.9 (KHTML, like Gecko) Safari/417.8';
		$this->accept = 'text/xml,application/xml,application/xhtml+xml,text/html;q=0.9,text/plain;q=0.8,image/png,*/*;q=0.5';
		$this->charSet = 'ISO-8859-1,utf-8:q=0.7,*;q=0.7';
		$this->language = 'en-us,en;q=0.5';
		$this->referer = '';
		
		if (!defined('WRD_OFF'))
		{
			define('WRD_OFF', 0);
			define('WRD_ECHO', 1);
			define('WRD_LOG', 2);
			
			define('WRM_GET', 0);
			define('WRM_POST', 1);
			
			define('WRP_NORMAL', 0);
			define('WRP_MULTIPART', 1);
		}
		
		$this->debugMode = WRD_OFF;
		$this->postMode = WRP_NORMAL;
		$this->debugLogFile = '';
		$this->debugLogClearOnDispatch = true;
		$this->timeout = 30;
		$this->useSSL = false;
		$this->proxy = '';
	}
	
	protected function buildCookieStr()
	{
		$cookieStr = '';
		$start = true;
		foreach($this->cookies as $name=>$value)
		{
			if (!$start) { $cookieStr .= '; '; }
			$cookieStr .= "$name=$value";
			$start = false;
		}
		$this->debug("Built COOKIE String: $cookieStr");
		return $cookieStr;
	}
	
	protected function buildGetStr()
	{
		$getStr = '';
		$getCount = count($this->getList);
		if ($getCount)
		{
			$sepStr = '?';
			foreach($this->getList as $name=>$value)
			{
				$value = urlencode($value);
				$getStr .= "$sepStr$name=$value";
				$sepStr = '&';
			}
		}
		$this->debug("Built GET String: $getStr");
		return $getStr;
	}
	
	protected function buildPostStr()
	{
		if ($this->manualPostContent)
			return $this->manualPostContent;
			
		$postStr = '';
		$postCount = count($this->postList);
		if ($postCount)
		{
			$sepStr = '';
			foreach($this->postList as $name=>$arr)
			{
				if ($arr['type'] == 'XML')
				{
					$value = $arr['content'];
				} else {
					$value = urlencode($arr['content']);
				}
				$postStr .= "$sepStr$name=$value";
				$sepStr = '&';
			}
		} else {
			$postStr = 'No Content';
		}
		$this->debug("Built POST String: $cookieStr");
		return $postStr;
	}
		
	protected function buildHeader()
	{

		$header[0] = ''; // place holder for first line of header
		$header[] = "Host: {$this->domain}";
		$header[] = "User-Agent: {$this->userAgent}";
		$header[] = "Accept: {$this->accept}";
		$header[] = "Accept-Language: {$this->language}";
		$header[] = "Accept-Encoding: ";
		$header[] = "Accept-Charset: {$this->charSet}";
		if ($this->authName) { $header[] = 'Authorization: Basic ' . base64_encode("{$this->authName}:{$this->authPass}"); }
		if ($this->referer) { $header[] = "Referer: {$this->referer}"; }
		if ($this->hasCookies()) { $header[] = "Cookie: {$this->buildCookieStr()}"; }
		$header[] = "Connection: close";	

		$hostStr = ($this->proxy) ? "http://{$this->domain}" : '';
		switch($this->method)
		{
			case 'get':
			case 'GET':
			case WRM_GET:
				$header[0] = "GET $hostStr{$this->finalURL} HTTP/1.1";
				$header[] = '';
				$header[] = "Content-Type: text/html";
				$header[] = "Content-Length: 0";
				$header[] = '';
				break;
				
			case 'post':
			case 'POST':
			case WRM_POST:
				if (count($this->postList) == 0) $this->postMode = WRP_NORMAL;
				
				$header[0] = "POST $hostStr{$this->finalURL} HTTP/1.1";
				switch ($this->postMode)
				{
					case WRP_NORMAL:
						$postData = $this->buildPostStr();
						$requestLen = strlen($postData);
						$header[] = "Content-Type: application/x-www-form-urlencoded";
						$header[] = "Content-Length: $requestLen";
						$header[] = '';
						$header[] = $postData;
						break;
						
					case WRP_MULTIPART:
						$boundary = time() . time();
						$postData = $this->buildMultipartPostStr($boundary);
						$requestLen = strlen($postData);
						$header[] = "Content-Type: multipart/form-data; boundary=$boundary";
						$header[] = "Content-Length: $requestLen";
						$header[] = '';
						$header[] = "$postData";
						break;
						
					default:
						$this->debug("buildHeader: Terminal failure - unknown postMode '{$this->postMode}'");
						throw new Exception("buildHeader: Terminal failure - unknown postMode '{$this->postMode}'");
						break;
				}				
				break;
				
			default:
				$this->debug("buildHeader: Terminal failure - unknown method '{$this->method}'");
				throw new Exception("buildHeader: Terminal failure - unknown method '{$this->method}'");
				break;
		}
		
		$out = implode("\r\n", $header);
		$this->debug("Outbound Header:\n$out");
		return $out;
	}
	
	protected function buildMultipartPostStr($boundary)
	{
		$out = array();
		foreach($this->postList as $name=>$arr)
		{
			$value = $arr['content'];
			$type = $arr['type'];
			$out[] = "--$boundary";
			$out[] = "Content-Disposition: form-data; name=\"$name\"";
			$out[] = "Content-type: $type";
			$out[] = '';
			$out[] = "$value";
		}
		
		$out[] = "--$boundary--";
			
		return implode("\r\n", $out);
	}
	
	protected function buildURL()
	{
		$this->finalURL = "{$this->url}{$this->buildGetStr()}";
		$this->debug("FinalURL: {$this->finalURL}");
	}
	
	protected function clearDebugLog()
	{
		if ($this->debugMode == WRD_LOG)
		{
			if (!$this->debugLogFile)
				throw new Exception('webRequest2: Debug mode set to LOG, but debugLogFile property not set');
			if (file_put_contents($this->debugLogFile, '') === false)
				throw new Exception('webRequest2: Debug mode set to LOG, but debugLogFile cannot be written to');
		}
	}
	
	protected function debug($msg)
	{
		switch($this->debugMode)
		{
			case WRD_OFF: 
				return;
				
			case WRD_ECHO: 
				echo "$msg\n";
				break;
				
			case WRD_LOG:
				if (!$this->debugLogFile)
					throw new Exception('webRequest2: Debug mode set to LOG, but debugLogFile property not set');
				if (file_put_contents($this->debugLogFile, "$msg\n\n", FILE_APPEND) == false)
					throw new Exception('webRequest2: Debug mode set to LOG, but debugLogFile cannot be written to');
		}
	}
	
	protected function execute($theHeader)
	{
		$this->beforeExecute();
		
		$this->debug('Execute: Starts');
		
		$this->clearHeaders();
		$this->rawResponse = '';
		$this->rawHeaders = '';
		$this->rawContent = '';
		$this->transferChunked = false;
		$this->chunkedLength = 0;
		$this->errorFlag = false;
		$this->resultCode = 0;
		$this->redirect = '';
		$this->caughtEarlyTerm = false;
		
		$keepTrying = true;
		while($keepTrying)
		{
			if ($this->socket)
			{
				fclose($this->socket);
				$this->socket = 0;
			}
				
			$sslStr = ($this->useSSL) ? 'ssl://' : '';
			$hostStr = ($this->proxy) ? "$sslStr{$this->proxy}" : "$sslStr{$this->domain}";
			$this->debug("Execute: HostStr=[{$hostStr}] Port:{$this->port}");
			
			$this->socket = @fsockopen($hostStr, $this->port, $errno, $errstr);
			if (!$this->socket)
			{
				$this->debug('Execute: Cannot open socket');
				$this->handleFailure();
				$this->afterExecute();
				return false;
			}
			
			if (!$this->ancient) 
			{ 
				$this->debug("Execute: PHP5 or greater, setting timeout of {$this->timeout}");
				stream_set_timeout($this->socket, $this->timeout); 
			}
			
			$this->debug('Execute: Sending request');
			$bytesToSend = strlen(trim($theHeader));
			if (($bytesSent = fwrite($this->socket, trim($theHeader))) <> $bytesToSend)
			{
				$this->debug("Execute: Failed - Only $bytesSent of $bytesToSend sent");
				$this->handleFailure();
				$this->afterExecute();
				return false;
			}
			$this->debug('Execute: Request Sent');
		
			$this->rawResponse = $this->getChunk();
			$keepTrying = false;
		
			// If we got zero and there's a proxy address then perhaps we pop the proxyRetry event...
			if (strlen($this->rawResponse) == 0)
			{
				if ($this->handleProxyRetry(&$keepTrying))
					continue;
			}
		}
		
		if (!$this->rawResponse)
		{
			// We did not receive anything, time to fail...
			$this->debug("Execute: Failed - Did not receive anything from remote host");
			$this->handleFailure();
			$this->afterExecute();
			return false;			
		}	
		

		preg_match('/^(.*)\r\n\r\n/smU', $this->rawResponse, $parts);
		$this->rawHeader = $parts[1];
		
		preg_match('/\r\n\r\n(.*)/sm', $this->rawResponse, $parts);
		$this->rawContent = $parts[1];

		$this->processHeaders();
		$this->reactToResultCode();

		// New 2008-02-13 - if there is an earlyTermStr and it was seen in the first packet sent,
		// then the caughtEarlyTerm flag will be set and we are ready to quit...
		if ($this->caughtEarlyTerm)
		{
			$this->debug('Execute: Successful on earlyTermStr');
			$this->debug('postProcess: Content length is ' . strlen($this->rawContent));
			$this->handleSuccess();
			$this->afterExecute();
			
			$this->debug('Execute: Completes');
			return $this->resultCode;
		}
		
		if ($this->chunkedTransfer)
		{
			$receivedSoFar = strlen($this->rawContent);
			while ($receivedSoFar < $this->chunkedLength)
			{
				$this->debug('Execute: Getting Chunked Block');
				$this->rawContent .= $this->getChunk();
				$receivedSoFar = strlen($this->rawContent);
				
				if ($this->errorFlag)
				{
					$this->debug('Execute: Terminating');
					$this->handleFailure();
					$this->afterExecute();
					return false;
				}
				
				// New 2008-02-13 - if there is an earlyTermStr and it was seen anywhere in the
				// total content then the caughtEarlyTerm flag will be set and we are ready to quit...
				if ($this->caughtEarlyTerm)
				{
					$this->debug('Execute: Breaking chunked retrieval because of earlyTermStr found in getChunk');
					break;
				}
				
				// More checking - if there is an early term string, then it MIGHT have come across
				// across two packets, in which case I need to check the entire content block thus far
				// so see if it's there...
				if ($this->earlyTermStr)
				{
					if (strpos($this->rawContent, $this->earlyTermStr))
					{
						$this->debug('Execute: Breaking chunked retrieval because of earlyTermStr found in content');
						break;
					}
				}
				
			}
		}
		
		$this->debug('Execute: Successful Retrieve');
		$this->debug('postProcess: Content length is ' . strlen($this->rawContent));
		$this->handleSuccess();
		$this->afterExecute();
		
		$this->debug('Execute: Completes');
		return $this->resultCode;

	}
	
	protected function getChunk()
	{
		$packets = array();
		$this->debug('GetChunk: Starts');
		while (!feof($this->socket))
		{
			$thisBuff = fread($this->socket, 65535);
			if (!strlen($thisBuff))
			{
				$reason = ($info['timedout']) ? 'Local Timeout' : 'Remote Timeout';
				$this->debug("getChunk: $reason");
				$this->errorFlag = true;
				break;
			}
			
			$this->debug('GetChunk: Received ' . strlen($thisBuff));
			$packets[] = $thisBuff;

			// New, 2008-02-13: If we see the existence of an earlyTermStr and it's found in what
			// we've received so far, close and and go home...
			if ($this->earlyTermStr)
			{
				$this->debug("GetChunk: Evaluating content against early term string");
				$buff = implode('', $packets);
				if (strpos($buff, $this->earlyTermStr))
				{
					$this->debug("GetChunk: Sees an early termination string '{$this->earlyTermStr}'");
					$this->caughtEarlyTerm = true;
					return $buff;
				}
			}
			
		}
		return implode('', $packets);
	}
	
	protected function handleFailure()
	{
		$this->debug("handleFailure()");
		if ($this->onFailure)
		{
			if (function_exists($this->onFailure))
			{
				$this->debug("Firing onFailure Event: {$this->onHandleFailure}(\$this)");
				call_user_func($this->onFailure, $this);
			}
		}
	}
	
	protected function handleProxyRetry(&$keepTrying)
	{
		$this->debug("handleProxyRetry()");
		if ($this->onProxyRetry)
		{
			if (function_exists($this->onProxyRetry))
			{
				$this->debug("Firing onProxyRetry Event: {$this->onProxyRetry}(\$this, '{$this->domain}', {$this->port}, [true])");
				call_user_func($this->onProxyRetry, $this, &$this->domain, &$this->port, &$keepTrying);
				$ktStr = ($keepTrying) ? '[true]' : '[false]';
				$this->debug("onProxyRetry returns: Host: {$this->domain}, Port: {$this->port}, keepTrying: $ktStr");
				$keepTrying = true;
				return true;
			} else {
				$this->debug("Execute Failure: onProxyRetry is set to function that does not exists, '{$this->onProxyRetry}'");
				$this->handleFailure();
				$this->afterExecute();
				return false;
			}
		}
	}
	
	protected function handleSuccess()
	{
		$this->debug("handleSuccess()");
		if ($this->onSuccess)
		{
			if (function_exists($this->onSuccess))
			{
				$this->debug("Firing onHandleSuccess Event: {$this->onHandleSuccess}(\$this)");
				call_user_func($this->onSuccess, $this);
			}
		}
	}
	
	protected function hasCookies() { return count($this->cookies); }
	
	protected function processHeaders()
	{
		$this->debug('processHeaders: Starts');
		$tempArr = explode("\r\n", $this->rawHeader);
		$ptr = 0;
		foreach($tempArr as $line)
		{
			if ($ptr == 0)
			{
				// Zeroth line - not a valid header, get the result code:
				preg_match('/([0-9]{3})/', $line, $parts);
				$this->resultCode = $parts[1];
				$ptr++;
				$this->debug("processHeaders: Result Code is {$this->resultCode}");
			} else {
				$parts = explode(': ', $line);
				$this->headers[$parts[0]] = $parts[1];
			}
		}
		
		$this->debug("processHeaders: Array Follows\n" . print_r($this->headers, true));
		
		$this->chunkedTransfer = preg_match('/: chunked/i', $this->rawHeader);
		if ($this->chunkedTransfer)
		{
			// OK - the actual content length is now going to be the first line of the content... grab it and ditch it...
			preg_match('/([^\r]+)\r\n(.*)/ms', $this->rawContent, $parts);
			$this->chunkedLength = hexdec($parts[1]);
			$this->rawContent = $parts[2];
			$this->debug("processHeaders: Chunked Transfer - expected length is {$this->chunkedLength}");
		}
		
		// If there are cookies, pull them into my cookie array...
		if ($this->headers['Set-Cookie'])
		{
			$temp = explode(';', $this->headers['Set-Cookie']);
			foreach($temp as $line)
			{
				$parts = explode('=', $line);
				$name = trim($parts[0]);
				$value = urldecode(trim($parts[1]));
				$this->cookies[$name] = $value;
			}
			$this->debug("processHeaders: Cookie Array Follows\n" . print_r($this->cookies, true));
		}
	}
	
	protected function reactToResultCode()
	{
		// This function should be extended in the future to handle more eventualities
		switch($this->resultCode)
		{
			case 200: 
				break;
				
			case 301:
			case 302:
				$this->redirect = $this->headers['Location'];
				$this->debug("reactToResultCode: Redirect To {$this->redirect}");
				break;
				
			case 404:
				break;
				
		}
	}
	

	// Protected functions, designed to be overridden:
	protected function afterExecute() 
	{
		$this->debug('Default afterExecute()');
		// Remember to call this function if you override it...
		if ($this->socket)
		{
			fclose($this->socket);
			$this->socket = 0;
		}
	}
	
	protected function beforeExecute() 
	{
		$this->debug('Default beforeExecute()');		
	}
	
	
	// Public functions
	function addGetParam($varName, $varValue) 
	{ 
		$this->getList[trim($varName)] = $varValue;
		$this->debug("Adding GET Param: [$varName] = [$varValue]");
	}
	
	function addPostParam($varName, $varValue, $type='text/plain') 
	{ 
		$varName = trim($varName);
		$this->postList[$varName]['content'] = $varValue;
		$this->postList[$varName]['type'] = $type;
		$this->debug("Adding POST Param: [$varName] = [$varValue]");
	}
	
	function clearCookies() { $this->cookies = array(); }
	
	function clearGetParams() { $this->getList = array(); }
	
	function clearHeaders() { $this->headers = array(); }
	
	function clearPostParams() { $this->postList = array(); }
	
	function dispatch()
	{
		if ($this->debugLogClearOnDispatch) { $this->clearDebugLog(); }
		
		$this->debug('Dispatch Starts');
		$this->debug("Method: {$this->method}");
		
		$this->buildURL();
		$req = $this->buildHeader();
		return $this->execute($req);
	}
	
	function getContent() { return $this->rawContent; }
	
	function getCookie($cookieName) { return $this->cookies[$cookieName]; }	
	
	function getCookies() { return $this->cookies; }	
	
	function getHeader($headerName) { return $this->headers[$headerName]; }	
	
	function getHeaders() { return $this->headers; }
	
	function getLinksAll()
	{
		$regex = <<<REGEX
~<[\s]*a[\s]+href[\s]*=[\s]*['"]([^'"]*)~i
REGEX;
		preg_match_all($regex, $this->getContent(), $matches);
		return $matches[1];
	}
	
	function getLinksExternal()
	{
		$out = array();
		$temp = $this->getLinksAll();
		foreach($temp as $link)
		{
			// If a link has http://{this->domain} in it or NO domain, then it is local...
			if (!preg_match("~^http[s]*://{$this->domain}~", $link) and (preg_match('/^http/', $link)))
				$out[] = $link;
		}
		return $out;
	}

	function getLinksInternal()
	{
		$out = array();
		$temp = $this->getLinksAll();
		foreach($temp as $link)
		{
			// If a link has http://{this->domain} in it or NO domain, then it is local...
			if (preg_match("~^http[s]*://{$this->domain}~", $link) or (!preg_match('/^http/', $link)))
			{
				// OK - it is an internal link, but let's do a little bit to it to help out the caller...
	
				// If the URL has http:// and no port, then kill the front end of it:
				if (substr(strtolower($link), 0, 5) == 'http:')
				{
					if (!preg_match('~:[0-9]{1,6}/~', $link))
					{
						preg_match("~{$this->domain}(.*)~", $link, $matches);
						$link = $matches[1];
					}
				}
				
				// If it is a relative link, then we need to take the current directory and add it
				// on to the front end so that the link is absolute:
				if (substr($link, 0, 1) <> '/')
				{
					// Get the current directory from the original url...
					preg_match("~{$this->domain}(/.*/)[^/]*~", $link, $matches);
					if (!$matches)
					{
						// There was no directory - the last send was the root, and the URL is
						// relative to the root...
						$link = "/$link";
					} else {
						$link = "{$matches[1]}$link";
					}
				}
				
				// Kill simple on-page positioning links...
				if (substr($link, 0, 1) == '#')
					continue;
				
				$out[] = $link;
			}
		}
		return $out;
	}

	function getRawHeader() { return $this->rawHeader; }
	function getRawResponse() { return $this->rawResponse; }

	function reset() 
	{
		$this->rawPostData = '';
		$this->url = '';
		$this->domain = '';
		$this->port = 80;
		$this->method = 'GET';
		$this->getArray['__count'] = -1;
		$this->postArray['__count'] = -1;
		$this->rawResponse = '';
		$this->rawHeader = '';
		$this->rawContent = '';
		
		$this->cookieStr = '';
		$this->postStr = '';
		$this->manualPostContent = '';

		$this->clearCookies();
		$this->clearGetParams();
		$this->clearHeaders();
		$this->clearPostParams();
	}
	
	function setCookie($cookieName, $cookieValue) { $this->cookies[$cookieName] = $cookieValue; }

	function simpleGet($completeURL)
	{
		$this->debug("simpleGet: Starts with [$completeURL]");
		
		if (substr(strtolower($completeURL), 0, 5) == 'https')
		{
			$this->debug("simpleGet: Using SSL");
			$this->useSSL = true;
			$this->port = 443;
			preg_match('~//(.*)~', $completeURL, $parts);
			$completeURL = $parts[1];
		} else if (substr(strtolower($completeURL), 0, 4) == 'http') {
			$this->useSSL = false;
			$this->port = 80;
			preg_match('~//(.*)~', $completeURL, $parts);
			$completeURL = $parts[1];
		}

		preg_match('~([^/]+)(.*)~', $completeURL, $parts);
		$this->domain = $parts[1];
		$this->url = $parts[2];
		$this->finalURL = $this->url;
		
		$req = $this->buildHeader();
		if ($this->execute($req)) { return $this->rawContent; }
		
		$this->debug('simpleGet Failed');
		return false;
		
	}
}

?>