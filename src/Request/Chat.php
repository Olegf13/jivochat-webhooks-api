<?php

namespace Olegf13\Jivochat\Webhooks\Request;

use Olegf13\Jivochat\Webhooks\PopulateObjectViaArray;

/**
 * Holds data on completed chatting (chat rank and messages list).
 *
 * @package Olegf13\Jivochat\Webhooks\Request
 */
class Chat
{
    use PopulateObjectViaArray;

    /** @var Message[] Messages list. See {@link Message} for details. */
    public $messages;
    /** @var string|null User chat rank ("positive"|"negative"|null). */
    public $rate;
    /** @var bool|null A sign that the user was added to the black list (e.g. false). */
    public $blacklisted;

    /**
     * Setter for {@link messages} property.
     *
     * @param array $messages
     * @throws \InvalidArgumentException
     */
    public function setAgents(array $messages)
    {
        /** @var Agent $agent */
        foreach ($messages as $data) {
            if (!is_array($data) || !($data instanceof Message)) {
                throw new \InvalidArgumentException('Invalid data given.');
            }

            if (is_array($data)) {
                $message = new Message();
                $message->populate($data);
                $this->messages[] = $message;
                continue;
            }

            if ($data instanceof Message) {
                $this->messages[] = $data;
                continue;
            }
        }
    }
}