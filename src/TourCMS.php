<?php
/*
Copyright (c) 2010 - 2025 Palisis TourCMS

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
# Version: 5.0.0

namespace TourCMS\Utils;

use \SimpleXMLElement;

class TourCMS
{
    // ENDPOINTS CONSTS

    public const PATH_API_RATE_LIMIT = '/api/rate_limit_status.xml';


    // Account(s)
    public const PATH_API_ACCOUNT_CREATE = "/p/account/create.xml";
    public const PATH_API_ACCOUNT_UPDATE = "/p/account/update.xml";
    public const PATH_API_ACCOUNT_SHOW = "/p/account/show.xml";
    public const PATH_API_ACCOUNT_CUSTOM_FIELDS_GET = "/api/account/custom_fields/get.xml";


    // Agent(s)
    public const PATH_API_AGENT_PROFILE_GET = "/api/agent/profile/get.xml";
    public const PATH_API_AGENT_PROFILE_UPDATE = "/api/agent/profile/update.xml";
    public const PATH_API_AGENTS_SEARCH = '/c/agents/search.xml';
    public const PATH_API_START_AGENT_LOGIN = '/c/start_agent_login.xml';
    public const PATH_API_AGENT_BOOKING_KEY_RETRIEVE = '/c/retrieve_agent_booking_key.xml';
    public const PATH_API_AGENTS_UPDATE = '/c/agents/update.xml';


    // Channel(s)
    public const PATH_API_CHANNELS_LIST = '/p/channels/list.xml';
    public const PATH_API_CHANNEL_LOGO_UPLOAD_GET_URL = "/c/channel/logo/upload/url.xml";
    public const PATH_API_CHANNEL_LOGO_UPLOAD_PROCESS = "/c/channel/logo/upload/process.xml";
    public const PATH_API_CHANNEL_SHOW = '/c/channel/show.xml';
    public const PATH_API_P_CHANNELS_PERFORMANCE = '/p/channels/performance.xml';
    public const PATH_API_C_CHANNELS_PERFORMANCE = '/c/channel/performance.xml';
    public const PATH_API_CHANNEL_CREATE = "/p/channel/create.xml";
    public const PATH_API_CHANNEL_UPDATE = "/p/channel/update.xml";


    // Tour(s) / Hotel(s)
    public const PATH_API_P_TOURS_SEARCH = '/p/tours/search.xml';
    public const PATH_API_C_TOURS_SEARCH = '/c/tours/search.xml';
    public const PATH_API_P_HOTELS_SEARCH_RANGE = '/p/hotels/search_range.xml';
    public const PATH_API_C_HOTELS_SEARCH_RANGE = '/c/hotels/search_range.xml';
    public const PATH_API_P_HOTELS_SEARCH_AVAIL = '/p/hotels/search_avail.xml';
    public const PATH_API_C_HOTELS_SEARCH_AVAIL = '/c/hotels/search_avail.xml';
    public const PATH_API_TOURS_FILTERS = '/c/tours/filters.xml';
    public const PATH_API_TOUR_UPDATE = '/c/tour/update.xml';
    public const PATH_API_P_TOURS_LIST = '/p/tours/list.xml';
    public const PATH_API_C_TOURS_LIST = '/c/tours/list.xml';
    public const PATH_API_P_TOURS_IMAGES_LIST = '/p/tours/images/list.xml';
    public const PATH_API_C_TOURS_IMAGES_LIST = '/c/tours/images/list.xml';
    public const PATH_API_P_TOURS_LOCATIONS = '/p/tours/locations.xml';
    public const PATH_API_C_TOURS_LOCATIONS = '/c/tours/locations.xml';
    public const PATH_API_TOUR_DELETE = "/c/tour/delete.xml";
    public const PATH_API_TOUR_SHOW = "/c/tour/show.xml";
    public const PATH_API_TOURS_FILE_UPLOAD_GET_URL = "/c/tours/files/upload/url.xml";
    public const PATH_API_TOURS_FILES_UPLOAD_PROCESS = "/c/tours/files/upload/process.xml";
    public const PATH_API_TOUR_IMAGES_DELETE = "/c/tour/images/delete.xml";
    public const PATH_API_TOUR_DOCUMENT_DELETE = "/c/tour/document/delete.xml";
    public const PATH_API_SHOW_TOUR_AVAILABILITY = "/c/tour/datesprices/checkavail.xml";
    public const PATH_API_TOUR_DATES_AND_DEALS = "/c/tour/datesprices/datesndeals/search.xml";
    public const PATH_API_SHOW_TOUR_DEPARTURES = "/c/tour/datesprices/dep/show.xml";
    public const API_PATH_SHOW_TOUR_FREESALE = "/c/tour/datesprices/freesale/show.xml";
    public const PATH_API_TOURS_SEARCH_CRITERIA_GET = "/api/tours/search_criteria/get.xml";


    // Departures
    public const PATH_API_SEARCH_RAW_DEPARTURES = "/c/tour/datesprices/dep/manage/search.xml";
    public const PATH_API_DEPARTURE_SHOW = "/c/tour/datesprices/dep/manage/show.xml";
    public const PATH_API_DEPARTURE_CREATE = "/c/tour/datesprices/dep/manage/new.xml";
    public const PATH_API_DEPARTURE_UPDATE = "/c/tour/datesprices/dep/manage/update.xml";
    public const PATH_API_DEPARTURE_DELETE = "/c/tour/datesprices/dep/manage/delete.xml";


    // Promo codes
    public const PATH_API_PROMO_SHOW = "/c/promo/show.xml";


    // Bookings
    public const PATH_API_BOOKING_GET_REDIRECT_URL = "/c/booking/new/get_redirect_url.xml";
    public const PATH_API_BOOKING_START = "/c/booking/new/start.xml";
    public const PATH_API_BOOKING_COMMIT = "/c/booking/new/commit.xml";
    public const PATH_API_P_BOOKINGS_SEARCH = "/p/bookings/search.xml";
    public const PATH_API_C_BOOKINGS_SEARCH = "/c/bookings/search.xml";
    public const PATH_API_P_BOOKINGS_LIST = '/p/bookings/list.xml';
    public const PATH_API_C_BOOKINGS_LIST = '/c/bookings/list.xml';
    public const PATH_API_BOOKINGS_SHOW = '/c/booking/show.xml';
    public const PATH_API_BOOKING_UPDATE = '/c/booking/update.xml';
    public const PATH_API_BOOKING_CANCEL = '/c/booking/cancel.xml';
    public const PATH_API_BOOKING_DELETE = '/c/booking/delete.xml';
    public const PATH_API_OPTION_CHECK_AVAILABILITY = '/c/booking/options/checkavail.xml';
    public const PATH_API_BOOKING_COMPONENT_NEW = '/c/booking/component/new.xml';
    public const PATH_API_BOOKING_COMPONENT_DELETE = '/c/booking/component/delete.xml';
    public const PATH_API_BOOKING_COMPONENT_UPDATE = '/c/booking/component/update.xml';
    public const PATH_API_BOOKING_NOTE_NEW = '/c/booking/note/new.xml';
    public const PATH_API_BOOKING_EMAIL_SEND = '/c/booking/email/send.xml';


    // Payments
    public const PATH_API_PAYMENT_NEW = '/c/booking/payment/new.xml';
    public const PATH_API_PAYMENT_FAIL = '/c/booking/payment/fail.xml';
    public const PATH_API_PAYMENT_SPREEDLY_CREATE = '/c/booking/payment/spreedly/new.xml';
    public const PATH_API_PAYMENT_SPREEDLY_COMPLETE = '/c/booking/gatewaytransaction/spreedlycomplete.xml';
    public const PATH_API_PAYMENTS_LIST = '/c/booking/payment/list.xml';
    public const PATH_API_PAYMENTS_PAYWORKS_NEW = '/c/booking/payment/payworks/new.xml';


    // Voucher
    public const PATH_API_P_VOUCHER_SEARCH = '/p/voucher/search.xml';
    public const PATH_API_C_VOUCHER_SEARCH = '/c/voucher/search.xml';
    public const PATH_API_VOUCHER_REDEEM = '/c/voucher/redeem.xml';


    // Enquiry
    public const PATH_API_ENQUIRY_NEW = '/c/enquiry/new.xml';
    public const PATH_API_P_ENQUIRIES_SEARCH = '/p/enquiries/search.xml';
    public const PATH_API_C_ENQUIRIES_SEARCH = '/c/enquiries/search.xml';
    public const PATH_API_ENQUIRY_SHOW = '/c/enquiry/show.xml';


    // Customer(s)
    public const PATH_API_CUSTOMER_CREATE = '/c/customer/create.xml';
    public const PATH_API_CUSTOMER_SHOW = '/c/customer/show.xml';
    public const PATH_API_CUSTOMER_UPDATE = '/c/customer/update.xml';
    public const PATH_API_CUSTOMER_LOGIN_SEARCH = '/c/customers/login_search.xml';
    public const PATH_API_CUSTOMER_VERIFICATION = '/c/customer/verification.xml';


    // Pickups
    public const PATH_API_PICKUPS_LIST = '/c/pickups/list.xml';
    public const PATH_API_PICKUPS_NEW = '/c/pickups/new.xml';
    public const PATH_API_PICKUPS_UPDATE = '/c/pickups/update.xml';
    public const PATH_API_PICKUPS_DELETE = '/c/pickups/delete.xml';


    // Pickup Routes
    public const PATH_API_TOUR_PICKUP_ROUTES_SHOW = "/api/tours/pickup/routes/show.xml";
    public const PATH_API_TOUR_PICKUP_ROUTES_UPDATE = "/api/tours/pickup/routes/update.xml";
    public const PATH_API_TOUR_PICKUP_ROUTES_ADD_PICKUP = "/api/tours/pickup/routes/pickup_add.xml";
    public const PATH_API_TOUR_PICKUP_ROUTES_UPDATE_PICKUP = "/api/tours/pickup/routes/pickup_update.xml";
    public const PATH_API_TOUR_PICKUP_ROUTES_DELETE_PICKUP = "/api/tours/pickup/routes/pickup_delete.xml";


    // Geos
    public const PATH_API_TOUR_GEOS_CREATE = "/api/tours/geos/create.xml";
    public const PATH_API_TOUR_GEOS_UPDATE = "/api/tours/geos/update.xml";
    public const PATH_API_TOUR_GEOS_DELETE = "/api/tours/geos/delete.xml";


    // Importer
    public const PATH_API_TOUR_FACETS_GET = "/api/tours/importer/get_tour_facets.xml";
    public const PATH_API_LIST_TOURS_GET = "/api/tours/importer/get_tour_list.xml";
    public const PATH_API_IMPORT_TOURS_STATUS = "/api/tours/importer/get_import_tours_status.xml";
    public const PATH_API_LIST_TOUR_BOOKINGS_RESTRICTIONS = "/api/tours/restrictions/list_tour_bookings_restrictions.xml";
    

    // Staff
    public const PATH_API_STAFF_LIST = '/c/staff/list.xml';

    // Supplier
    public const  PATH_API_SUPPLIER_SHOW = '/c/supplier/show.xml';

    // Markups
    public const PATH_API_MARKUP_SCHEME_SHOW = "/c/markups/show.xml";


    // HTTP VERBS CONST
    public const HTTP_VERB_POST = 'POST';
    public const HTTP_VERB_GET  = 'GET';
    public const HEADER_X_REQUEST_ID = 'X-Request-Id';

    public const RESULT_TYPE_RAW = "raw";
    public const RESULT_TYPE_XML = "simplexml";

    // General settings
    protected string $baseUrl = "https://api.tourcms.com";
    protected int $marketplaceId = 0;
    protected string $privateKey = "";
    protected string $resultType = "";
    protected int $timeout = 0;
    protected array $lastRequestHeaders = [];
    protected array $lastResponseHeaders = [];
    protected string $userAgent = "TourCMS PHP Wrapper v5.0.0";
    protected bool $prependCallerToUserAgent = true;
    protected array $headers = [];
    protected array $permanentHeaders = [];

    /**
     * __construct
     *
     * @author Paul Slugocki
     * @param $marketplaceId Marketplace ID
     * @param $key API Private Key
     * @param $resultType Result type, defaults to raw
     * @param $timeout Timeout, default 0
     */
    public function __construct(int $marketplaceId, string $key, string $resultType = self::RESULT_TYPE_RAW, int $timeout = 0)
    {
        $this->marketplaceId = $marketplaceId;
        $this->privateKey = $key;
        $this->resultType = $resultType;
        $this->timeout = $timeout;
    }

    /**
     * request
     *
     * @author Paul Slugocki
     * @param string $path API path to call
     * @param int $channel Channel ID, defaults to zero
     * @param string $verb HTTP Verb, defaults to GET
     * @param null|string|SimpleXMLElement $postData POST data to send
     * @return string|SimpleXMLElement
     */
    public function request(string $path, int $channel = 0, string $verb = self::HTTP_VERB_GET, null|string|SimpleXMLElement $postData = null): bool|SimpleXMLElement|string
    {
        // Prepare the URL we are sending to
        $url = $this->baseUrl . $path;
        // We need a signature for the header

        $outboundTime = time();
        $signature = $this->generateSignature($path, $verb, $channel, $outboundTime);

        // Build headers
        $this->add_header("Content-type", "text/xml;charset=\"utf-8\"");
        $this->add_header("Date", gmdate('D, d M Y H:i:s \G\M\T', $outboundTime));
        $this->add_header("Authorization", "TourCMS $channel:$this->marketplaceId:$signature");
        // Add user-agent to headers array
        if (!empty($this->userAgent)) {
            $finalUserAgent = $this->prependCallerToUserAgent ? $this->userAgent . " (" . $this->marketplaceId . "_" . $channel . ")" : $this->userAgent;
            $this->add_header("User-Agent", $finalUserAgent);
        }

        $this->headers = array_merge($this->permanentHeaders, $this->headers);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, (is_int($this->timeout) && $this->timeout > 0) ? $this->timeout : 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);
        curl_setopt($ch, CURLOPT_HEADER, true);

        /*
			Windows users having trouble connecting via SSL?
			Download the CA bundle from: http://curl.haxx.se/docs/caextract.html
			Finally uncomment the following line and point it to the downloaded file
		*/
        // curl_setopt($ch, CURLOPT_CAINFO, "c:/path/to/ca-bundle.crt");

        if ($verb == self::HTTP_VERB_POST) {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, self::HTTP_VERB_POST);
            if (!is_null($postData)) {
                if (is_string($postData)) {
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
                }
                if ($postData instanceof SimpleXMLElement) {
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData->asXML());
                }
            }
        }

        // Callback function to populate the response headers on curl_exec
        $apiResponseHeaders = [];
        curl_setopt(
            $ch,
            CURLOPT_HEADERFUNCTION,
            function ($ch, $header) use (&$apiResponseHeaders) {
                $len = strlen($header);
                $header = explode(':', $header, 2);
                if (count($header) < 2) // ignore invalid headers
                    return $len;

                $name = strtolower(trim($header[0]));
                $apiResponseHeaders[$name] = trim($header[1]);

                return $len;
            }
        );

        $response = curl_exec($ch);

        $this->lastResponseHeaders = $apiResponseHeaders;

        $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $result = substr($response, $headerSize);

        // Check whether we need to return raw XML or
        // convert to SimpleXML first
        if ($this->resultType == self::RESULT_TYPE_XML)
            $result = simplexml_load_string($result);

        $this->lastRequestHeaders = $this->headers;
        $this->headers = [];

        return $result;
    }

    /**
     * get_base_url
     *
     * @author Paul Slugocki
     * @return string
     */
    public function get_base_url(): string
    {
        return $this->baseUrl;
    }

    /**
     * set_base_url
     *
     * @author Paul Slugocki
     * @param $url New base url
     * @return bool
     */
    public function set_base_url(string $url): void
    {
        $this->baseUrl = $url;
    }

    /**
     * set_user_agent
     *
     * @author Francisco Martinez Ramos
     * @return bool
     */
    public function set_user_agent(string $user_agent, bool $prepend = true): void
    {
        $this->prependCallerToUserAgent = $prepend;
        $this->userAgent = $user_agent;
    }

    /**
     * add_header
     *
     * @author Francisco Martínez Ramos
     * @param string $header Key of the header
     * @param string $value Value of the header
     * @return void
     */
    public function add_header(string $header, string $value, bool $permanent = false): void
    {
        $newHeader = "$header: $value";

        if ($permanent) {
            array_push($this->permanentHeaders, $newHeader);
        } else {
            array_push($this->headers, $newHeader);
        }
    }

    /**
     * set_request_identifier
     *
     * @author Francisco Martínez Ramos
     * @param string $value Value of the request identifier header
     * @return void
     */
    public function set_request_identifier(string $value): void
    {
        $this->add_header(self::HEADER_X_REQUEST_ID, $value);
    }

    # Get last request headers

    public function get_last_request_headers(): array
    {
        return $this->lastRequestHeaders;
    }

    # Get last response headers

    public function get_lastResponseHeaders(): array
    {
        return $this->lastResponseHeaders;
    }

    # Test environment

    public function test_environment(int $channel = 0): void
    {
        include 'test.php';
    }

    # API methods (Housekeeping)

    public function api_rate_limit_status(int $channel = 0): SimpleXMLElement|string
    {
        return $this->request(self::PATH_API_RATE_LIMIT, $channel);
    }

    # Channel methods

    public function list_channels(string $params = ""): SimpleXMLElement|string
    {
        return $this->request(self::PATH_API_CHANNELS_LIST . $this->validateParams($params));
    }

    public function channel_upload_logo_get_url(int $channel): SimpleXMLElement|string
    {
        return $this->request(self::PATH_API_CHANNEL_LOGO_UPLOAD_GET_URL, $channel);
    }

    public function channel_upload_logo_process(int $channel, SimpleXMLElement|string $uploadInfo)
    {
        return $this->request(self::PATH_API_CHANNEL_LOGO_UPLOAD_PROCESS, $channel, self::HTTP_VERB_POST, $uploadInfo);
    }

    public function show_channel(int $channel): SimpleXMLElement|string
    {
        return $this->request(self::PATH_API_CHANNEL_SHOW, $channel);
    }

    public function channel_performance(int $channel = 0): SimpleXMLElement|string
    {

        if ($channel == 0) return $this->request(self::PATH_API_P_CHANNELS_PERFORMANCE);

        return $this->request(self::PATH_API_C_CHANNELS_PERFORMANCE, $channel);
    }

    # Tour/Hotel methods

    public function search_tours(string $params = "", int $channel = 0): SimpleXMLElement|string
    {

        $params = $this->validateParams($params);

        if ($channel == 0) return $this->request(self::PATH_API_P_TOURS_SEARCH . $params);

        return $this->request(self::PATH_API_C_TOURS_SEARCH . $params, $channel);
    }

    public function search_hotels_range(string $params = "", ?int $tourId = null, int $channel = 0)
    {

        $params = $this->validateParams($params);

        if (!is_null($tourId)) {

            if (empty($params)) {
                $params = '?single_tour_id=';
            } else {
                $params .= "&single_tour_id=";
            }

            $params .= $tourId;
        }

        if ($channel == 0) return $this->request(self::PATH_API_P_HOTELS_SEARCH_RANGE . $params);

        return $this->request(self::PATH_API_C_HOTELS_SEARCH_RANGE . $params, $channel);
    }

    public function search_hotels_specific(string $params = "", ?int $tourId = null, int $channel = 0)
    {
        $params = $this->validateParams($params);

        if (!is_null($tourId)) {

            if (empty($params)) {
                $params = '?single_tour_id=';
            } else {
                $params .= "&single_tour_id=";
            }

            $params .= $tourId;
        }

        if ($channel == 0) return $this->request(self::PATH_API_P_HOTELS_SEARCH_AVAIL . $params);

        return $this->request(self::PATH_API_C_HOTELS_SEARCH_AVAIL . $params, $channel);
    }

    public function list_product_filters(int $channel = 0): SimpleXMLElement|string
    {
        return $this->request(self::PATH_API_TOURS_FILTERS, $channel);
    }

    public function update_tour($tour_data, int $channel): SimpleXMLElement|string
    {
        return $this->request(self::PATH_API_TOUR_UPDATE, $channel, self::HTTP_VERB_POST, $tour_data);
    }

    public function update_tour_url(int $tourId, int $channel, string $tourUrl): SimpleXMLElement|string
    {

        $urlData = new SimpleXMLElement('<tour />');
        $urlData->addChild('tour_id', $tourId);
        $urlData->addChild('tour_url', $tourUrl);

        return $this->update_tour($urlData, $channel);
    }

    public function list_tours(int $channel = 0, string $params = ""): SimpleXMLElement|string
    {
        $params = $this->validateParams($params);
        if ($channel == 0) return $this->request(self::PATH_API_P_TOURS_LIST . $params);
        
        return $this->request(self::PATH_API_C_TOURS_LIST . $params, $channel);
    }

    public function list_tour_images(int $channel = 0, string $params = ""): SimpleXMLElement|string
    {
        $params = $this->validateParams($params);
        if ($channel == 0) return $this->request(self::PATH_API_P_TOURS_IMAGES_LIST . $params);

        return $this->request(self::PATH_API_C_TOURS_IMAGES_LIST . $params, $channel);
    }

    public function list_tour_locations(int $channel = 0, string $params = ""): SimpleXMLElement|string
    {
        $params = $this->validateParams($params);
        if ($channel == 0) return $this->request(self::PATH_API_P_TOURS_LOCATIONS . $params);
        
        return $this->request(self::PATH_API_C_TOURS_LOCATIONS . $params, $channel);
    }

    public function delete_tour(int $tourId, int $channel): SimpleXMLElement|string
    {
        $url = self::PATH_API_TOUR_DELETE .'?id=' . $tourId;
        return $this->request($url, $channel, self::HTTP_VERB_POST);
    }

    public function show_tour(int $tourId, int $channel, ?string $params = null)
    {
        $url = self::PATH_API_TOUR_SHOW . '?id=' . $tourId;

        /*

			Third param for show tour could be:

			- bool: show_options=1 / 0 (deprecated)

			- string: params

		*/

        if (is_string($params)) {

            $url .= "&" . $params;
        } else {

            if ($params)
                $url .= "&show_options=1";
        }

        if ($tourId > 0) {
            return $this->request($url, $channel);
        }
    }

    public function tour_upload_file_get_url($tour, $channel, $file_type, $file_id): SimpleXMLElement|string
    {
        $url = self::PATH_API_TOURS_FILE_UPLOAD_GET_URL . "?id=$tour&file_type=$file_type&file_id=$file_id";
        return $this->request($url, $channel, self::HTTP_VERB_GET);
    }

    public function tour_upload_file_process(int $channel, SimpleXMLElement|string $uploadInfo): SimpleXMLElement|string
    {
        return $this->request(self::PATH_API_TOURS_FILES_UPLOAD_PROCESS, $channel, self::HTTP_VERB_POST, $uploadInfo);
    }

    public function delete_tour_image(int $channel, SimpleXMLElement|string $image_info): SimpleXMLElement|string
    {
        return $this->request(self::PATH_API_TOUR_IMAGES_DELETE, $channel, self::HTTP_VERB_POST, $image_info);
    }

    public function delete_tour_document(int $channel, $documentXML): SimpleXMLElement|string
    {
        return $this->request(self::PATH_API_TOUR_DOCUMENT_DELETE, $channel, self::HTTP_VERB_POST, $documentXML);
    }


    public function check_tour_availability(string $params, int $tourId, int $channel): SimpleXMLElement|string
    {
        return $this->request(self::PATH_API_SHOW_TOUR_AVAILABILITY . '?id=' . $tourId . $this->validateParams($params), $channel);
    }

    public function show_tour_datesanddeals(int $tourId, int $channel, string $params = ""): SimpleXMLElement|string
    {
        return $this->request(self::PATH_API_TOUR_DATES_AND_DEALS .'?id=' . $tourId . $this->validateParams($params), $channel);
    }


    public function show_tour_departures(int $tourId, int $channel, string $params = ""): SimpleXMLElement|string
    {
        return $this->request(self::PATH_API_SHOW_TOUR_DEPARTURES . '?id=' . $tourId . $this->validateParams($params), $channel);
    }

    public function show_tour_freesale(int $tourId, int $channel): SimpleXMLElement|string
    {
        return $this->request(self::API_PATH_SHOW_TOUR_FREESALE . '?id=' . $tourId, $channel);
    }

    public function tours_search_criteria(int $channel): SimpleXMLElement|string
    {
        return $this->request(self::PATH_API_TOURS_SEARCH_CRITERIA_GET, $channel);
    }

    /*
		Raw departure methods
	*/

    public function search_raw_departures(int $tourId, int $channel, string $params = ""): SimpleXMLElement|string
    {
        return $this->request(self::PATH_API_SEARCH_RAW_DEPARTURES . '?id=' . $tourId . $this->validateParams($params), $channel);
    }

    public function show_departure(int $departure, int $tour, int $channel): SimpleXMLElement|string
    {
        return $this->request(self::PATH_API_DEPARTURE_SHOW . '?id=' . $tour . '&departure_id=' . $departure, $channel);
    }

    public function create_departure(SimpleXMLElement|string $departureData, int $channel): SimpleXMLElement|string
    {
        return $this->request(self::PATH_API_DEPARTURE_CREATE, $channel, self::HTTP_VERB_POST, $departureData);
    }

    public function update_departure(SimpleXMLElement|string $departureData, int $channel): SimpleXMLElement|string
    {
        return $this->request(self::PATH_API_DEPARTURE_UPDATE, $channel, self::HTTP_VERB_POST, $departureData);
    }

    public function delete_departure(int $departureId, int $tourId, int $channelId): SimpleXMLElement|string
    {
        return $this->request(self::PATH_API_DEPARTURE_DELETE . '?id=' . $tourId . '&departure_id=' . $departureId, $channelId, self::HTTP_VERB_POST);
    }

    /*
		Promo code
	*/

    public function show_promo(int $promoId, int $channel): SimpleXMLElement|string
    {
        return $this->request(self::PATH_API_PROMO_SHOW . '?promo_code=' . $promoId, $channel);
    }

    # Booking methods

    /*
		Making bookings
	*/

    public function get_booking_redirect_url(SimpleXMLElement|string $urlData, int $channel): SimpleXMLElement|string
    {
        return $this->request(self::PATH_API_BOOKING_GET_REDIRECT_URL, $channel, self::HTTP_VERB_POST, $urlData);
    }

    public function start_new_booking(SimpleXMLElement|string $bookingData, int $channel): SimpleXMLElement|string
    {
        return $this->request(self::PATH_API_BOOKING_START, $channel, self::HTTP_VERB_POST, $bookingData);
    }

    public function commit_new_booking(SimpleXMLElement|string $bookingData, $channel)
    {
        return $this->request(self::PATH_API_BOOKING_COMMIT, $channel, self::HTTP_VERB_POST, $bookingData);
    }

    /*
		Retrieving bookings
	*/

    public function search_bookings(string $params = "", int $channel = 0): SimpleXMLElement|string
    {
        $params = $this->validateParams($params);
        if ($channel == 0) return $this->request(self::PATH_API_P_BOOKINGS_SEARCH . $params);
        
        return $this->request(self::PATH_API_C_BOOKINGS_SEARCH . $params, $channel);
    }

    public function list_bookings(string $params = "", int $channel = 0): SimpleXMLElement|string
    {
        $params = $this->validateParams($params);
        if ($channel == 0) return $this->request(self::PATH_API_P_BOOKINGS_LIST . $params);
        
        return $this->request(self::PATH_API_C_BOOKINGS_LIST . $params, $channel);
    }

    public function show_booking(int $bookingId, int $channel): SimpleXMLElement|string
    {
        return $this->request(self::PATH_API_BOOKINGS_SHOW . '?booking_id=' . $bookingId, $channel);
    }

    public function search_voucher(SimpleXMLElement|string|null $voucherData = null, $channel = 0): SimpleXMLElement|string
    {

        if ($voucherData == null) {
            $voucherData = new SimpleXMLElement('<voucher />');
            $voucherData->addChild('barcode_data', '');
        }

        if ($channel == 0) {
            return $this->request(self::PATH_API_P_VOUCHER_SEARCH, $channel, self::HTTP_VERB_POST, $voucherData);
        }
        
        return $this->request(self::PATH_API_C_VOUCHER_SEARCH, $channel, self::HTTP_VERB_POST, $voucherData);
    }

    /*
		Updating bookings
	*/

    public function update_booking(SimpleXMLElement|string $bookingData, int $channel): SimpleXMLElement|string
    {
        return $this->request(self::PATH_API_BOOKING_UPDATE, $channel, self::HTTP_VERB_POST, $bookingData);
    }

    public function create_payment(SimpleXMLElement|string $paymentData, int $channel): SimpleXMLElement|string
    {
        return $this->request(self::PATH_API_PAYMENT_NEW, $channel, self::HTTP_VERB_POST, $paymentData);
    }

    public function log_failed_payment(SimpleXMLElement|string $paymentData, int $channel): SimpleXMLElement|string
    {
        return $this->request(self::PATH_API_PAYMENT_FAIL, $channel, self::HTTP_VERB_POST, $paymentData);
    }

    public function spreedly_create_payment(SimpleXMLElement|string $paymentData, int $channel): SimpleXMLElement|string
    {
        return $this->request(self::PATH_API_PAYMENT_SPREEDLY_CREATE, $channel, self::HTTP_VERB_POST, $paymentData);
    }

    public function spreedly_complete_payment(string $transactionId, int $channel): SimpleXMLElement|string
    {
        return $this->request(self::PATH_API_PAYMENT_SPREEDLY_COMPLETE . '?id=' . $transactionId, $channel, self::HTTP_VERB_POST);
    }

    public function cancel_booking(SimpleXMLElement|string $bookingData, int $channel): SimpleXMLElement|string
    {
        return $this->request(self::PATH_API_BOOKING_CANCEL, $channel, self::HTTP_VERB_POST, $bookingData);
    }

    public function delete_booking(int $bookingId, int $channel): SimpleXMLElement|string
    {
        return $this->request(self::PATH_API_BOOKING_DELETE . '?booking_id=' . $bookingId, $channel, self::HTTP_VERB_POST);
    }

    public function check_option_availability(int $bookingId, string $tourComponentId, int $channel): SimpleXMLElement|string
    {
        return $this->request(self::PATH_API_OPTION_CHECK_AVAILABILITY . '?booking_id=' . $bookingId . '&tour_component_id=' . $tourComponentId, $channel);
    }

    public function booking_add_component(SimpleXMLElement|string $componentData, int $channel): SimpleXMLElement|string
    {
        return $this->request(self::PATH_API_BOOKING_COMPONENT_NEW, $channel, self::HTTP_VERB_POST, $componentData);
    }

    public function booking_remove_component(SimpleXMLElement|string $componentData, int $channel): SimpleXMLElement|string
    {
        return $this->request(self::PATH_API_BOOKING_COMPONENT_DELETE, $channel, self::HTTP_VERB_POST, $componentData);
    }

    public function booking_update_component(SimpleXMLElement|string $componentData, int $channel): SimpleXMLElement|string
    {
        return $this->request(self::PATH_API_BOOKING_COMPONENT_UPDATE, $channel, self::HTTP_VERB_POST, $componentData);
    }

    public function add_note_to_booking(int $bookingId, int $channel, string $text, string $noteType): SimpleXMLElement|string
    {

        $bookingData = new SimpleXMLElement('<booking />');
        $bookingData->addChild('booking_id', $bookingId);
        $note = $bookingData->addChild('note');
        $note->addChild('text', $text);
        $note->addChild('type', $noteType);

        return $this->request(self::PATH_API_BOOKING_NOTE_NEW, $channel, self::HTTP_VERB_POST, $bookingData);
    }

    public function send_booking_email(SimpleXMLElement|string $bookingData, int $channel): SimpleXMLElement|string
    {
        return $this->request(self::PATH_API_BOOKING_EMAIL_SEND, $channel, self::HTTP_VERB_POST, $bookingData);
    }

    public function redeem_voucher(SimpleXMLElement|string $voucherData, int $channel = 0): SimpleXMLElement|string
    {
        return $this->request(self::PATH_API_VOUCHER_REDEEM, $channel, self::HTTP_VERB_POST, $voucherData);
    }

    # Enquiry and customer methods

    public function create_enquiry(SimpleXMLElement|string $enquiryData, int $channel): SimpleXMLElement|string
    {
        return $this->request(self::PATH_API_ENQUIRY_NEW, $channel, self::HTTP_VERB_POST, $enquiryData);
    }

    public function update_customer(SimpleXMLElement|string $customerData, int $channel): SimpleXMLElement|string
    {
        return $this->request(self::PATH_API_CUSTOMER_UPDATE, $channel, self::HTTP_VERB_POST, $customerData);
    }

    public function search_enquiries(string $params = "", int $channel = 0): SimpleXMLElement|string
    {
        $params = $this->validateParams($params);
        if ($channel == 0) return $this->request(self::PATH_API_P_ENQUIRIES_SEARCH . $params);

        return $this->request(self::PATH_API_C_ENQUIRIES_SEARCH . $params, $channel);
    }

    public function show_enquiry(int $enquiryId, int $channel): SimpleXMLElement|string
    {
        return $this->request(self::PATH_API_ENQUIRY_SHOW . '?enquiry_id=' . $enquiryId, $channel);
    }

    public function create_customer(SimpleXMLElement|string $customer, int $channel): SimpleXMLElement|string
	{
		return $this->request(self::PATH_API_CUSTOMER_CREATE, $channel, self::HTTP_VERB_POST, $customer);
	}

    public function show_customer(int $customerId, int $channel): SimpleXMLElement|string
    {
        return $this->request(self::PATH_API_CUSTOMER_SHOW . '?customer_id=' . $customerId, $channel);
    }

    public function check_customer_login(string $username, string $password, int $channel): SimpleXMLElement|string
    {
        return $this->request(self::PATH_API_CUSTOMER_LOGIN_SEARCH . '?customer_username=' . $username . '&customer_password=' . $password, $channel);
    }

    public function verify_customer(SimpleXMLElement|string $customer, int $channel): SimpleXMLElement|string
	{
		return $this->request(self::PATH_API_CUSTOMER_VERIFICATION, $channel, self::HTTP_VERB_POST, $customer);
	}

    # Agents
    public function search_agents(string $params, int $channel): SimpleXMLElement|string
    {
        $params = $this->validateParams($params);
        return $this->request(self::PATH_API_AGENTS_SEARCH . $params, $channel);
    }

    public function start_new_agent_login(string $params, int $channel): SimpleXMLElement|string
    {
        return $this->request(self::PATH_API_START_AGENT_LOGIN, $channel, self::HTTP_VERB_POST, $params);
    }

    public function retrieve_agent_booking_key(string $privateToken, int $channel): SimpleXMLElement|string
    {
        return $this->request(self::PATH_API_AGENT_BOOKING_KEY_RETRIEVE . '?k=' . $privateToken, $channel);
    }

    public function update_agent(SimpleXMLElement|string $agentData, int $channel): SimpleXMLElement|string
    {
        return $this->request(self::PATH_API_AGENTS_UPDATE, $channel, self::HTTP_VERB_POST, $agentData);
    }

    public function show_agent_profile(SimpleXMLElement|string $agent, int $channel = 0): SimpleXMLElement|string
    {
        return $this->request(self::PATH_API_AGENT_PROFILE_GET . "?id=$agent", $channel);
    }

    public function update_agent_profile(SimpleXMLElement|string $agentProfileData): SimpleXMLElement|string
    {
        return $this->request(self::PATH_API_AGENT_PROFILE_UPDATE, 0, self::HTTP_VERB_POST, $agentProfileData);
    }

    # Payments
    public function list_payments($params, $channel): SimpleXMLElement|string
    {
        $params = $this->validateParams($params);
        return $this->request(self::PATH_API_PAYMENTS_LIST . $params, $channel);
    }

    public function payworks_booking_payment_new(SimpleXMLElement|string $payment, int $channel): SimpleXMLElement|string
    {
        return $this->request(self::PATH_API_PAYMENTS_PAYWORKS_NEW, $channel, self::HTTP_VERB_POST, $payment);
    }

    # Staff members
    public function list_staff_members(int $channel): SimpleXMLElement|string
    {
        return $this->request(self::PATH_API_STAFF_LIST, $channel);
    }

    # Internal supplier methods
    public function show_supplier(int $supplierId, int $channel): SimpleXMLElement|string
    {
        return $this->request(self::PATH_API_SUPPLIER_SHOW . '?supplier_id=' . $supplierId, $channel);
    }

    # CRUD Pickup points
    public function list_pickups(string $params, int $channel): SimpleXMLElement|string
    {
        return $this->request(self::PATH_API_PICKUPS_LIST . $this->validateParams($params), $channel);
    }

    public function create_pickup(SimpleXMLElement|string $pickupData, int $channel): SimpleXMLElement|string
    {
        return $this->request(self::PATH_API_PICKUPS_NEW, $channel, self::HTTP_VERB_POST, $pickupData);
    }

    public function update_pickup(SimpleXMLElement|string $pickupData, int $channel): SimpleXMLElement|string
    {
        return $this->request(self::PATH_API_PICKUPS_UPDATE, $channel, self::HTTP_VERB_POST, $pickupData);
    }

    public function delete_pickup(SimpleXMLElement|string $pickupData, int $channel): SimpleXMLElement|string
    {
        return $this->request(self::PATH_API_PICKUPS_DELETE, $channel, self::HTTP_VERB_POST, $pickupData);
    }

    public function show_tours_pickup_routes(int $tourId, int $channel): SimpleXMLElement|string
    {
        return $this->request(self::PATH_API_TOUR_PICKUP_ROUTES_SHOW . "?id=$tourId", $channel);
    }

    public function update_tours_pickup_routes(SimpleXMLElement|string $data, int $channel): SimpleXMLElement|string
    {
        return $this->request(self::PATH_API_TOUR_PICKUP_ROUTES_UPDATE, $channel, self::HTTP_VERB_POST, $data);
    }

    public function tours_pickup_routes_add_pickup(SimpleXMLElement|string $data, int $channel): SimpleXMLElement|string
    {
        return $this->request(self::PATH_API_TOUR_PICKUP_ROUTES_ADD_PICKUP, $channel, self::HTTP_VERB_POST, $data);
    }

    public function tours_pickup_routes_update_pickup(SimpleXMLElement|string $data, int $channel): SimpleXMLElement|string
    {
        return $this->request(self::PATH_API_TOUR_PICKUP_ROUTES_UPDATE_PICKUP, $channel, self::HTTP_VERB_POST, $data);
    }

    public function tours_pickup_routes_delete_pickup(SimpleXMLElement|string $data, int $channel): SimpleXMLElement|string
    {
        return $this->request(self::PATH_API_TOUR_PICKUP_ROUTES_DELETE_PICKUP, $channel, self::HTTP_VERB_POST, $data);
    }

    # Account
    public function create_account(SimpleXMLElement|string $uploadInfo): SimpleXMLElement|string
    {
        return $this->request(self::PATH_API_ACCOUNT_CREATE, 0, self::HTTP_VERB_POST, $uploadInfo);
    }

    public function update_account(SimpleXMLElement|string $uploadInfo, int $channel): SimpleXMLElement|string
    {
        return $this->request(self::PATH_API_ACCOUNT_UPDATE, $channel, self::HTTP_VERB_POST, $uploadInfo);
    }

    public function show_account(int $accountId): SimpleXMLElement|string
    {
        $url = self::PATH_API_ACCOUNT_SHOW . "?account_id=" . $accountId;
        return $this->request($url, 0);
    }

    public function create_channel(SimpleXMLElement|string $channel_info, int $channel): SimpleXMLElement|string
    {
        return $this->request(self::PATH_API_CHANNEL_CREATE, $channel, self::HTTP_VERB_POST, $channel_info);
    }

    public function update_channel(SimpleXMLElement|string $channelInfo, int $channel): SimpleXMLElement|string
    {
        return $this->request(self::PATH_API_CHANNEL_UPDATE, $channel, self::HTTP_VERB_POST, $channelInfo);
    }

    public function show_markup_scheme(int $channel): SimpleXMLElement|string
    {
        return $this->request(self::PATH_API_MARKUP_SCHEME_SHOW, $channel, self::HTTP_VERB_GET);
    }

    public function create_tour_geopoint(SimpleXMLElement|string $geopoint, int $channel): SimpleXMLElement|string
    {
        return $this->request(self::PATH_API_TOUR_GEOS_CREATE, $channel, self::HTTP_VERB_POST, $geopoint);
    }

    public function update_tour_geopoint(SimpleXMLElement|string $geopoint, int $channel): SimpleXMLElement|string
    {
        return $this->request(self::PATH_API_TOUR_GEOS_UPDATE, $channel, self::HTTP_VERB_POST, $geopoint);
    }

    public function delete_tour_geopoint(SimpleXMLElement|string $geopoint, int $channel): SimpleXMLElement|string
    {
        return $this->request(self::PATH_API_TOUR_GEOS_DELETE, $channel, self::HTTP_VERB_POST, $geopoint);
    }

    public function get_custom_fields(int $channel): SimpleXMLElement|string
    {
        return $this->request(self::PATH_API_ACCOUNT_CUSTOM_FIELDS_GET, $channel, self::HTTP_VERB_GET);
    }

    public function get_tour_facets(int $channel): SimpleXMLElement|string
    {
        return $this->request(self::PATH_API_TOUR_FACETS_GET, $channel, self::HTTP_VERB_GET);
    }

    public function get_list_tours(int $channel, string $queryString): SimpleXMLElement|string
    {
        $queryString = $this->validateParams($queryString);
        return $this->request(self::PATH_API_LIST_TOURS_GET . $queryString, $channel, self::HTTP_VERB_GET);
    }

    public function get_import_tours_status(int $channel, SimpleXMLElement|string $codes): SimpleXMLElement|string
    {
        return $this->request(self::PATH_API_IMPORT_TOURS_STATUS, $channel, self::HTTP_VERB_POST, $codes);
    }

    public function list_tour_booking_restrictions(int $channel, string $queryString): SimpleXMLElement|string
    {
        $queryString = $this->validateParams($queryString);
        return $this->request(self::PATH_API_LIST_TOUR_BOOKINGS_RESTRICTIONS . $queryString, $channel, self::HTTP_VERB_GET);
    }

    # Used for validating webhook signatures
    /**
     * Validate XML Hash
     * @param SimpleXMLElement $xml
     * @return bool
     */
    public function validate_xml_hash(SimpleXMLElement $xml): bool
    {
        return $this->generate_xml_hash($xml) == $xml->signed->hash;
    }

    /**
     * @param \SimpleXMLElement $xml
     * @return string
     */
    public function generate_xml_hash(SimpleXMLElement $xml): string
    {
        $algorithm = $xml->signed->algorithm;
        $fields = explode(" ", $xml->signed->hash_fields);

        $values = [];
        foreach ($fields as $field) {
            $xpathResult = $xml->xpath($field);
            foreach ($xpathResult as $result) {
                $values[] = (string)$result[0];
            }
        }

        $stringToHash = implode("|", $values);
        $hash = $this->get_hash($algorithm, $stringToHash);
        return $hash;
    }

    /**
     * @param string $algorithm
     * @param string $stringToHash
     * @return string
     */
    public function get_hash(string $algorithm, string $stringToHash): string
    {
        return hash_hmac($algorithm, $stringToHash, $this->privateKey, false);
    }

    // Internal Functions

    protected function validateParams(mixed $params): string
    {
        if (empty($params) || !is_string($params)) {
            return '';
        }

        if (!empty($params) && substr($params, 0, 1) !== '?') {
            $params = '?' . $params;
        }

        return $params;
    }

    /**
     * generateSignature
     *
     * @author Paul Slugocki
     * @param $path API Path
     * @param $verb HTTP Verb
     * @param $channel Channel ID
     * @return string
     */
    protected function generateSignature(string $path, string $verb, string $channel, int $outboundTime): string
    {
        $string_to_sign = trim($channel . "/" . $this->marketplaceId . "/" . $verb . "/" . $outboundTime . $path);
        $signature = rawurlencode(base64_encode((hash_hmac("sha256", mb_convert_encoding($string_to_sign, 'UTF-8', 'ISO-8859-1'), $this->privateKey, TRUE))));
        return $signature;
    }

}
