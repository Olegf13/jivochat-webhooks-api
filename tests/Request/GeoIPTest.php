<?php

namespace Olegf13\Tests\Request;

use Faker\Factory;
use Olegf13\Jivochat\Webhooks\Request\GeoIP;
use PHPUnit\Framework\TestCase;

/**
 * Class GeoIPTest
 * @package Olegf13\Tests\Request.
 */
class GeoIPTest extends TestCase
{
    protected $data;

    /**
     * @inheritdoc
     */
    public function setUp()
    {
        $faker = Factory::create();
        $this->data = [
            'region_code' => $faker->stateAbbr,
            'country_code' => $faker->countryCode,
            'country' => $faker->country,
            'region' => $faker->state,
            'city' => $faker->city,
            'isp' => '',
            'latitude' => $faker->latitude,
            'longitude' => $faker->longitude,
            'organization' => $faker->company,
            'nonExistentAttribute' => $faker->word,
        ];
    }

    public function testPopulate()
    {
        $obj = new GeoIP();
        $obj->populate($this->data);

        $this->assertAttributeNotEmpty('region_code', $obj);
        $this->assertAttributeNotEmpty('country_code', $obj);
        $this->assertAttributeNotEmpty('country', $obj);
        $this->assertAttributeNotEmpty('region', $obj);
        $this->assertAttributeNotEmpty('city', $obj);
        $this->assertAttributeNotEmpty('latitude', $obj);
        $this->assertAttributeNotEmpty('longitude', $obj);
        $this->assertAttributeNotEmpty('organization', $obj);

        $this->assertAttributeEquals('', 'isp', $obj);

        $this->assertObjectNotHasAttribute('nonExistentAttribute', $obj);
    }
}