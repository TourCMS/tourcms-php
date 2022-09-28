<?php
/*
Copyright (c) 2010-2019 Travel UCD

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
# Version: 3.10.0

namespace TourCMS\Utils;

use \SimpleXMLElement;

class TourCMS {

	const HTTP_VERB_POST = 'POST';

	// General settings
	protected $base_url = "https://api.tourcms.com";
	protected $marketp_id = 0;
	protected $private_key = "";
	protected $result_type = "";
	protected $timeout = 0;
	protected $last_response_headers = array();

	/**
	 * __construct
	 *
	 * @author Paul Slugocki
	 * @param $mp Marketplace ID
	 * @param $k API Private Key
	 * @param $res Result type, defaults to raw
	 * @param $to Timeout, default 0
	 */
	public function __construct($mp, $k, $res = "raw", $to = 0) {
		$this->marketp_id = $mp;
		$this->private_key = $k;
		$this->result_type = $res;
		$this->timeout = $to;
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
		curl_setopt($ch, CURLOPT_TIMEOUT, (is_int($this->timeout) && $this->timeout > 0) ? $this->timeout : 0 );
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_HEADER, true);

		/*
			Windows users having trouble connecting via SSL?
			Download the CA bundle from: http://curl.haxx.se/docs/caextract.html
			Finally uncomment the following line and point it to the downloaded file
		*/
		// curl_setopt($ch, CURLOPT_CAINFO, "c:/path/to/ca-bundle.crt");

		if($verb == "POST") {
			curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, 'POST' );
				if(!is_null($post_data))
					curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data->asXML());
		}

		// Callback function to populate the response headers on curl_exec
		$api_response_headers = array();
		curl_setopt($ch, CURLOPT_HEADERFUNCTION,
			function($ch, $header) use (&$api_response_headers)
			{
				$len = strlen($header);
				$header = explode(':', $header, 2);
				if (count($header) < 2) // ignore invalid headers
					return $len;

				$name = strtolower(trim($header[0]));
				$api_response_headers[$name] = trim($header[1]);

				return $len;
			}
		);

		$response = curl_exec($ch);

		$this->last_response_headers = $api_response_headers;

		$header_size = curl_getinfo( $ch, CURLINFO_HEADER_SIZE );
		$result = substr( $response, $header_size );

		// Check whether we need to return raw XML or
		// convert to SimpleXML first
		if($this->result_type == "simplexml")
			$result = simplexml_load_string($result);

		return($result);
	}

	/**
	 * get_base_url
	 *
	 * @author Paul Slugocki
	 * @return String
	 */
	public function get_base_url() {
		return $this->base_url;
	}

	/**
	 * set_base_url
	 *
	 * @author Paul Slugocki
	 * @param $url New base url
	 * @return Boolean
	 */
	public function set_base_url($url) {
		$this->base_url = $url;
		return true;
	}

	# Get last response headers

	public function get_last_response_headers() {
		return $this->last_response_headers;
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

	# Test environment

	public function test_environment($channel = 0) {
		include('test.php');
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

		$params = $this->validateParams($params);

		if($channel==0)
			return($this->request('/p/tours/search.xml'.$params));
		else
			return($this->request('/c/tours/search.xml'.$params, $channel));
	}

	public function search_hotels_range($params = "", $tour = "", $channel = 0) {

		$params = $this->validateParams($params);
		
		if (!empty($tour)) {
			
			if (empty($params)) {
                		$params = '?single_tour_id=';
            		} else {
		                $params .= "&single_tour_id=";
            		}
			
			$params .= $tour;
			
		}

		if($channel==0)
			return($this->request('/p/hotels/search_range.xml'.$params));
		else
			return($this->request('/c/hotels/search_range.xml'.$params, $channel));
	}

	public function search_hotels_specific($params = "", $tour = "", $channel = 0) {
		$params = $this->validateParams($params);

		if (!empty($tour)) {
            		
			if (empty($params)) {
                		$params = '?single_tour_id=';
            		} else { 
				$params .= "&single_tour_id=";
			}
			
			$params .= $tour;
			
		}

		if($channel==0)
			return($this->request('/p/hotels/search_avail.xml'.$params));
		else
			return($this->request('/c/hotels/search_avail.xml'.$params, $channel));
	}

	public function list_product_filters($channel = 0) {
			return($this->request('/c/tours/filters.xml', $channel));
	}

	public function update_tour($tour_data, $channel) {
		return($this->request('/c/tour/update.xml', $channel, "POST", $tour_data));
	}

	public function update_tour_url($tour, $channel, $tour_url) {

		$url_data = new SimpleXMLElement('<tour />');
		$url_data->addChild('tour_id', $tour);
		$url_data->addChild('tour_url', $tour_url);

		return($this->update_tour($url_data, $channel));
	}

	public function list_tours($channel = 0, $params = "") {
		$params = $this->validateParams($params);
		if($channel==0)
			return($this->request('/p/tours/list.xml'.$params));
		else
			return($this->request('/c/tours/list.xml'.$params, $channel));
	}

	public function list_tour_images($channel = 0, $params = "")
	{
		$params = $this->validateParams($params);
		if($channel==0)
			return($this->request('/p/tours/images/list.xml'.$params));
		else
			return($this->request('/c/tours/images/list.xml'.$params, $channel));
	}

	public function list_tour_locations($channel = 0, $params = "")
	{
		$params = $this->validateParams($params);
		if($channel==0)
			return($this->request('/p/tours/locations.xml'.$params));
		else
			return($this->request('/c/tours/locations.xml'.$params, $channel));
	}

	public function delete_tour($tour, $channel)
	{
		$url = '/c/tour/delete.xml?id='.$tour;
		return($this->request($url, $channel, self::HTTP_VERB_POST));
	}

	public function show_tour($tour, $channel, $params = false)
	{
		$url = '/c/tour/show.xml?id='.$tour;

		/*

			Third param for show tour could be:

			- bool: show_options=1 / 0 (deprecated)

			- string: params

		*/

			if(is_string($params)) {

				$url .= "&" . $params;

			} else {

				if($params)
					$url .= "&show_options=1";

			}

		if((int)$tour > 0) {
			return($this->request($url, $channel));
		}
	}


	public function check_tour_availability($params, $tour, $channel)
	{
		if (!empty($params)) $params = "&" . $params;
		return ($this->request('/c/tour/datesprices/checkavail.xml?id='.$tour.$params, $channel));
	}

	public function show_tour_datesanddeals($tour, $channel, $qs = "")
	{
		if (!empty($qs)) $qs = "&" . $qs;
		return($this->request('/c/tour/datesprices/datesndeals/search.xml?id='.$tour.$qs, $channel));
	}


	public function show_tour_departures($tour, $channel, $qs = "")
	{
		if (!empty($qs)) $qs = "&" . $qs;
		return($this->request('/c/tour/datesprices/dep/show.xml?id='.$tour.$qs, $channel));
	}

	public function show_tour_freesale($tour, $channel)
	{
		return($this->request('/c/tour/datesprices/freesale/show.xml?id='.$tour, $channel));
	}

	/*
		Raw departure methods
	*/

	public function search_raw_departures($tour, $channel, $qs = "")
	{
		if (!empty($qs)) $qs = "&" . $qs;
		return($this->request('/c/tour/datesprices/dep/manage/search.xml?id='.$tour.$qs, $channel));
	}

	public function show_departure($departure, $tour, $channel)
	{
		return($this->request('/c/tour/datesprices/dep/manage/show.xml?id='.$tour.'&departure_id='.$departure, $channel));
	}

	public function create_departure($departure_data, $channel)
	{
		return($this->request('/c/tour/datesprices/dep/manage/new.xml', $channel, "POST", $departure_data));
	}

	public function update_departure($departure_data, $channel)
	{
		return($this->request('/c/tour/datesprices/dep/manage/update.xml', $channel, "POST", $departure_data));
	}

	public function delete_departure($departure, $tour, $channel)
	{
		return($this->request('/c/tour/datesprices/dep/manage/delete.xml?id='.$tour.'&departure_id='.$departure, $channel, "POST"));
	}

	/*
		Promo code
	*/

	public function show_promo($promo, $channel)
	{
		return($this->request('/c/promo/show.xml?promo_code='.$promo, $channel));
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
		$params = $this->validateParams($params);
		if($channel==0)
			return($this->request('/p/bookings/search.xml'.$params));
		else
			return($this->request('/c/bookings/search.xml'.$params, $channel));
	}

	public function list_bookings($params = "", $channel = 0)
	{
		$params = $this->validateParams($params);
        if($channel==0)
            return($this->request('/p/bookings/list.xml'.$params));
        else
            return($this->request('/c/bookings/list.xml'.$params, $channel));
	}

	public function show_booking($booking, $channel) {
		return($this->request('/c/booking/show.xml?booking_id='.$booking, $channel));
	}

	public function search_voucher($voucher_data = null, $channel = 0) {

		if($voucher_data == null) {
			$voucher_data = new SimpleXMLElement('<voucher />');
			$voucher_data->addChild('barcode_data', '');
		}

		if($channel == 0) {
			return($this->request('/p/voucher/search.xml', $channel, 'POST', $voucher_data));
		} else {
			return($this->request('/c/voucher/search.xml', $channel, 'POST', $voucher_data));
		}
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

	public function log_failed_payment($payment_data, $channel)
	{
		return($this->request('/c/booking/payment/fail.xml', $channel, "POST", $payment_data));
	}

	public function spreedly_create_payment($payment_data, $channel)
	{
		return($this->request('/c/booking/payment/spreedly/new.xml', $channel, "POST", $payment_data));
	}

	public function spreedly_complete_payment($transaction_id, $channel)
	{
		return($this->request('/c/booking/gatewaytransaction/spreedlycomplete.xml?id=' . $transaction_id, $channel, 'POST'));
	}

	public function cancel_booking($booking_data, $channel)
	{
		return($this->request('/c/booking/cancel.xml', $channel, "POST", $booking_data));
	}

	public function delete_booking($booking, $channel)
	{
		return($this->request('/c/booking/delete.xml?booking_id='.$booking, $channel, "POST"));
	}

	public function check_option_availability($booking, $tour_component_id, $channel){
		return ($this->request('/c/booking/options/checkavail.xml?booking_id='.$booking.'&tour_component_id='.$tour_component_id, $channel));
	}

	public function booking_add_component($component_data, $channel){
		return($this->request('/c/booking/component/new.xml', $channel, "POST", $component_data));
	}

	public function booking_remove_component($component_data, $channel){
		return($this->request('/c/booking/component/delete.xml', $channel, "POST", $component_data));
	}

	public function booking_update_component($component_data, $channel){
		return($this->request('/c/booking/component/update.xml', $channel, "POST", $component_data));
	}

	public function add_note_to_booking($booking, $channel, $text, $note_type) {

		$booking_data = new SimpleXMLElement('<booking />');
		$booking_data->addChild('booking_id', $booking);
		$note = $booking_data->addChild('note');
		$note->addChild('text', $text);
		$note->addChild('type', $note_type);

		return($this->request('/c/booking/note/new.xml', $channel, 'POST', $booking_data));
	}

	public function send_booking_email($booking_data, $channel){
			return($this->request('/c/booking/email/send.xml', $channel, "POST", $booking_data));
	}

	public function redeem_voucher($voucher_data, $channel = 0) {
		return($this->request('/c/voucher/redeem.xml', $channel, 'POST', $voucher_data));
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
		$params = $this->validateParams($params);
		if($channel==0)
			return($this->request('/p/enquiries/search.xml'.$params));
		else
			return($this->request('/c/enquiries/search.xml'.$params, $channel));
	}

	public function show_enquiry($enquiry, $channel)
	{
		return($this->request('/c/enquiry/show.xml?enquiry_id='.$enquiry, $channel));
	}

	public function show_customer($customer, $channel)
	{
		return($this->request('/c/customer/show.xml?customer_id='.$customer, $channel));
	}

	public function check_customer_login($customer, $password, $channel) {
		return($this->request('/c/customers/login_search.xml?customer_username='.$customer.'&customer_password='.$password, $channel));
	}

	# Agents
	public function search_agents($params, $channel)
	{
		$params = $this->validateParams($params);
		return($this->request('/c/agents/search.xml'.$params, $channel));
	}

	public function start_new_agent_login($params, $channel)
	{
		return($this->request('/c/start_agent_login.xml', $channel, "POST", $params));
	}

	public function retrieve_agent_booking_key($private_token, $channel)
	{
		return($this->request('/c/retrieve_agent_booking_key.xml?k='.$private_token, $channel));
  	}
	
	public function update_agent($update_data, $channel)
	{
		return ($this->request('/c/agents/update.xml', $channel, "POST", $update_data));
	}

	# Payments
	public function list_payments($params, $channel)
	{
		$params = $this->validateParams($params);
		return($this->request('/c/booking/payment/list.xml'.$params, $channel));
	}
  
  	public function payworks_booking_payment_new($payment, $channel)
  	{
  		return ($this->request('/c/booking/payment/payworks/new.xml', $channel, "POST", $payment));
  	}

	# Staff members
	public function list_staff_members($channel)
	{
		return($this->request('/c/staff/list.xml', $channel));
	}
  
	# Internal supplier methods
	public function show_supplier($supplier, $channel)
	{
		return($this->request('/c/supplier/show.xml?supplier_id='.$supplier, $channel));
	}

	# Used for validating webhook signatures
	public function validate_xml_hash($xml) {

		return $this->generate_xml_hash($xml) == $xml->signed->hash;

	}

	public function generate_xml_hash($xml) {

		$algorithm = $xml->signed->algorithm;

		$fields = explode(" ", $xml->signed->hash_fields);

		foreach($fields as $field) {

			$xpath_result = $xml->xpath($field);

			foreach($xpath_result as $result) {
				$values[] = (string)$result[0];
			}
		}

		$string_to_hash = implode("|", $values);

		$hash = hash_hmac($algorithm, $string_to_hash, $this->private_key, FALSE);

		return $hash;

	}

	# CRUD Pickup points
	public function list_pickups($query_string, $channel)
	{
		$query_string = $this->validateParams($query_string);
		return ($this->request('/c/pickups/list.xml' . $query_string, $channel));
	}

	public function create_pickup($pickup_data, $channel)
	{
		return ($this->request('/c/pickups/new.xml', $channel, "POST", $pickup_data));
	}

	public function update_pickup($pickup_data, $channel)
	{
		return ($this->request('/c/pickups/update.xml', $channel, "POST", $pickup_data));
	}

	public function delete_pickup($pickup_data, $channel)
	{
		return ($this->request('/c/pickups/delete.xml', $channel, "POST", $pickup_data));
	}

	protected function validateParams($params)
	{
		if (empty($params) || !is_string($params)) {
			return '';
		}

		if (!empty($params) && substr($params, 0, 1) !== '?') {
			$params = '?'.$params;
		}

		return $params;
	}

}

?>
