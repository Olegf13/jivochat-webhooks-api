<?php

namespace Olegf13\Jivochat\Webhooks\Event;

use Olegf13\Jivochat\Webhooks\Request\Agent;
use Olegf13\Jivochat\Webhooks\Request\Chat;

/**
 * Class ChatFinished
 * @package Olegf13\Jivochat\Webhooks\Event
 */
class ChatFinished extends Event
{
    /** @var int Chat id (e.g. 7180). */
    public $chat_id;
    /** @var Chat Data on completed chatting. See {@link Chat} for details. */
    public $chat;
    /** @var Agent[] Agents list. See {@link Agent} for details. */
    public $agents;

    /**
     * Setter for {@link agents} property.
     *
     * @param array $agents
     * @throws \InvalidArgumentException
     */
    public function setAgents(array $agents)
    {
        /** @var Agent $agent */
        foreach ($agents as $data) {
            if (!is_array($data) || !($data instanceof Agent)) {
                throw new \InvalidArgumentException('Invalid data given.');
            }

            if (is_array($data)) {
                $agent = new Agent();
                $agent->populate($data);
                $this->agents[] = $agent;
                continue;
            }

            if ($data instanceof Agent) {
                $this->agents[] = $data;
                continue;
            }
        }
    }

    /**
     * Setter for {@link chat} property.
     *
     * @param Agent|array $data
     * @throws \InvalidArgumentException
     */
    public function setChat($data)
    {
        if (is_array($data)) {
            $chat = new Chat();
            $chat->populate($data);
            $this->chat = $chat;
            return;
        }

        if ($data instanceof Chat) {
            $this->chat = $data;
            return;
        }

        throw new \InvalidArgumentException('Invalid data given.');
    }
}