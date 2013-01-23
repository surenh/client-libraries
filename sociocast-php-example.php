# Returns the GET query string included the SHA256 hash which is used to sign the API call
#
# parameters - the Hash containing parameters to the API call, may be empty but not nil

function createGetRequestQueryString($parameters=array()) {
  
  //Sort the paramters in alphabetical order
  $sigParams = array(
    "apikey" => 'YOUR-KEY',
    "apisecret" => 'YOUR-SECRET',
    "ts" => time()
  );
  ksort($sigParams); # can avoid sorting by keeping the params in order above but doing for safety
    
  // Create the string for hashing. Do not use http_build_query, because hashing cannot be escaped.
  $sigParameterString = "";
  foreach ($sigParams as $key => $value) {
    $sigParameterString .= "&" . $key . "=" . $value;
  }
  $sigParameterString = substr($sigParameterString, 1);
    
  // Now create the signature hash and add it to the parameter string.
  // Also remove the apisecret from the parameters.
  $sigParams['sig'] = hash('sha256', $sigParameterString);
  unset($sigParams['apisecret']);

  $paramString = array_merge($sigParams, $parameters);
  
  // Return
  return http_build_query($paramString);
}
