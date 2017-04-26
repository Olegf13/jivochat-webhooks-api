<?php

namespace Olegf13\Jivochat\Webhooks\Event;

use Olegf13\Jivochat\Webhooks\PopulateObjectViaArray;
use Olegf13\Jivochat\Webhooks\Request\Agent;

/**
 * Class ChatAssigned
 *
 * Event will be sent when a chat connects to CRM using the parameter "crm_link" from reply on Chat Accepted.
 *
 * All known data about visitor and some agent's info will be sent in the request parameters.
 * Also parameters including visitor's id if it was sent to the widget using `jivo_api.setUserToken`.
 *
 * In response we expect only `{"result": "ok or an error message"}`.
 *
 * @package Olegf13\Jivochat\Webhooks\Event
 */
class ChatAssigned extends Event
{
    use PopulateObjectViaArray;

    /** @var int Chat id (e.g. 7180). */
    public $chat_id;
    /** @var Agent Object with information about the operator. See {@link Agent} for details. */
    public $agent;
    /** @var string CRM link from the event Chat_accepted (e.g. "..."). */
    public $assign_to;

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