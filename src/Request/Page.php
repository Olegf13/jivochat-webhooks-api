<?php

namespace Olegf13\Jivochat\Webhooks\Request;

use Olegf13\Jivochat\Webhooks\PopulateObjectViaArray;

/**
 * Information about a page on which the visitor (URL and page title).
 *
 * @package Olegf13\Jivochat\Webhooks\Request
 */
class Page
{
    use PopulateObjectViaArray;

    /** @var string URL of the page where the user is located (e.g. "http://example.com/"). */
    public $url;
    /** @var string Page Title (e.g. "Page title"). */
    public $title;
}