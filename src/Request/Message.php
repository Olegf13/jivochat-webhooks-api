<?php

namespace Olegf13\Jivochat\Webhooks\Request;

use Olegf13\Jivochat\Webhooks\PopulateObjectViaArray;

/**
 * Chat message.
 *
 * @package Olegf13\Jivochat\Webhooks\Request
 */
class Message
{
    use PopulateObjectViaArray;

    /** @var int Timestamp of the message receipt (e.g. null). */
    public $timestamp;
    /** @var string Message Type ("visitor" - a message from a client, "agent" - a message from an agent). */
    public $type;
    /** @var int|null Agent ID, which responded to the message (exists only if {@link type} = "agent"). */
    public $agent_id;
    /** @var string Message body (e.g. "Hi, can I ..."). */
    public $message;
    /** @var bool|null A sign that the user was added to the black list (e.g. null). */
    public $blacklisted;
}