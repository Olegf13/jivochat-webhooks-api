<?php

namespace Jivochat\Webhooks;

/**
 * Class Visitor
 */
class Visitor
{
    /** @var string|null Visitor name. */
    public $name;
    /** @var string|null Visitor email. */
    public $email;
    /** @var string|null Visitor phone. */
    public $phone;
    /** @var string|null Visitor number. */
    public $number;
    /** @var string|null Additional information about the client. */
    public $description;
    /** @var object|null Information about visitor's social accounts. */
    public $social;
    /** @var int|null Number of visitor's chats. */
    public $chats_count;
}