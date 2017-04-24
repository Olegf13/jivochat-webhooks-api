<?php

namespace Jivochat\Webhooks\Event;

use Jivochat\Webhooks\Request\Agent;

/**
 * Class ChatUpdated
 * @package Jivochat\Webhooks\Event
 */
class ChatUpdated extends Event
{
    /** @var int Chat id (e.g. 7180). */
    protected $chat_id;
    /** @var Agent Object with information about the operator. See {@link Agent} for details. */
    protected $agent;

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