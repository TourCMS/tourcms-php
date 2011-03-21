<?php
/*
Copyright (c) 2010 Travel UCD

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.
*/

# TourCMS: PHP wrapper class for TourCMS Rest API
# Version: 1.1
# Author: Paul Slugocki

class TourCMS {

	// General settings
	protected $base_url = "https://api.tourcms.com";
	protected $marketp_id = 0;
	protected $private_key = "";
	protected $result_type = "";
	
	// API config
	protected $api = array();
	
	/**
	 * __construct
	 *
	 * @author Paul Slugocki
	 * @param $mp Marketplace ID
	 * @param $k API Private Key
	 * @param $res Result type, defaults to raw
	 */
	public function __construct($mp, $k, $res = "raw") {
		$this->marketp_id = $mp;
		$this->private_key = $k;
		$this->result_type = $res;
	}
	
	/**
	 * request
	 *
	 * @author Paul Slugocki
	 * @param $path API path to call
	 * @param $channel Channel ID, defaults to zero
	 * @param $verb HTTP Verb, defaults to GET
	 * @return String or SimpleXML
	 */
	protected function request($path, $channel = 0, $verb = 'GET') {
		// Prepare the URL we are sending to
		$url = $this->base_url.$path;
		// We need a signature for the header
		
		$outbound_time = time();
		$signature = $this->generate_signature($path, $verb, $channel, $outbound_time);

		// Build headers
		$headers = array("Content-type: text/xml;charset=\"utf-8\"",
				 "Date: ".gmdate('D, d M Y H:i:s \G\M\T', $outbound_time),
				 "Authorization: TourCMS $channel:$this->marketp_id:$signature");
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_TIMEOUT, 30 );
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_HEADER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		
		$response = curl_exec($ch);
		
		$header_size = curl_getinfo( $ch, CURLINFO_HEADER_SIZE );
		$result = substr( $response, $header_size );
		
		// Check whether we need to return raw XML or
		// convert to SimpleXML first
		if($this->result_type == "simplexml")
			$result = simplexml_load_string($result);

		
		return($result);
	}
	
	/**
	 * generate_signature
	 *
	 * @author Paul Slugocki
	 * @param $path API Path
	 * @param $verb HTTP Verb
	 * @param $channel Channel ID
	 * @return String
	 */
	protected function generate_signature($path, $verb, $channel, $outbound_time) {
		
		$string_to_sign = trim($channel."/".$this->marketp_id."/".$verb."/".$outbound_time.$path);
		
		$signature = rawurlencode(base64_encode((hash_hmac("sha256", utf8_encode($string_to_sign), $this->private_key, TRUE ))));
		
		return $signature;
	}
	
	# API methods
	public function api_rate_limit_status($channel = 0) {
		return($this->request('/api/rate_limit_status.xml', $channel));
	}
	
	# Channel methods
	public function list_channels() {
		return($this->request('/p/channels/list.xml'));
	}
	
	public function show_channel($channel) {
		return($this->request('/c/channel/show.xml', $channel));
	}
	
	# Tour methods
	public function search_tours($params = "", $channel = 0) {
		if($channel==0) 
			return($this->request('/p/tours/search.xml?'.$params));
		else
			return($this->request('/c/tours/search.xml?'.$params, $channel));		
	}
	
	# Tour methods
	
	public function search_hotels_range($params = "", $tour = "", $channel = 0) {
		if($channel==0) 
			return($this->request('/p/hotels/search_range.xml?'.$params."&single_tour_id=".$tour));
		else
			return($this->request('/c/hotels/search_range.xml?'.$params."&single_tour_id=".$tour, $channel));
	}

	public function search_hotels_specific($params = "", $tour = "", $channel = 0) {
		if($channel==0) 
			return($this->request('/p/hotels/search_avail.xml?'.$params."&single_tour_id=".$tour));
		else
			return($this->request('/c/hotels/search_avail.xml?'.$params."&single_tour_id=".$tour, $channel));
	}
	
	public function list_tours($channel = 0) {
		if($channel==0) 
			return($this->request('/p/tours/list.xml'));
		else
			return($this->request('/c/tours/list.xml', $channel));
	}
	
	public function list_tour_images($channel = 0) {
		if($channel==0) 
			return($this->request('/p/tours/images/list.xml'));
		else
			return($this->request('/c/tours/images/list.xml', $channel));	
	}
	
	public function show_tour($tour, $channel) {
		return($this->request('/c/tour/show.xml?id='.$tour, $channel));		
	}
	
	public function show_tour_departures($tour, $channel)
	{
		return($this->request('/c/tour/datesprices/dep/show.xml?id='.$tour, $channel));	
	}
	
	public function show_tour_freesale($tour, $channel)
	{
		return($this->request('/c/tour/datesprices/freesale/show.xml?id='.$tour, $channel));	
	}
	
	
}

?>