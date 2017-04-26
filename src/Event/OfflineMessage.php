<?php

namespace Olegf13\Jivochat\Webhooks\Event;

/**
 * Class OfflineMessage
 * @package Olegf13\Jivochat\Webhooks\Event
 */
class OfflineMessage extends Event
{
    /** @var string Offline message ID (e.g. "2614"). */
    public $offline_message_id;
    /** @var string Message (e.g. "Message text"). */
    public $message;
}