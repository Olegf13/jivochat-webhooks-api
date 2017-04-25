<?php

namespace Olegf13\Jivochat\Webhooks\Request;

use Olegf13\Jivochat\Webhooks\PopulateObjectViaArray;

/**
 * User session information (IP, "user agent" etc).
 *
 * @package Olegf13\Jivochat\Webhooks\Request
 */
class Session
{
    use PopulateObjectViaArray;

    /** @var GeoIP Data from geoip. See {@link GeoIP} for details. */
    public $geoip;
    /** @var string|null utm (e.g. null). */
    public $utm;
    /** @var string IP address (e.g. "208.80.152.201"). */
    public $ip_addr;
    /** @var string User agent info. (e.g. "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/54.0.2840.71 Safari/537.36"). */
    public $user_agent;
}