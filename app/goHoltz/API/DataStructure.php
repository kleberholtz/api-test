<?php

namespace App\goHoltz\API;

use Illuminate\Support\Facades\Log;

/**
 * Data structure for API responses.
 * 
 * @author Kleber Holtz <kleber.holtz@goholtz.com>
 * 
 * @package App\goHoltz\API
 */
class DataStructure
{
    /**
     * Info message type.
     */
    const INFO = 0;
    const MESSAGE_INFO = 0;
    const MESSAGE_TYPE_INFO = 0;

    /**
     * Success message type.
     */
    const OK = 1;
    const SUCCESS = 1;
    const MESSAGE_SUCCESS = 1;
    const MESSAGE_TYPE_SUCCESS = 1;

    /**
     * Warning message type.
     */
    const WARN = 2;
    const WARNING = 2;
    const MESSAGE_WARNING = 2;
    const MESSAGE_TYPE_WARNING = 2;

    /**
     * Error message type.
     */
    const FAIL = 3;
    const ERROR = 3;
    const MESSAGE_ERROR = 3;
    const MESSAGE_TYPE_ERROR = 3;

    /**
     * Debug message type.
     */
    const DEBUG = 4;
    const MESSAGE_DEBUG = 4;
    const MESSAGE_TYPE_DEBUG = 4;

    /**
     * Message types.
     * 
     * @var array
     */
    public static array $messageTypes = [
        self::INFO => 'info',
        self::SUCCESS => 'success',
        self::WARNING => 'warn',
        self::ERROR => 'error',
        self::DEBUG => 'debug',
    ];

    /**
     * @var bool
     */
    private bool $success = false;

    /**
     * @var array
     */
    private array $messages = [];

    /**
     * @var mixed
     */
    private mixed $data = null;

    /**
     * @var ?object
     */
    private ?object $data_info = null;

    /**
     * Call a method statically.
     * 
     * @param string $name
     * @param array $arguments
     * 
     * @return mixed
     */
    public static function __callStatic(string $name, array $arguments): mixed
    {
        $response = new self();

        if (method_exists($response, $name)) {
            return call_user_func_array([$response, $name], $arguments);
        }

        return null;
    }

    /**
     * Create a new instance of the class.
     * 
     * @return self
     */
    public static function create(): self
    {
        return new self();
    }

    /**
     * Add a message to the response.
     * 
     * @param string $message
     * @param int $type
     * @param array $translate [array $replace, ?string $locale = null]
     * 
     * @return void
     */
    public function addMessage(string $message, int $type = self::INFO, array ...$translate): void
    {
        if (!in_array($type, array_keys(self::$messageTypes))) {
            $type = self::INFO;
        }

        /**
         * Debug messages are only added if the app is in debug mode.
         */
        if ($type === self::DEBUG) {
            if (config('app.debug') === false) {
                return;
            }

            Log::debug($message, $translate[0] ?? []);
        }

        $this->messages[] = (object) [
            'type' => self::$messageTypes[$type],
            'message' => __($message, ...$translate)
        ];

        return;
    }

    /**
     * Add multiple messages to the response.
     * 
     * @param array $messages
     * @param array $translate [array $replace, ?string $locale = null]
     * 
     * @return void
     */
    public function addMessages(array $messages, array ...$translate): void
    {
        foreach ($messages as $message) {
            $type = key($message);
            $msg = current($message);

            $this->addMessage($msg, $type, ...$translate);
        }

        return;
    }

    /**
     * Set the data of the response.
     * 
     * @param mixed $data
     * 
     * @return void
     */
    public function setData(mixed $data): void
    {
        $this->data = $data;

        return;
    }

    /**
     * Set the data info of the response.
     * 
     * @param array $data
     * 
     * @return void
     */
    public function setDataInfo(array $data_info): void
    {
        $this->data_info = (object) $data_info;

        return;
    }

    /**
     * Set the response as successful.
     * 
     * @return void
     */
    public function success(): void
    {
        $this->success = true;

        return;
    }

    /**
     * Set the response as failed.
     * 
     * @return void
     */
    public function fail(): void
    {
        $this->success = false;

        return;
    }

    /**
     * Get the response as an array.
     * 
     * @return array
     */
    public function toArray(): array
    {
        $array = [
            'success' => $this->success,
            'messages' => $this->messages,
            'data' => $this->data
        ];

        if ($this->data_info !== null) {
            $array['data_info'] = $this->data_info;
        }

        return $array;
    }

    /**
     * Get the response as an object.
     * 
     * @return object
     */
    public function toObject(): object
    {
        return (object) $this->toArray();
    }

    /**
     * Get the response as a JSON string.
     * 
     * @return string|false
     */
    public function toJson(): string|false
    {
        return @json_encode($this->toArray());
    }
}
