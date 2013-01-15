#!/usr/bin/ruby -w

require 'optparse'
require 'digest'
require 'json'
require 'net/http'
require 'uri'
require 'erb'

# Returns the SHA256 hash which is used to sign the API call
#
# apikey - unique API key assigned to the client
# apisecret - shared secret assigned to the client
# timestamp - number of seconds since the Unix Epoch at which the request was made
# parameters - the Hash containing parameters to the API call
def calculate_hash(apikey, apisecret, timestamp, parameters)
        # gather values included in hash, sort them alphabetically
	sig_params = Hash.new
	sig_params[:apikey] = apikey
	sig_params[:apisecret] = apisecret
	sig_params[:ts] = timestamp
	
	# stringify the values to be hashed - e.g. key1=value1&key2=value2&key3=value3
	string_to_hash = ''
	sig_params.sort.map do |key,value|
	    string_to_hash = string_to_hash + key.to_s + "=" + value.to_s + "&"
	end
	string_to_hash = string_to_hash.chomp('&') # Ensuring that the last '&' is removed.
	
	# compute the SHA256 hash and return as a string
	Digest::SHA256.hexdigest(string_to_hash).to_s
end

# API call to create a content profile
#
# apikey - unique API key assigned to the client
# apisecret - shared secret assigned to the client
# url - the url whose profile you need to fetch
def get_profile_for_url(apikey, apisecret, url)
	# Generate a timestamp for the request
	timestamp = Time.now.to_i # Current time in seconds since epoch
	
	# Store the url in a params hash to calculate the signature
	parameters = {}
	parameters[:url] = url
	
	# Calculate the signature for this call - used for authentication by the api server
	sig = calculate_hash(apikey, apisecret, timestamp, parameters)
	
	# Construct the HTTP request
	hostname = @server   #'int-api.sociocast.com'
	port = @port
	ts_string = 'ts=' + timestamp.to_s
	apikey_string = 'apikey=' + apikey.to_s
	sig_string = 'sig=' + sig
	
	puts "Trying to build GET request string for URL: #{url}"
        # Encode the URL for safe passage and ensure that the calculated signature is appended to the call
        # along with the time stamp.
	request_path = '/content/profile?url=' + 
	               ERB::Util.url_encode(url) + '&' + apikey_string + '&' + ts_string + '&' + sig_string 
	
	# Send the request
	headers = { 'Content-Type' => 'application/json; charset=utf-8' }
	request = Net::HTTP::Get.new(request_path, initheader = headers)
	response = Net::HTTP.new(hostname, port).start{|http| http.request(request)}

	puts "Response #{response.code} #{response.message}: #{response.body}"
end


# Main code
@server = 'api-test.sociocast.com'
@port   = 80
url     = ARGV[0] # The URL that you need to get the content profile for. 
apikey  = #YOUR_API_KEY
secret  = #YOUR_SECRET

get_profile_for_url(apikey,secret,url)
