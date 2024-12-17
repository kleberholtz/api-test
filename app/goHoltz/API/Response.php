<?php

namespace App\goHoltz\API;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;

/**
 * API responses.
 * 
 * @author Kleber Holtz <kleber.holtz@goholtz.com>
 * @version 1.1.2
 * 
 * @package App\goHoltz\API
 */
class Response
{
    /**
     * HTTP Status Codes.
     */
    public const HTTP_CONTINUE = 100;
    public const HTTP_SWITCHING_PROTOCOLS = 101;
    public const HTTP_PROCESSING = 102;                             // RFC2518
    public const HTTP_EARLY_HINTS = 103;                            // RFC8297
    public const HTTP_OK = 200;
    public const HTTP_CREATED = 201;
    public const HTTP_ACCEPTED = 202;
    public const HTTP_NON_AUTHORITATIVE_INFORMATION = 203;
    public const HTTP_NO_CONTENT = 204;
    public const HTTP_RESET_CONTENT = 205;
    public const HTTP_PARTIAL_CONTENT = 206;
    public const HTTP_MULTI_STATUS = 207;                           // RFC4918
    public const HTTP_ALREADY_REPORTED = 208;                       // RFC5842
    public const HTTP_IM_USED = 226;                                // RFC3229
    public const HTTP_MULTIPLE_CHOICES = 300;
    public const HTTP_MOVED_PERMANENTLY = 301;
    public const HTTP_FOUND = 302;
    public const HTTP_SEE_OTHER = 303;
    public const HTTP_NOT_MODIFIED = 304;
    public const HTTP_USE_PROXY = 305;
    public const HTTP_RESERVED = 306;
    public const HTTP_TEMPORARY_REDIRECT = 307;
    public const HTTP_PERMANENTLY_REDIRECT = 308;                   // RFC7238
    public const HTTP_BAD_REQUEST = 400;
    public const HTTP_UNAUTHORIZED = 401;
    public const HTTP_PAYMENT_REQUIRED = 402;
    public const HTTP_FORBIDDEN = 403;
    public const HTTP_NOT_FOUND = 404;
    public const HTTP_METHOD_NOT_ALLOWED = 405;
    public const HTTP_NOT_ACCEPTABLE = 406;
    public const HTTP_PROXY_AUTHENTICATION_REQUIRED = 407;
    public const HTTP_REQUEST_TIMEOUT = 408;
    public const HTTP_CONFLICT = 409;
    public const HTTP_GONE = 410;
    public const HTTP_LENGTH_REQUIRED = 411;
    public const HTTP_PRECONDITION_FAILED = 412;
    public const HTTP_REQUEST_ENTITY_TOO_LARGE = 413;
    public const HTTP_REQUEST_URI_TOO_LONG = 414;
    public const HTTP_UNSUPPORTED_MEDIA_TYPE = 415;
    public const HTTP_REQUESTED_RANGE_NOT_SATISFIABLE = 416;
    public const HTTP_EXPECTATION_FAILED = 417;
    public const HTTP_I_AM_A_TEAPOT = 418;                          // RFC2324
    public const HTTP_MISDIRECTED_REQUEST = 421;                    // RFC7540
    public const HTTP_UNPROCESSABLE_ENTITY = 422;                   // RFC4918
    public const HTTP_LOCKED = 423;                                 // RFC4918
    public const HTTP_FAILED_DEPENDENCY = 424;                      // RFC4918
    public const HTTP_TOO_EARLY = 425;                              // RFC-ietf-httpbis-replay-04
    public const HTTP_UPGRADE_REQUIRED = 426;                       // RFC2817
    public const HTTP_PRECONDITION_REQUIRED = 428;                  // RFC6585
    public const HTTP_TOO_MANY_REQUESTS = 429;                      // RFC6585
    public const HTTP_REQUEST_HEADER_FIELDS_TOO_LARGE = 431;        // RFC6585
    public const HTTP_UNAVAILABLE_FOR_LEGAL_REASONS = 451;          // RFC7725
    public const HTTP_INTERNAL_SERVER_ERROR = 500;
    public const HTTP_NOT_IMPLEMENTED = 501;
    public const HTTP_BAD_GATEWAY = 502;
    public const HTTP_SERVICE_UNAVAILABLE = 503;
    public const HTTP_GATEWAY_TIMEOUT = 504;
    public const HTTP_VERSION_NOT_SUPPORTED = 505;
    public const HTTP_VARIANT_ALSO_NEGOTIATES_EXPERIMENTAL = 506;   // RFC2295
    public const HTTP_INSUFFICIENT_STORAGE = 507;                   // RFC4918
    public const HTTP_LOOP_DETECTED = 508;                          // RFC5842
    public const HTTP_NOT_EXTENDED = 510;                           // RFC2774
    public const HTTP_NETWORK_AUTHENTICATION_REQUIRED = 511;        // RFC6585

    /**
     * Status codes translation table.
     *
     * The list of codes is complete according to the
     * {@link https://www.iana.org/assignments/http-status-codes/http-status-codes.xhtml Hypertext Transfer Protocol (HTTP) Status Code Registry}
     * (last updated 2021-10-01).
     *
     * Unless otherwise noted, the status code is defined in RFC2616.
     *
     * @var array
     */
    public static $statusTexts = [
        100 => 'Continue',
        101 => 'Switching Protocols',
        102 => 'Processing',                                        // RFC2518
        103 => 'Early Hints',
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        207 => 'Multi-Status',                                      // RFC4918
        208 => 'Already Reported',                                  // RFC5842
        226 => 'IM Used',                                           // RFC3229
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        307 => 'Temporary Redirect',
        308 => 'Permanent Redirect',                                // RFC7238
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Timeout',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Content Too Large',                                 // RFC-ietf-httpbis-semantics
        414 => 'URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Range Not Satisfiable',
        417 => 'Expectation Failed',
        418 => 'I\'m a teapot',                                     // RFC2324
        421 => 'Misdirected Request',                               // RFC7540
        422 => 'Unprocessable Content',                             // RFC-ietf-httpbis-semantics
        423 => 'Locked',                                            // RFC4918
        424 => 'Failed Dependency',                                 // RFC4918
        425 => 'Too Early',                                         // RFC-ietf-httpbis-replay-04
        426 => 'Upgrade Required',                                  // RFC2817
        428 => 'Precondition Required',                             // RFC6585
        429 => 'Too Many Requests',                                 // RFC6585
        431 => 'Request Header Fields Too Large',                   // RFC6585
        451 => 'Unavailable For Legal Reasons',                     // RFC7725
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported',
        506 => 'Variant Also Negotiates',                           // RFC2295
        507 => 'Insufficient Storage',                              // RFC4918
        508 => 'Loop Detected',                                     // RFC5842
        510 => 'Not Extended',                                      // RFC2774
        511 => 'Network Authentication Required',                   // RFC6585
    ];

    /**
     * DataStructure instance.
     * 
     * @var DataStructure
     */
    private static DataStructure $dataStructure;

    /**
     * 
     */
    private static array $params = [];

    /**
     * 
     */
    private static ?object $cache = null;

    /**
     * Get current DataStructure instance or create a new one.
     * 
     * @return DataStructure
     */
    public static function dataStructure(): DataStructure
    {
        return self::$dataStructure ??= DataStructure::create();
    }

    /**
     * Returns a response.
     * 
     * @param DataStructure $dataStructure
     * @param int $code
     * @param array $headers
     * 
     * @return JsonResponse
     */
    public static function response(DataStructure $dataStructure, int $status = self::HTTP_OK, array $headers = []): JsonResponse
    {
        $data = self::cached();

        if ($data === null) {
            $data = $dataStructure->toArray();

            if (self::$cache !== null) {
                self::setCache([
                    'data' => $data,
                    'status' => $status,
                    'headers' => $headers
                ]);

                if (!self::cache()) {
                    $response = $dataStructure->create();
                    $response->addMessage('Failed to cache response.', $response::ERROR);
                    $response->fail();

                    return response()->json($response->toArray(), self::HTTP_INTERNAL_SERVER_ERROR);
                }

                if (self::$cache->show_hit) {
                    $data['data_info'] = array_merge((array) $data['data_info'], [
                        'cache' => 'MISS',
                    ]);
                }
            }

            return response()->json($data, $status, $headers);
        }

        if ($data->show_hit) {
            $data->data['data_info'] = array_merge((array) $data->data['data_info'], [
                'cache' => 'HIT',
            ]);
        }

        return response()->json($data->data, $data->status, $data->headers);
    }

    public static function send(DataStructure $dataStructure, int $status = self::HTTP_OK, array $headers = []): JsonResponse
    {
        return self::response($dataStructure, $status, $headers);
    }

    /**
     * Returns a response from cache.
     * 
     * @return JsonResponse
     */
    public static function fromCache(): JsonResponse
    {
        $data = self::cached();

        if ($data === null) {
            $response = self::dataStructure();
            $response->addMessage('Failed to cache response.', $response::ERROR);
            $response->fail();

            return response()->json($response->toArray(), self::HTTP_INTERNAL_SERVER_ERROR);
        }

        if ($data->show_hit) {
            $data->data['data_info'] = array_merge((array) $data->data['data_info'], [
                'cache' => 'HIT',
            ]);
        }

        return response()->json($data->data, $data->status, $data->headers);
    }


    /**
     * Returns a response with a info message.
     * 
     * @param DataStructure $dataStructure
     * @param ?string $message
     * @param array $headers
     * 
     * @return JsonResponse
     */
    public static function infoResponse(DataStructure $dataStructure, string $message = null, array $headers = [], array ...$translate): JsonResponse
    {
        if ($message !== null) {
            $dataStructure->addMessage($message, $dataStructure::INFO, ...$translate);
        }

        $dataStructure->success();
        return self::response($dataStructure, self::HTTP_OK, $headers);
    }

    /**
     * Alias for infoResponse.
     * 
     * @param DataStructure $dataStructure
     * @param ?string $message
     * @param array $headers
     * 
     * @return JsonResponse
     */
    public static function info(DataStructure $dataStructure, string $message = null, array $headers = [], array ...$translate): JsonResponse
    {
        return self::infoResponse($dataStructure, $message, $headers, ...$translate);
    }

    /**
     * Return a success response.
     * 
     * @param DataStructure $dataStructure
     * @param ?string $message
     * @param array $headers
     * 
     * @return JsonResponse
     */
    public static function successResponse(DataStructure $dataStructure, mixed $data = null, array $headers = []): JsonResponse
    {
        if ($data !== null) {
            $dataStructure->setData($data);
        }

        $dataStructure->success();
        return self::response($dataStructure, self::HTTP_OK, $headers);
    }

    /**
     * Alias for successResponse.
     * 
     * @param DataStructure $dataStructure
     * @param ?string $message
     * @param array $headers
     * 
     * @return JsonResponse
     */
    public static function success(DataStructure $dataStructure, mixed $data = null, array $headers = []): JsonResponse
    {
        return self::successResponse($dataStructure, $data, $headers);
    }

    /**
     * Return a success response with a message.
     * 
     * @param DataStructure $dataStructure
     * @param ?string $message
     * @param array $headers
     * 
     * @return JsonResponse
     */
    public static function successMsgResponse(DataStructure $dataStructure, string $message = null, array $headers = [], array ...$translate): JsonResponse
    {
        if ($message !== null) {
            $dataStructure->addMessage($message, $dataStructure::SUCCESS, ...$translate);
        }

        $dataStructure->success();
        return self::response($dataStructure, self::HTTP_OK, $headers);
    }

    /**
     * Alias for successMsgResponse.
     * 
     * @param DataStructure $dataStructure
     * @param ?string $message
     * @param array $headers
     * 
     * @return JsonResponse
     */
    public static function successMsg(DataStructure $dataStructure, string $message = null, array $headers = [], array ...$translate): JsonResponse
    {
        return self::successMsgResponse($dataStructure, $message, $headers, ...$translate);
    }

    /**
     * Returns a warn response.
     * 
     * @param DataStructure $dataStructure
     * @param ?string $message
     * @param int $status
     * @param array $headers
     * 
     * @return JsonResponse
     */
    public static function warnResponse(DataStructure $dataStructure, ?string $message = null, int $status = self::HTTP_BAD_REQUEST, array $headers = [], array ...$translate): JsonResponse
    {
        if ($message !== null) {
            $dataStructure->addMessage($message, $dataStructure::WARNING, ...$translate);
        }

        $dataStructure->fail();
        return self::response($dataStructure, $status, $headers);
    }

    /**
     * Alias for warnResponse.
     * 
     * @param DataStructure $dataStructure
     * @param ?string $message
     * @param int $status
     * @param array $headers
     * 
     * @return JsonResponse
     */
    public static function warn(DataStructure $dataStructure, ?string $message = null, int $status = self::HTTP_BAD_REQUEST, array $headers = [], array ...$translate): JsonResponse
    {
        return self::warnResponse($dataStructure, $message, $status, $headers, ...$translate);
    }

    /**
     * Returns a fail response.
     * 
     * @param DataStructure $dataStructure
     * @param ?string $message
     * @param int $status
     * @param array $headers
     * 
     * @return JsonResponse
     */
    public static function failResponse(DataStructure $dataStructure, ?string $message = null, int $status = self::HTTP_BAD_REQUEST, array $headers = [], array ...$translate): JsonResponse
    {
        if ($message !== null) {
            $dataStructure->addMessage($message, $dataStructure::ERROR, ...$translate);
        }

        $dataStructure->fail();
        return self::response($dataStructure, $status, $headers);
    }

    /**
     * Alias for failResponse.
     * 
     * @param DataStructure $dataStructure
     * @param ?string $message
     * @param int $status
     * @param array $headers
     * 
     * @return JsonResponse
     */
    public static function fail(DataStructure $dataStructure, ?string $message = null, int $status = self::HTTP_BAD_REQUEST, array $headers = [], array ...$translate): JsonResponse
    {
        return self::failResponse($dataStructure, $message, $status, $headers, ...$translate);
    }

    /**
     * Check if response is cached.
     * 
     * @return bool
     */
    public static function isCached(): bool
    {
        if (self::$cache === null) {
            return false;
        }

        return Cache::has(self::$cache->key);
    }

    /**
     * Set information to be cached.
     * 
     * @return void
     */
    public static function setCache(array $data = []): self
    {
        if (!empty(self::$cache)) {
            $data = array_merge((array) self::$cache, $data);
        }

        self::$cache = (object) $data;

        return new self;
    }

    /**
     * Get cached data.
     * 
     * @return ?object
     */
    public static function cached(): ?object
    {
        if (self::$cache === null) {
            return null;
        }

        if (self::$cache->key === null) {
            return null;
        }

        return Cache::get(self::$cache->key);
    }

    /**
     * Set data to be cached.
     * 
     * @return bool
     */
    public static function cache(): bool
    {
        if (self::$cache === null) {
            return false;
        }

        $data = self::$cache;

        $key = $data->key;
        unset($data->key);
        $ttl = $data->ttl;
        unset($data->ttl);

        return Cache::put($key, $data, now()->addSeconds($ttl));
    }

    /**
     * Get Cache Data.
     * 
     * @return ?object
     */
    public static function getCache(): ?object
    {
        return self::$cache;
    }

    /**
     * Get Cache UUID.
     * 
     * @return ?string
     */
    public static function getCacheUUID(): ?string
    {
        return self::$cache->uuid ?? null;
    }

    /**
     * Get Cache Data.
     * 
     * @return ?array
     */
    public static function getCacheData(): ?array
    {
        return self::$cache->data ?? null;
    }

    /**
     * Get Cache Show Hit.
     * 
     * @return bool
     */
    public static function getCacheShowHit(): bool
    {
        return self::$cache->show_hit ?? false;
    }

    /**
     * Set Cache Show Hit to true
     * 
     * @return bool
     */
    public static function cacheShowHit(): bool
    {
        if (self::$cache === null) {
            return false;
        }

        if (self::setCache(['show_hit' => true]) instanceof self) {
            return true;
        }

        return false;
    }

    /**
     * Set Cache Show Hit to true
     * 
     * @return bool
     */
    public static function cacheTTL(int $ttl): bool
    {
        if (self::$cache === null) {
            return false;
        }

        if (self::setCache(['ttl' => $ttl]) instanceof self) {
            return true;
        }

        return false;
    }

    /**
     * Defines that the response should be cached.
     * 
     * @param ?array $params
     * @param int $ttl [optional] - Default 60
     * 
     * @return void
     */
    public static function useCache(?array $params = null, int $ttl = 60): void
    {
        $params = ($params === null ? self::$params : $params);
        $uuid = sha1(json_encode($params));
        $uri = request()->route()->uri;
        $method = request()->method();

        $key = "API:{$method}-";
        $key .= hash('crc32b', "{$uri}");
        $key .= "-{$uuid}";

        self::setCache([
            'uri' => $uri,
            'method' => $method,
            'params' => $params,
            'data' => null,
            'status' => null,
            'headers' => [],
            'show_hit' => true,
            'key' => $key,
            'ttl' => $ttl
        ]);

        return;
    }

    /**
     * Validate request data.
     * 
     * @param array $rules
     * @param array $fields
     * @param ?DataStructure $response [optional]
     * @param array $default [optional]
     * 
     * @return JsonResponse|array
     */
    public static function validate(array $rules, array $fields, ?DataStructure &$response = null, array $default = [], bool $useCache = false): JsonResponse|array
    {
        $response = $response ??= self::dataStructure();
        $validator = Validator::make($fields, $rules);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $response->addMessage($error, DataStructure::MESSAGE_TYPE_WARNING);
            }

            return self::warnResponse($response, null, self::HTTP_BAD_REQUEST);
        }

        self::$params = $validator->validated();
        foreach ($default as $key => $value) {
            if (!isset(self::$params[$key])) {
                self::$params[$key] = $value;
            }
        }

        if ($useCache) {
            self::useCache();

            if (self::isCached()) {
                return self::fromCache();
            }
        }

        return self::$params;
    }
}
