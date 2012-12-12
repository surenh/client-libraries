# Returns the SHA256 hash which is used to sign the API call
#
# parameters - the Hash containing parameters to the API call, may be empty but not nil

function createUrlParameters($parameters=array()) {
  // Get the parameters in order
  $updatedParameters = array_merge(array(
    "ts" => time(),
    "apisecret" => 'SAMPLE_SECRET_KEY',
    "apikey" => 'SAMPLE_API_KEY'
  ), $parameters);
  ksort($updatedParameters);
    
  // Now lets create the string for hashing.
  // don't use http_build_query, because it not escaped for hashing.
  // so instead, brute force it, will work for 1 level arrays
  $sigParameterString = "";
  foreach ($updatedParameters as $key => $value) {
    $sigParameterString .= "&" . $key . "=" . $value;
  }
  $sigParameterString = substr($sigParameterString, 1);
    
  // Now create the signature hash, add it to the parameter string
  // Remove the apisecret from the parameters
  $updatedParameters['sig'] = hash('sha256', $sigParameterString);
  unset($updatedParameters['apisecret']);
    
  // Return
  return http_build_query($updatedParameters);
}

