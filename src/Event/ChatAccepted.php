<?php

namespace Olegf13\Jivochat\Webhooks\Event;

use Olegf13\Jivochat\Webhooks\PopulateObjectViaArray;
use Olegf13\Jivochat\Webhooks\Request\Agent;

/**
 * Class ChatAccepted
 *
 * Event will be sent when agent clicks 'Reply'.
 *
 * All known data about visitor and some agent's info will be sent in the request parameters.
 * Also parameters including visitor's id if it was sent to the widget using `jivo_api.setUserToken`.
 *
 * If response to `chat_accepted` contains contact_info,
 * this data will be displayed to the agent as if a visitor introduced in the chat window.
 * It's also will be saved in the archive and email with the chat log.
 *
 * @package Olegf13\Jivochat\Webhooks\Event
 */
class ChatAccepted extends Event
{
    use PopulateObjectViaArray;

    /** @var int Chat id (e.g. 7180). */
    public $chat_id;
    /** @var Agent Object with information about the operator. See {@link Agent} for details. */
    public $agent;

    /**
     * Setter for {@link agent} property.
     *
     * @param Agent|array $data
     * @throws \InvalidArgumentException
     */
    public function setAgent($data)
    {
        if (is_array($data)) {
            $agent = new Agent();
            $agent->populate($data);
            $this->agent = $agent;
            return;
        }

        if ($data instanceof Agent) {
            $this->agent = $data;
            return;
        }

        throw new \InvalidArgumentException('Invalid data given.');
    }
}