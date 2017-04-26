<?php

namespace Olegf13\Jivochat\Webhooks\Event;

use Olegf13\Jivochat\Webhooks\PopulateObjectViaArray;

/**
 * Class OfflineMessage
 * @package Olegf13\Jivochat\Webhooks\Event
 */
class OfflineMessage extends Event
{
    use PopulateObjectViaArray;

    /** @var string Offline message ID (e.g. "2614"). */
    public $offline_message_id;
    /** @var string Message (e.g. "Message text"). */
    public $message;
}