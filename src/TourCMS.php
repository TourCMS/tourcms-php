<?php
/*
Copyright (c) 2010 - 2024 Travel UCD

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
# Version: 4.10.0

namespace TourCMS\Utils;

use \SimpleXMLElement;

class TourCMS {

	// ENDPOINTS CONST
	const PATH_API_TOUR_GEOS_CREATE = "/api/tours/geos/create.xml";
	const PATH_API_TOUR_GEOS_UPDATE = "/api/tours/geos/update.xml";
	const PATH_API_TOUR_GEOS_DELETE = "/api/tours/geos/delete.xml";
	const PATH_API_TOUR_PICKUP_ROUTES_SHOW = "/api/tours/pickup/routes/show.xml";
	const PATH_API_TOUR_PICKUP_ROUTES_UPDATE = "/api/tours/pickup/routes/update.xml";
	const PATH_API_TOUR_PICKUP_ROUTES_ADD_PICKUP = "/api/tours/pickup/routes/pickup_add.xml";
	const PATH_API_TOUR_PICKUP_ROUTES_UPDATE_PICKUP = "/api/tours/pickup/routes/pickup_update.xml";
	const PATH_API_TOUR_PICKUP_ROUTES_DELETE_PICKUP = "/api/tours/pickup/routes/pickup_delete.xml";
	const PATH_API_ACCOUNT_CUSTOM_FIELDS_GET = "/api/account/custom_fields/get.xml";
	const PATH_API_TOUR_FACETS_GET = "/api/tours/importer/get_tour_facets.xml";
	const PATH_API_LIST_TOURS_GET = "/api/tours/importer/get_tour_list.xml";
	const PATH_API_IMPORT_TOURS_STATUS = "/api/tours/importer/get_import_tours_status.xml";
	const PATH_API_LIST_TOUR_BOOKINGS_RESTRICTIONS = "/api/tours/restrictions/list_tour_bookings_restrictions.xml";
	const PATH_API_AGENT_PROFILE_GET = "/api/agent/profile/get.xml";
	const PATH_API_AGENT_PROFILE_UPDATE = "/api/agent/profile/update.xml";
	const PATH_API_TOURS_SEARCH_CRITERIA_GET = "/api/tours/search_criteria/get.xml";

	// HTTP VERBS CONST
	const HTTP_VERB_POST = 'POST';
	const HTTP_VERB_GET  = 'GET';

	// General settings
	protected $base_url = "https://api.tourcms.com";
	protected $marketp_id = 0;
	protected $private_key = "";
	protected $result_type = "";
	protected $timeout = 0;
	protected $last_request_headers = array();
	protected $last_response_headers = array();
	protected $user_agent = "TourCMS PHP Wrapper v4.1.1";
	protected $prepend_caller_to_user_agent = true;
	protected array $headers = [];
	protected array $permanent_headers = [];

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
		$this->add_header("Content-type", "text/xml;charset=\"utf-8\"");
		$this->add_header("Date", gmdate('D, d M Y H:i:s \G\M\T', $outbound_time));
		$this->add_header("Authorization", "TourCMS $channel:$this->marketp_id:$signature");
		// Add user-agent to headers array
		if (!empty($this->user_agent)) {
			$finalUserAgent = $this->prepend_caller_to_user_agent ? $this->user_agent." (".$this->marketp_id."_".$channel.")" : $this->user_agent;
			$this->add_header("User-Agent", $finalUserAgent);
		}

		$this->headers = array_merge($this->permanent_headers, $this->headers);

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_TIMEOUT, (is_int($this->timeout) && $this->timeout > 0) ? $this->timeout : 0 );
		curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);
		curl_setopt($ch, CURLOPT_HEADER, true);

		/*
			Windows users having trouble connecting via SSL?
			Download the CA bundle from: http://curl.haxx.se/docs/caextract.html
			Finally uncomment the following line and point it to the downloaded file
		*/
		// curl_setopt($ch, CURLOPT_CAINFO, "c:/path/to/ca-bundle.crt");

		if ($verb == self::HTTP_VERB_POST) {
			curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, self::HTTP_VERB_POST);
			if (!is_null($post_data)) {
				if (is_string($post_data)) {
					curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
				}
				if ($post_data instanceof SimpleXMLElement) {
					curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data->asXML());
				}
			}
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

		$this->last_request_headers = $this->headers;
		$this->headers = [];

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

	/**
	* set_user_agent
	*
	* @author Francisco Martinez Ramos
	* @return bool
	*/
	public function set_user_agent(string $user_agent, bool $prepend = true) {
		$this->prepend_caller_to_user_agent = $prepend;
		$this->user_agent = $user_agent;
		return true;
	}

	/**
	 * add_header
	 *
	 * @author Francisco Martínez Ramos
	 * @param string $header Key of the header
	 * @param string $value Value of the header
	 * @return bool
	 */
	public function add_header(string $header, string $value, bool $permanent = false): bool
	{
		$new_header = "$header: $value";

		if($permanent) {
			array_push($this->permanent_headers, $new_header);
		} else {
			array_push($this->headers, $new_header);
		}

		return true;
	}
   
	/**
	 * set_request_identifier
	 *
	 * @author Francisco Martínez Ramos
	 * @param string $value Value of the request identifier header
	 * @return bool
	 */
	public function set_request_identifier(string $value): bool
	{
		return $this->add_header('X-Request-Id', $value);
	}

	# Get last request headers

	public function get_last_request_headers() {
		return $this->last_request_headers;
	}

	# Get last response headers

	public function get_last_response_headers() {
		return $this->last_response_headers;
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

	public function list_channels($params = "") {
		$params = $this->validateParams($params);
		return($this->request('/p/channels/list.xml'.$params));
	}

	public function channel_upload_logo_get_url($channel)
	{
		$url = "/c/channel/logo/upload/url.xml";
		return($this->request($url, $channel));
	}

	public function channel_upload_logo_process($channel, $upload_info)
	{
		$url = "/c/channel/logo/upload/process.xml";
		return($this->request($url, $channel, self::HTTP_VERB_POST, $upload_info));
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

	public function tour_upload_file_get_url($tour, $channel, $file_type, $file_id)
	{
		$url = "/c/tours/files/upload/url.xml?id=$tour&file_type=$file_type&file_id=$file_id";
		return($this->request($url, $channel, self::HTTP_VERB_GET));
	}

	public function tour_upload_file_process($channel, $upload_info)
	{
		$url = "/c/tours/files/upload/process.xml";
		return($this->request($url, $channel, self::HTTP_VERB_POST, $upload_info));
	}

	public function delete_tour_image($channel, $image_info)
	{
		$url = "/c/tour/images/delete.xml";
		return($this->request($url, $channel, self::HTTP_VERB_POST, $image_info));
	}

	public function delete_tour_document($channel, $document_xml)
	{
		$url = "/c/tour/document/delete.xml";
		return($this->request($url, $channel, self::HTTP_VERB_POST, $document_xml));
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

	public function tours_search_criteria($channel)
	{
		return($this->request(self::PATH_API_TOURS_SEARCH_CRITERIA_GET, $channel));
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

	public function check_customer_login($customer, $password, $channel) {
		return ($this->request('/c/customers/login_search.xml?customer_username='.$customer.'&customer_password='.$password, $channel));
	}

	public function create_customer($customer, $channel)
	{
		return ($this->request('/c/customer/create.xml', $channel, self::HTTP_VERB_POST, $customer));
	}

	public function show_customer($customer, $channel)
	{
		return ($this->request('/c/customer/show.xml?customer_id='.$customer, $channel));
	}

	public function update_customer($customer, $channel)
	{
		return ($this->request('/c/customer/update.xml', $channel, self::HTTP_VERB_POST, $customer));
	}

	public function verify_customer($customer, $channel)
	{
		return ($this->request('/c/customer/verification.xml', $channel, self::HTTP_VERB_POST, $customer));
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

	public function show_agent_profile($agent, $channel = 0)
	{
		return($this->request(self::PATH_API_AGENT_PROFILE_GET."?id=$agent", $channel));
	}

	public function update_agent_profile($update_data)
	{
		return ($this->request(self::PATH_API_AGENT_PROFILE_UPDATE, 0, "POST", $update_data));
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
	public function validate_xml_hash($xml) 
	{
		return $this->generate_xml_hash($xml) == $xml->signed->hash;
	}

	public function generate_xml_hash($xml) 
	{
		$algorithm = $xml->signed->algorithm;
		$fields = explode(" ", $xml->signed->hash_fields);

		$values = [];
		foreach($fields as $field) {
			$xpath_result = $xml->xpath($field);
			foreach($xpath_result as $result) {
				$values[] = (string)$result[0];
			}
		}

		$string_to_hash = implode("|", $values);
		$hash = $this->get_hash($algorithm, $string_to_hash);
		return $hash;
	}

	public function get_hash($algorithm, $string_to_hash) :string 
	{
		return hash_hmac($algorithm, $string_to_hash, $this->private_key, FALSE);
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

	public function show_tours_pickup_routes($tour, $channel)
	{
		return $this->request(self::PATH_API_TOUR_PICKUP_ROUTES_SHOW."?id=$tour", $channel);
	}

	public function update_tours_pickup_routes($data, $channel)
	{
		return $this->request(self::PATH_API_TOUR_PICKUP_ROUTES_UPDATE, $channel, self::HTTP_VERB_POST, $data);
	}

	public function tours_pickup_routes_add_pickup($data, $channel)
	{
		return $this->request(self::PATH_API_TOUR_PICKUP_ROUTES_ADD_PICKUP, $channel, self::HTTP_VERB_POST, $data);
	}

	public function tours_pickup_routes_update_pickup($data, $channel)
	{
		return $this->request(self::PATH_API_TOUR_PICKUP_ROUTES_UPDATE_PICKUP, $channel, self::HTTP_VERB_POST, $data);
	}

	public function tours_pickup_routes_delete_pickup($data, $channel)
	{
		return $this->request(self::PATH_API_TOUR_PICKUP_ROUTES_DELETE_PICKUP, $channel, self::HTTP_VERB_POST, $data);
	}

	# Account
	public function create_account($upload_info) {
		$url = "/p/account/create.xml";
		return($this->request($url, 0, self::HTTP_VERB_POST, $upload_info));
	}

	public function update_account($upload_info, $channel) {
		$url = "/p/account/update.xml";
		return($this->request($url, $channel, self::HTTP_VERB_POST, $upload_info));
	}

	public function show_account($account_id) {
		$url = "/p/account/show.xml?account_id=".$account_id;
		return($this->request($url, 0));
	}

	public function create_channel($channel_info, $channel) {
		$url = "/p/channel/create.xml";
		return($this->request($url, $channel, self::HTTP_VERB_POST, $channel_info));
	}

	public function update_channel($channel_info, $channel) {
		$url = "/p/channel/update.xml";
		return($this->request($url, $channel, self::HTTP_VERB_POST, $channel_info));
	}

	public function show_markup_scheme($channel) {
		$url = "/c/markups/show.xml";
		return($this->request($url, $channel, self::HTTP_VERB_GET));
	}

	public function create_tour_geopoint($geopoint, $channel)
	{
		return $this->request(self::PATH_API_TOUR_GEOS_CREATE, $channel, self::HTTP_VERB_POST, $geopoint);
	}

	public function update_tour_geopoint($geopoint, $channel)
	{
		return $this->request(self::PATH_API_TOUR_GEOS_UPDATE, $channel, self::HTTP_VERB_POST, $geopoint);
	}

	public function delete_tour_geopoint($geopoint, $channel)
	{
		return $this->request(self::PATH_API_TOUR_GEOS_DELETE, $channel, self::HTTP_VERB_POST, $geopoint);
	}

	public function get_custom_fields($channel)
	{
		return $this->request(self::PATH_API_ACCOUNT_CUSTOM_FIELDS_GET, $channel, self::HTTP_VERB_GET);
	}

	public function get_tour_facets($channel)
	{
		return $this->request(self::PATH_API_TOUR_FACETS_GET, $channel, self::HTTP_VERB_GET);
	}

	public function get_list_tours($channel, $query_string)
	{
		$query_string = $this->validateParams($query_string);
		return $this->request(self::PATH_API_LIST_TOURS_GET.$query_string, $channel, self::HTTP_VERB_GET);
	}

	public function get_import_tours_status($channel, $codes)
	{
		return $this->request(self::PATH_API_IMPORT_TOURS_STATUS, $channel, self::HTTP_VERB_POST, $codes);
	}

	public function list_tour_booking_restrictions($channel, $query_string)
	{
		$query_string = $this->validateParams($query_string);
		return $this->request(self::PATH_API_LIST_TOUR_BOOKINGS_RESTRICTIONS.$query_string, $channel, self::HTTP_VERB_GET);
	}

// Internal Functions

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

	/**
	* generate_signature
	*
	* @author Paul Slugocki
	* @param $path API Path
	* @param $verb HTTP Verb
	* @param $channel Channel ID
	* @return String
	*/
	protected function generate_signature($path, $verb, $channel, $outbound_time) 
	{
		$string_to_sign = trim($channel."/".$this->marketp_id."/".$verb."/".$outbound_time.$path);
		$signature = rawurlencode(base64_encode((hash_hmac("sha256", mb_convert_encoding($string_to_sign, 'UTF-8', 'ISO-8859-1'), $this->private_key, TRUE ))));
		return $signature;
	}

}