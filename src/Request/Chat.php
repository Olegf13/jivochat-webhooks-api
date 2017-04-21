<?php

namespace Jivochat\Webhooks\Request;

use Jivochat\Webhooks\PopulateObjectViaArray;

/**
 * Holds data on completed chatting (chat rank and messages list).
 *
 * @package Jivochat\Webhooks\Request
 */
class Chat
{
    use PopulateObjectViaArray;

    /** @var Message[] Messages list. See {@link Message} for details. */
    public $messages;
    /** @var string|null User chat rank ("positive"|"negative"|null). */
    public $rate;
}