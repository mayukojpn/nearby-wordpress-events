<?php
/**
 * Unit tests for methods in WP_Nearby_Events.
 *
 * @package Nearby WordPress Events
 */

/**
 * Class Test_WP_Nearby_Events.
 */
class Test_WP_Nearby_Events extends WP_UnitTestCase {
	/**
	 * An instance of the class to test.
	 *
	 * @var WP_Nearby_Events
	 */
	private $instance;

	/**
	 * Perform for every test.
	 */
	public function setUp() {
		parent::setUp();

		$this->instance = new WP_Nearby_Events( 1, $this->get_user_location() );
	}

	/**
	 * Simulate a stored user location.
	 *
	 * @return array
	 */
	private function get_user_location() {
		return array(
			'description' => 'San Francisco',
			'latitude'    => '37.7749300',
			'longitude'   => '-122.4194200',
			'country'     => 'US',
		);
	}

	/**
	 * Test: `get_events()` should return an instance of WP_Error if the response code is not 200.
	 */
	public function test_get_events_bad_response_code() {
		add_filter( 'pre_http_request', array( $this, '_http_request_bad_response_code' ) );

		$this->assertWPError( $this->instance->get_events() );

		remove_filter( 'pre_http_request', array( $this, '_http_request_bad_response_code' ) );
	}

	/**
	 * Test: The response body should not be cached if the response code is not 200.
	 */
	public function test_get_cached_events_bad_response_code() {
		add_filter( 'pre_http_request', array( $this, '_http_request_bad_response_code' ) );

		$this->instance->get_events();

		$this->assertFalse( $this->instance->get_cached_events() );

		remove_filter( 'pre_http_request', array( $this, '_http_request_bad_response_code' ) );
	}

	/**
	 * Simulate an HTTP response with a non-200 response code.
	 *
	 * @return array
	 */
	public function _http_request_bad_response_code() {
		return array(
			'headers'  => '',
			'body'     => '',
			'response' => array(
				'code' => 404,
			),
			'cookies'  => '',
			'filename' => '',
		);
	}

	/**
	 * Test: `get_events()` should return an instance of WP_Error if the response body does not have
	 * the required properties.
	 */
	public function test_get_events_invalid_response() {
		add_filter( 'pre_http_request', array( $this, '_http_request_invalid_response' ) );

		$this->assertWPError( $this->instance->get_events() );

		remove_filter( 'pre_http_request', array( $this, '_http_request_invalid_response' ) );
	}

	/**
	 * Test: The response body should not be cached if it does not have the required properties.
	 */
	public function test_get_cached_events_invalid_response() {
		add_filter( 'pre_http_request', array( $this, '_http_request_invalid_response' ) );

		$this->instance->get_events();

		$this->assertFalse( $this->instance->get_cached_events() );

		remove_filter( 'pre_http_request', array( $this, '_http_request_invalid_response' ) );
	}

	/**
	 * Simulate an HTTP response with a body that does not have the required properties.
	 *
	 * @return array
	 */
	public function _http_request_invalid_response() {
		return array(
			'headers'  => '',
			'body'     => wp_json_encode( array() ),
			'response' => array(
				'code' => 200,
			),
			'cookies'  => '',
			'filename' => '',
		);
	}

	/**
	 * Test: With a valid response, `get_events()` should return an associated array containing a location array and
	 * an events array with individual events that have formatted time and date.
	 */
	public function test_get_events_valid_response() {
		add_filter( 'pre_http_request', array( $this, '_http_request_valid_response' ) );

		$response = $this->instance->get_events();

		$this->assertNotWPError( $response );
		$this->assertEqualSetsWithIndex( $this->get_user_location(), $response['location'] );
		$this->assertEquals( 'Sunday, Apr 16, 2017', $response['events'][0]['formatted_date'] );
		$this->assertEquals( '1:00 pm', $response['events'][0]['formatted_time'] );

		remove_filter( 'pre_http_request', array( $this, '_http_request_valid_response' ) );
	}

	/**
	 * Test: `get_cached_events()` should return the same data as `get_events()`, including formatted time
	 * and date values for each event.
	 */
	public function test_get_cached_events_valid_response() {
		add_filter( 'pre_http_request', array( $this, '_http_request_valid_response' ) );

		$this->instance->get_events();

		$cached_events = $this->instance->get_cached_events();

		$this->assertNotWPError( $cached_events );
		$this->assertEqualSetsWithIndex( $this->get_user_location(), $cached_events['location'] );
		$this->assertEquals( 'Sunday, Apr 16, 2017', $cached_events['events'][0]['formatted_date'] );
		$this->assertEquals( '1:00 pm', $cached_events['events'][0]['formatted_time'] );

		remove_filter( 'pre_http_request', array( $this, '_http_request_valid_response' ) );
	}

	/**
	 * Simulate an HTTP response with valid location and event data.
	 *
	 * @return array
	 */
	public function _http_request_valid_response() {
		return array(
			'headers'  => '',
			'body'     => wp_json_encode( array(
				'location' => $this->get_user_location(),
				'events'   => array(
					array(
						'type'           => 'meetup',
						'title'          => 'Flexbox + CSS Grid: Magic for Responsive Layouts',
						'url'            => 'https://www.meetup.com/Eastbay-WordPress-Meetup/events/236031233/',
						'meetup'         => 'The East Bay WordPress Meetup Group',
						'meetup_url'     => 'https://www.meetup.com/Eastbay-WordPress-Meetup/',
						'date'           => '2017-04-16 13:00:00',
						'location'       => array(
							'location'  => 'Oakland, CA, USA',
							'country'   => 'us',
							'latitude'  => 37.808453,
							'longitude' => -122.26593,
						),
					),
					array(
						'type'           => 'meetup',
						'title'          => 'Part 3- Site Maintenance - Tools to Make It Easy',
						'url'            => 'https://www.meetup.com/Wordpress-Bay-Area-CA-Foothills/events/237706839/',
						'meetup'         => 'WordPress Bay Area Foothills Group',
						'meetup_url'     => 'https://www.meetup.com/Wordpress-Bay-Area-CA-Foothills/',
						'date'           => '2017-04-26 13:30:00',
						'location'       => array(
							'location'  => 'Milpitas, CA, USA',
							'country'   => 'us',
							'latitude'  => 37.432813,
							'longitude' => -121.907095,
						),
					),
					array(
						'type'           => 'wordcamp',
						'title'          => 'WordCamp Kansas City',
						'url'            => 'https://2017.kansascity.wordcamp.org',
						'meetup'         => null,
						'meetup_url'     => null,
						'date'           => '2017-04-28 00:00:00',
						'location'       => array(
							'location'  => 'Kansas City, MO',
							'country'   => 'US',
							'latitude'  => 39.0392325,
							'longitude' => -94.577076,
						),
					),
				),
			) ),
			'response' => array(
				'code' => 200,
			),
			'cookies'  => '',
			'filename' => '',
		);
	}
}
