<?php

namespace Jivochat\Webhooks\Request;

use Jivochat\Webhooks\PopulateObjectViaArray;

/**
 * Geo data about the user (country, city, coordinates etc).
 *
 * @package Jivochat\Webhooks\Request
 */
class GeoIP
{
    use PopulateObjectViaArray;

    /** @var string Area code (e.g. "CA"). */
    public $region_code;
    /** @var string ISO country code (e.g. "US"). */
    public $country_code;
    /** @var string Country name (e.g. "United States"). */
    public $country;
    /** @var string Region (e.g. "California"). */
    public $region;
    /** @var string City (e.g. "San Francisco"). */
    public $city;
    /** @var string Unknown, empty string (e.g. ""). */
    public $isp;
    /** @var string Latitude (e.g. "37.7898"). */
    public $latitude;
    /** @var string Longitude (e.g. "-122.3942"). */
    public $longitude;
    /** @var string Company name (e.g. "Wikimedia Foundation"). */
    public $organization;
}