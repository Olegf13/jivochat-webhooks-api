<?php

namespace Jivochat\Webhooks\Event;

/**
 * Class OfflineMessage
 * @package Jivochat\Webhooks\Event
 */
class OfflineMessage extends Event
{
    /** @var string Offline message ID (e.g. "2614"). */
    protected $offline_message_id;
    /** @var string Message (e.g. "Message text"). */
    protected $message;
}