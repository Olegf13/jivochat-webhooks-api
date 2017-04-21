<?php

namespace Jivochat\Webhooks\Request;

use Jivochat\Webhooks\PopulateObjectViaArray;

/**
 * Information about a page on which the visitor (URL and page title).
 *
 * @package Jivochat\Webhooks\Request
 */
class Page
{
    use PopulateObjectViaArray;

    /** @var string URL of the page where the user is located (e.g. "http://example.com/"). */
    public $url;
    /** @var string Page Title (e.g. "Page title"). */
    public $title;
}