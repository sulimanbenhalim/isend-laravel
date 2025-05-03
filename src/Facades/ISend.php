<?php

namespace ISend\SMS\Facades;

use Illuminate\Support\Facades\Facade;
use ISend\SMS\Exceptions\ISendException;

/**
 * ISend SMS API Facade
 *
 * @method static \ISend\SMS\ISend to(string|array $recipient) Set the recipient(s) for the SMS
 * @method static \ISend\SMS\ISend message(string $message) Set the message content
 * @method static \ISend\SMS\ISend from(string $senderId) Set the sender ID
 * @method static \ISend\SMS\ISend scheduleAt(string $scheduleTime) Set the schedule time (Format: Y-m-d H:i)
 * @method static \ISend\SMS\ISend dltTemplateId(string $dltTemplateId) Set the DLT template ID
 * @method static \ISend\SMS\ISend send() Send the SMS message
 * @method static \ISend\SMS\ISend sendSms(string|array $recipient, string $message, ?string $senderId = null, ?string $scheduleTime = null, ?string $dltTemplateId = null) Send SMS directly without chaining
 * @method static string|null getId() Get the SMS ID (only available after sending)
 * @method static array|null getLastResponse() Get the last API response (for debugging)
 * @method static array getStatus(string $uid) Get the status of a sent SMS by ID
 * @method static array listMessages() List all messages
 * @method static array sendCampaign(string|array $contactListIds, string $message, ?string $senderId = null, ?string $scheduleTime = null, ?string $dltTemplateId = null) Send a campaign
 * @method static array getProfile() Get profile information
 * @method static array checkBalance() Check balance
 * @method static array getSenderIds() Get sender IDs
 * @method static array getTransactions() Get transactions
 * 
 * @throws ISendException When the API request fails
 * 
 * @see \ISend\SMS\ISend
 */
class ISend extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return \ISend\SMS\ISend::class;
    }
}