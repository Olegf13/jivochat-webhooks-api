<?php

namespace Olegf13\Tests\Request;

use Faker\Factory;
use Olegf13\Jivochat\Webhooks\Request\Agent;
use PHPUnit\Framework\TestCase;

/**
 * Class AgentTest
 * @package Olegf13\Tests\Request
 */
class AgentTest extends TestCase
{
    protected $data;

    /**
     * @inheritdoc
     */
    public function setUp()
    {
        $faker = Factory::create();
        $this->data = [
            'id' => $faker->randomNumber(),
            'name' => $faker->name,
            'email' => $faker->email,
            'phone' => $faker->phoneNumber,
            'nonExistentAttribute' => $faker->word,
        ];
    }

    public function testPopulate()
    {
        $obj = new Agent();
        $obj->populate($this->data);

        $this->assertAttributeNotEmpty('id', $obj);
        $this->assertAttributeInternalType('int', 'id', $obj);

        $this->assertAttributeNotEmpty('name', $obj);
        $this->assertAttributeNotEmpty('email', $obj);
        $this->assertAttributeNotEmpty('phone', $obj);

        $this->assertObjectNotHasAttribute('nonExistentAttribute', $obj);
    }
}