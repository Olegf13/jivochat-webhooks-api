<?php

namespace Olegf13\Jivochat\Webhooks\Request;

use Olegf13\Jivochat\Webhooks\PopulateObjectViaArray;

/**
 * Object with information about the operator (name, email etc).
 *
 * @package Olegf13\Jivochat\Webhooks\Request
 */
class Agent
{
    use PopulateObjectViaArray;

    /** @var string Operator ID (e.g. "3146"). */
    public $id;
    /** @var string Name of the operator (e.g. "Thomas Anderson"). */
    public $name;
    /** @var string Email of the operator (e.g. "agent@jivosite.com"). */
    public $email;
    /** @var string|null Phone of the operator (e.g. "+14083682346"). */
    public $phone;
}