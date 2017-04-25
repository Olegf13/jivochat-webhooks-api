<?php

namespace Olegf13\Jivochat\Webhooks\Request;

use Olegf13\Jivochat\Webhooks\PopulateObjectViaArray;

/**
 * Information about the visitor (name, email, chats count etc).
 *
 * @package Olegf13\Jivochat\Webhooks\Request
 */
class Visitor
{
    use PopulateObjectViaArray;

    /** @var string|null Visitor name (e.g. "John Smith"). */
    public $name;
    /** @var string|null Visitor email (e.g. "email@example.com"). */
    public $email;
    /** @var string|null Visitor phone (e.g. "+14084987855"). */
    public $phone;
    /** @var int Visitor number (e.g. 2067). */
    public $number;
    /** @var string|null Additional information about the client (e.g. "Description text"). */
    public $description;
    /** @var array|null Information about visitor's social accounts (e.g. null). */
    public $social;
    /** @var int Number of visitor's chats (e.g. 5). */
    public $chats_count;
}