<?php

namespace ISend\SMS;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\RequestOptions;
use Illuminate\Support\Facades\Log;
use ISend\SMS\Exceptions\ISendException;

class ISend
{
    /**
     * API message type for plain text messages.
     */
    private const MESSAGE_TYPE_PLAIN = 'plain';

    /**
     * The HTTP client instance.
     */
    protected Client $httpClient;

    /**
     * The API token.
     */
    protected string $apiToken;

    /**
     * The base URL.
     */
    protected string $baseUrl;
    
    /**
     * The API version path.
     */
    protected string $apiVersionPath;

    /**
     * The normalized API path (without leading slash).
     */
    protected string $normalizedApiPath;

    /**
     * The default sender ID.
     */
    protected string $defaultSenderId;

    /**
     * The recipient(s) for the SMS.
     *
     * @var string|array|null
     */
    protected $recipient = null;

    /**
     * The message content.
     */
    protected ?string $message = null;

    /**
     * The sender ID.
     */
    protected ?string $senderId = null;

    /**
     * The schedule time.
     */
    protected ?string $scheduleTime = null;

    /**
     * The DLT template ID.
     */
    protected ?string $dltTemplateId = null;

    /**
     * The SMS unique ID (after sending).
     */
    protected ?string $smsId = null;
    
    /**
     * Store the last API response for debugging.
     */
    protected ?array $lastResponse = null;

    /**
     * Create a new ISend instance.
     */
    public function __construct(string $apiToken, string $baseUrl, string $apiVersionPath, string $defaultSenderId)
    {
        $this->apiToken = $apiToken;
        $this->baseUrl = $baseUrl;
        $this->apiVersionPath = $apiVersionPath;
        $this->normalizedApiPath = ltrim($apiVersionPath, '/');
        $this->defaultSenderId = $defaultSenderId;
        
        // Ensure the base URL ends with a trailing slash for proper path resolution
        $baseUri = rtrim($this->baseUrl, '/') . '/';
        
        $this->httpClient = new Client([
            'base_uri' => $baseUri,
            'headers' => [
                'Authorization' => "Bearer {$this->apiToken}",
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ],
            // Add reasonable timeouts to prevent hanging requests
            RequestOptions::CONNECT_TIMEOUT => 10,
            RequestOptions::TIMEOUT => 30,
            // Don't throw exceptions for 4xx and 5xx responses, handle them manually
            RequestOptions::HTTP_ERRORS => false,
        ]);
    }

    /**
     * Set the recipient(s) for the SMS.
     *
     * @param string|array $recipient Single phone number or array of phone numbers
     */
    public function to($recipient): self
    {
        $this->recipient = $recipient;
        
        return $this;
    }

    /**
     * Set the message content.
     */
    public function message(string $message): self
    {
        $this->message = $message;
        
        return $this;
    }

    /**
     * Set the sender ID.
     */
    public function from(string $senderId): self
    {
        $this->senderId = $senderId;
        
        return $this;
    }

    /**
     * Set the schedule time.
     *
     * @param string $scheduleTime Format: Y-m-d H:i
     */
    public function scheduleAt(string $scheduleTime): self
    {
        $this->scheduleTime = $scheduleTime;
        
        return $this;
    }

    /**
     * Set the DLT template ID.
     */
    public function dltTemplateId(string $dltTemplateId): self
    {
        $this->dltTemplateId = $dltTemplateId;
        
        return $this;
    }

    /**
     * Send the SMS message.
     *
     * @throws ISendException If the API request fails
     */
    public function send(): self
    {
        $this->validateSendRequest();
        $payload = $this->prepareSendPayload();
        
        try {
            $this->logDebugInfo('sms/send', $payload);
            
            $response = $this->httpClient->post("{$this->normalizedApiPath}/sms/send", [
                'json' => $payload,
            ]);

            $statusCode = $response->getStatusCode();
            $responseBody = $response->getBody()->getContents();
            $data = json_decode($responseBody, true);
            
            // Store response data for debugging
            $this->lastResponse = $data;
            
            // Log API response if debug mode is enabled
            $this->logApiResponse($statusCode, $responseBody, $data);
            
            // Check for successful status code
            if ($statusCode >= 400) {
                throw new ISendException(
                    $data['message'] ?? 'API request failed with status code ' . $statusCode,
                    $statusCode,
                    $data,
                    null,
                    $payload
                );
            }
            
            // Store SMS ID if available
            if (isset($data['data']['uid'])) {
                $this->smsId = $data['data']['uid'];
            }

            return $this;
        } catch (GuzzleException $e) {
            $this->handleGuzzleException($e, 'Failed to send SMS', $payload);
        }
    }

    /**
     * Validate required parameters for sending an SMS.
     *
     * @throws ISendException If validation fails
     */
    protected function validateSendRequest(): void
    {
        if (empty($this->recipient)) {
            throw new ISendException('Recipient is required');
        }

        if (empty($this->message)) {
            throw new ISendException('Message content is required');
        }
    }

    /**
     * Prepare payload for sending an SMS.
     */
    protected function prepareSendPayload(): array
    {
        $recipientString = is_array($this->recipient)
            ? implode(',', $this->recipient)
            : $this->recipient;
            
        $payload = [
            'recipient' => $recipientString,
            'sender_id' => $this->senderId ?? $this->defaultSenderId,
            'type' => self::MESSAGE_TYPE_PLAIN,
            'message' => $this->message,
        ];

        if ($this->scheduleTime) {
            $payload['schedule_time'] = $this->scheduleTime;
        }

        if ($this->dltTemplateId) {
            $payload['dlt_template_id'] = $this->dltTemplateId;
        }
        
        return $payload;
    }

    /**
     * Alias for the send method for direct usage.
     *
     * @param string|array $recipient Single phone number or array of phone numbers
     * @throws ISendException If the API request fails
     */
    public static function sendSms(
        $recipient,
        string $message,
        ?string $senderId = null,
        ?string $scheduleTime = null,
        ?string $dltTemplateId = null
    ): self {
        $instance = app(ISend::class);
        
        $instance->to($recipient)->message($message);
        
        if ($senderId) {
            $instance->from($senderId);
        }
        
        if ($scheduleTime) {
            $instance->scheduleAt($scheduleTime);
        }
        
        if ($dltTemplateId) {
            $instance->dltTemplateId($dltTemplateId);
        }
        
        return $instance->send();
    }

    /**
     * Get the SMS ID (only available after sending).
     */
    public function getId(): ?string
    {
        return $this->smsId;
    }
    
    /**
     * Get the last API response (for debugging).
     */
    public function getLastResponse(): ?array
    {
        return $this->lastResponse;
    }

    /**
     * Get the status of a sent SMS by ID.
     *
     * @throws ISendException If the API request fails
     */
    public function getStatus(string $uid): array
    {
        return $this->makeApiGetRequest("sms/{$uid}", 'Failed to get SMS status');
    }

    /**
     * List all messages.
     *
     * @throws ISendException If the API request fails
     */
    public function listMessages(): array
    {
        return $this->makeApiGetRequest('sms', 'Failed to list messages');
    }

    /**
     * Send a campaign.
     *
     * @param string|array $contactListIds Contact list ID or array of IDs
     * @throws ISendException If the API request fails
     */
    public function sendCampaign(
        $contactListIds,
        string $message,
        ?string $senderId = null,
        ?string $scheduleTime = null,
        ?string $dltTemplateId = null
    ): array {
        $contactListString = is_array($contactListIds)
            ? implode(',', $contactListIds)
            : $contactListIds;
            
        $payload = [
            'contact_list_id' => $contactListString,
            'sender_id' => $senderId ?? $this->defaultSenderId,
            'type' => self::MESSAGE_TYPE_PLAIN,
            'message' => $message,
        ];

        if ($scheduleTime) {
            $payload['schedule_time'] = $scheduleTime;
        }

        if ($dltTemplateId) {
            $payload['dlt_template_id'] = $dltTemplateId;
        }

        try {
            $this->logDebugInfo('sms/campaign', $payload);
            
            $response = $this->httpClient->post("{$this->normalizedApiPath}/sms/campaign", [
                'json' => $payload,
            ]);
            
            $statusCode = $response->getStatusCode();
            $responseBody = $response->getBody()->getContents();
            $data = json_decode($responseBody, true);
            
            $this->logApiResponse($statusCode, $responseBody, $data);
            
            if ($statusCode >= 400) {
                throw new ISendException(
                    $data['message'] ?? 'API request failed with status code ' . $statusCode,
                    $statusCode,
                    $data,
                    null,
                    $payload
                );
            }
            
            return $data;
        } catch (GuzzleException $e) {
            $this->handleGuzzleException($e, 'Failed to send campaign', $payload);
        }
    }

    /**
     * Get profile information.
     *
     * @throws ISendException If the API request fails
     */
    public function getProfile(): array
    {
        return $this->makeApiGetRequest('me', 'Failed to get profile information');
    }

    /**
     * Check balance.
     *
     * @throws ISendException If the API request fails
     */
    public function checkBalance(): array
    {
        return $this->makeApiGetRequest('balance', 'Failed to check balance');
    }

    /**
     * Get sender IDs.
     *
     * @throws ISendException If the API request fails
     */
    public function getSenderIds(): array
    {
        return $this->makeApiGetRequest('senderid', 'Failed to get sender IDs');
    }

    /**
     * Get transactions.
     *
     * @throws ISendException If the API request fails
     */
    public function getTransactions(): array
    {
        return $this->makeApiGetRequest('transactions', 'Failed to get transactions');
    }
    
    /**
     * Make a GET request to the API.
     *
     * @throws ISendException If the API request fails
     */
    protected function makeApiGetRequest(string $endpoint, string $errorMessage): array
    {
        $requestData = ['endpoint' => $endpoint];
        
        try {
            $this->logDebugInfo($endpoint);
            
            $response = $this->httpClient->get("{$this->normalizedApiPath}/{$endpoint}");
            
            $statusCode = $response->getStatusCode();
            $responseBody = $response->getBody()->getContents();
            $data = json_decode($responseBody, true);
            
            $this->logApiResponse($statusCode, $responseBody, $data);
            
            if ($statusCode >= 400) {
                throw new ISendException(
                    $data['message'] ?? 'API request failed with status code ' . $statusCode,
                    $statusCode,
                    $data,
                    null,
                    $requestData
                );
            }
            
            return $data;
        } catch (GuzzleException $e) {
            $this->handleGuzzleException($e, $errorMessage, $requestData);
        }
    }
    
    /**
     * Handle exceptions from Guzzle HTTP client.
     *
     * @throws ISendException Always thrown with details from the Guzzle exception
     */
    protected function handleGuzzleException(GuzzleException $e, string $defaultMessage, ?array $requestData = null): never
    {
        $response = method_exists($e, 'getResponse') ? $e->getResponse() : null;
        $statusCode = $response ? $response->getStatusCode() : 0;
        $responseData = null;
        $message = $defaultMessage;
        
        if ($response) {
            try {
                $responseData = json_decode($response->getBody()->getContents(), true);
                $message = $responseData['message'] ?? $message;
            } catch (\Throwable $th) {
                // If we can't parse the response, just use the default message
            }
        }
        
        // Log the exception with context for debugging
        if (app()->hasDebugModeEnabled()) {
            Log::error('ISend SMS API Exception', [
                'message' => $message,
                'status_code' => $statusCode,
                'response_data' => $responseData,
                'request_data' => $requestData,
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
        
        throw new ISendException(
            $message,
            $statusCode,
            $responseData,
            $e,
            $requestData
        );
    }
    
    /**
     * Log debug information about the API request.
     */
    protected function logDebugInfo(string $endpoint, array $payload = []): void
    {
        if (!app()->hasDebugModeEnabled()) {
            return;
        }
        
        $debugData = [
            'base_url' => $this->baseUrl,
            'api_version_path' => $this->apiVersionPath,
            'normalized_api_path' => $this->normalizedApiPath,
            'endpoint' => $endpoint,
            'full_endpoint' => "{$this->normalizedApiPath}/{$endpoint}",
            'request_url' => rtrim($this->baseUrl, '/') . "/{$this->normalizedApiPath}/{$endpoint}",
            'headers' => [
                'Authorization' => 'Bearer ' . substr($this->apiToken, 0, 5) . '...',
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ],
        ];
        
        if (!empty($payload)) {
            $debugData['payload'] = $payload;
        }
        
        Log::debug('ISend SMS API Request', $debugData);
    }
    
    /**
     * Log API response for debugging.
     */
    protected function logApiResponse(int $statusCode, string $responseBody, ?array $data): void
    {
        if (!app()->hasDebugModeEnabled()) {
            return;
        }
        
        Log::debug('ISend SMS API Response', [
            'status_code' => $statusCode,
            'body' => substr($responseBody, 0, 1000) . (strlen($responseBody) > 1000 ? '...' : ''),
            'parsed_data' => $data,
        ]);
    }
}