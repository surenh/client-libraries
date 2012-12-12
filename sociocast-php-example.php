# Returns the SHA256 hash which is used to sign the API call
#
# parameters - the Hash containing parameters to the API call, may be empty but not nil

function createUrlParameters($parameters=array()) {
  
  //Sort the paramters in alphabetical order
  $updatedParameters = array_merge(array(
    "ts" => time(),
    "apisecret" => 'YOUR_SECRET_KEY',
    "apikey" => 'YOUR_API_KEY'
  ), $parameters);
  ksort($updatedParameters);
    
  // Create the string for hashing. Do not use http_build_query, because hashing cannot be escaped.
  $sigParameterString = "";
  foreach ($updatedParameters as $key => $value) {
    $sigParameterString .= "&" . $key . "=" . $value;
  }
  $sigParameterString = substr($sigParameterString, 1);
    
  // Now create the signature hash and add it to the parameter string.
  // Also remove the apisecret from the parameters.
  $updatedParameters['sig'] = hash('sha256', $sigParameterString);
  unset($updatedParameters['apisecret']);
    
  // Return
  return http_build_query($updatedParameters);
}

