<?php
/*
Copyright (c) 2010-2011 Travel UCD

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
# Version: 1.3
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
	public function request($path, $channel = 0, $verb = 'GET', $post_data = null) {
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
		
		if($verb == "POST") {
			curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, 'POST' );
				if(!is_null($post_data))
					curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data->asXML());
		}
		
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
	
	# API methods (Housekeeping)
	
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
	
	public function channel_performance($channel = 0) {
		if($channel==0) 
			return($this->request('/p/channels/performance.xml'));
		else
			return($this->request('/c/channel/performance.xml', $channel));
	}
	
	# Tour/Hotel methods
	
	public function search_tours($params = "", $channel = 0) {
		if($channel==0) 
			return($this->request('/p/tours/search.xml?'.$params));
		else
			return($this->request('/c/tours/search.xml?'.$params, $channel));		
	}
	
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
	
	public function update_tour($tour_data, $channel) {
		return($this->request('/c/tour/update.xml', $channel, "POST", $tour_data));
	}
	
	public function update_tour_url($tour, $channel, $tour_url) {
		// Create a SimpleXMLElement to hold the new url 
		$url_data = new SimpleXMLElement('<tour />'); 
		$url_data->addChild('tour_id', $tour); 
		$url_data->addChild('tour_url', $tour_url); 
		
		return($this->update_tour($url_data, $channel));
	}
	
	public function list_tours($channel = 0) {
		if($channel==0) 
			return($this->request('/p/tours/list.xml'));
		else
			return($this->request('/c/tours/list.xml', $channel));
	}
	
	public function list_tour_images($channel = 0) 
	{
		if($channel==0) 
			return($this->request('/p/tours/images/list.xml'));
		else
			return($this->request('/c/tours/images/list.xml', $channel));	
	}
	
	public function show_tour($tour, $channel) 
	{
		return($this->request('/c/tour/show.xml?id='.$tour, $channel));		
	}
	
	public function check_tour_availability($params, $tour, $channel)
	{
		return ($this->request('/c/tour/datesprices/checkavail.xml?id='.$tour."&".$params, $channel));
	}
	
	public function show_tour_datesanddeals($tour, $channel, $qs = "")
	{
		return($this->request('/c/tour/datesprices/datesndeals/search.xml?id='.$tour.'&'.$qs, $channel));	
	}

	
	public function show_tour_departures($tour, $channel)
	{
		return($this->request('/c/tour/datesprices/dep/show.xml?id='.$tour, $channel));	
	}
	
	public function show_tour_freesale($tour, $channel)
	{
		return($this->request('/c/tour/datesprices/freesale/show.xml?id='.$tour, $channel));	
	}
	
	# Booking methods
	
	/* 
		Making bookings
	*/

	public function get_booking_redirect_url($url_data, $channel)
	{
		return($this->request('/c/booking/new/get_redirect_url.xml', $channel, "POST", $url_data));
	}
	
	public function start_new_booking($booking_data, $channel)
	{
		return($this->request('/c/booking/new/start.xml', $channel, "POST", $booking_data));
	}
	
	public function commit_new_booking($booking_data, $channel)
	{
		return($this->request('/c/booking/new/commit.xml', $channel, "POST", $booking_data));
	}
	
	/*
		Retrieving bookings
	*/
	
	public function search_bookings($params = "", $channel = 0) 
	{
		if($channel==0) 
			return($this->request('/p/bookings/search.xml?'.$params));
		else
			return($this->request('/c/bookings/search.xml?'.$params, $channel));
	}
	
	public function show_booking($booking, $channel) {
		return($this->request('/c/booking/show.xml?booking_id='.$booking, $channel));
	}
	
	/*
		Updating bookings
	*/
	
	public function update_booking($booking_data, $channel)
	{
		return($this->request('/c/booking/update.xml', $channel, "POST", $booking_data));
	}
	
	public function create_payment($payment_data, $channel)
	{
		return($this->request('/c/booking/payment/new.xml', $channel, "POST", $payment_data));
	}
	
	# Enquiry and customer methods
	
	public function create_enquiry($enquiry_data, $channel)
	{
		return($this->request('/c/enquiry/new.xml', $channel, "POST", $enquiry_data));
	}
	
	public function update_customer($customer_data, $channel)
	{
		return($this->request('/c/customer/update.xml', $channel, "POST", $customer_data));
	}
	
	public function search_enquiries($params = "", $channel = 0) {
		if($channel==0) 
			return($this->request('/p/enquiries/search.xml?'.$params));
		else
			return($this->request('/c/enquiries/search.xml?'.$params, $channel));
	}
	
	public function show_enquiry($enquiry, $channel) {
		return($this->request('/c/enquiry/show.xml?enquiry_id='.$enquiry, $channel));
	}
	
	public function show_customer($customer, $channel) {
		return($this->request('/c/customer/show.xml?customer_id='.$customer, $channel));
	}
	
	public function check_customer_login($customer, $password, $channel) {
		return($this->request('/c/customers/login_search.xml?customer_username='.$customer.'&customer_password='.$password, $channel));
	}
	
	# Internal supplier methods
	public function show_supplier($supplier, $channel) {
		return($this->request('/c/supplier/show.xml?supplier_id='.$supplier, $channel));
	}
}

?>