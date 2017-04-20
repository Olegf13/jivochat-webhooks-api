<?php

namespace Jivochat\Webhooks;

/**
 * Class Response
 *
 * @package Jivochat\Webhook
 * @see https://www.jivochat.com/api/#webhooks
 * @see https://www.jivochat.com/api/#chat_accepted
 */
class Response
{
    /**
     * @var array Additional info about the client.
     * @see https://www.jivochat.com/api/#chat_accepted
     * @see https://www.jivochat.com/api/#setcustomdata
     */
    protected $custom_data = [];
    /**
     * @var array Contact info of the visitor.
     * @see https://www.jivochat.com/api/#chat_accepted
     * @see https://www.jivochat.com/api/#setcontactinfo
     */
    protected $contact_info = [];
    /**
     * @var bool A flag that determines the operator to display the binding key visitor to the card in CRM.
     * The button is displayed in front of all fields custom_data.
     * @see https://www.jivochat.com/api/#chat_accepted
     */
    protected $enable_assign = false;
    /**
     * @var string|null Link to the client card in CRM.
     * Displays the operator a separate button under all fields custom_data.
     * @see https://www.jivochat.com/api/#chat_accepted
     */
    protected $crm_link;
    /**
     * @var string JSON representation of Callback response data.
     */
    protected $response;

    /**
     * Setter for {@link custom_data}.
     *
     * @param string $title Title shown above a data field.
     * @param string $content Content of data field. Tags will be insulated.
     * @param string|null $link URL that opens when you click on a data field.
     * @param string|null $key Description of the data field, bold text before a colon.
     */
    public function setCustomData(string $title, string $content, string $link = null, string $key = null): void
    {
        $data = [
            'title' => $title,
            'content' => $content,
        ];
        if (null !== $key) {
            $data['key'] = $key;
        }
        if (null !== $link) {
            $data['link'] = $link;
        }

        $this->custom_data[] = $data;
    }

    /**
     * Setter for {@link contact_info}.
     *
     * @param string|null $name Client name.
     * @param string|null $email Client email.
     * @param string|null $phone Client phone number.
     * @param string|null $description Additional information about the client.
     */
    public function setContactInfo(string $name = null, string $email = null, string $phone = null, string $description = null): void
    {
        $contactInfo = [];
        if (null !== $name) {
            $contactInfo['name'] = $name;
        }
        if (null !== $email) {
            $contactInfo['email'] = $email;
        }
        if (null !== $phone) {
            $contactInfo['phone'] = $phone;
        }
        if (null !== $description) {
            $contactInfo['description'] = $description;
        }

        if (empty($contactInfo)) {
            return;
        }

        $this->contact_info = $contactInfo;
    }

    /**
     * Setter for {@link enable_assign}.
     *
     * @param bool $value
     */
    public function setEnableAssign(bool $value): void
    {
        $this->enable_assign = $value;
    }

    /**
     * Setter for {@link crm_link}.
     *
     * @param string $link
     */
    public function setCRMLink(string $link): void
    {
        $this->crm_link = $link;
    }

    /**
     * Getter for {@link custom_data}.
     *
     * @return array
     */
    public function getCustomData(): array
    {
        return $this->custom_data;
    }

    /**
     * Getter for {@link contact_info}.
     *
     * @return array
     */
    public function getContactInfo(): array
    {
        return $this->contact_info;
    }

    /**
     * Getter for {@link enable_assign}.
     *
     * @return bool
     */
    public function isEnableAssign(): bool
    {
        return $this->enable_assign;
    }

    /**
     * Getter for {@link crm_link}.
     *
     * @return null|string
     */
    public function getCrmLink()
    {
        return $this->crm_link;
    }

    /**
     * Returns Jivochat Webhook response string.
     *
     * @return string Webhook response JSON string.
     * @throws \RuntimeException
     */
    public function getResponse(): string
    {
        $this->buildResponse();

        return $this->response;
    }

    /**
     * Builds response data JSON string.
     *
     * @throws \RuntimeException in case if error occurs during encoding response to JSON.
     */
    protected function buildResponse(): void
    {
        $response = [
            'result' => 'ok',
        ];
        if (!empty($this->custom_data) || !empty($this->contact_info) || !empty($this->crm_link)) {
            $response = [
                'result' => 'ok',
                'custom_data' => $this->custom_data,
                'contact_info' => $this->contact_info,
                'enable_assign' => $this->enable_assign,
            ];

            if (null !== $this->crm_link) {
                $response['crm_link'] = $this->crm_link;
            }
        }

        $encodedResponse = json_encode($response, JSON_UNESCAPED_UNICODE);
        if (false === $encodedResponse) {
            $errorCode = json_last_error();
            $errorMsg = json_last_error_msg();
            $responseExport = var_export($response, true);
            $message = <<<MSG
An error occurred on encoding Response to JSON!
Error code - #{$errorCode}, message: "{$errorMsg}".
Export of the response content:
{$responseExport}
MSG;
            throw new \RuntimeException($message);
        }

        $this->response = $encodedResponse;
    }
}