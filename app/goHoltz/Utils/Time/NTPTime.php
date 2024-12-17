<?php

namespace App\goHoltz\Utils\Time;

/**
 * Takes the time from any NTP server
 */
class NTPTime implements ITime
{
    /** @var string */
    public $host;

    /** @var int */
    public $port;

    /** @var int */
    public $timeout;

    /**
     * @param string $host
     * @param int $port
     * @param int $timeout
     */
    public function __construct(string $host = 'time.google.com', int $port = 123, int $timeout = 1)
    {
        if ($port <= 0 || $port > 65535) {
            throw new \Exception('Port must be 0 < port < 65535');
        }
        if ($timeout < 0) {
            throw new \Exception('Timeout must be >= 0');
        }
        $this->host = $host;
        $this->port = $port;
        $this->timeout = $timeout;
    }

    /**
     * {@inheritdoc}
     */
    public function getTime()
    {
        try {
            /* Create a socket and connect to NTP server */
            $sock = \socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
            \socket_set_option($sock, SOL_SOCKET, SO_RCVTIMEO, ['sec' => $this->timeout, 'usec' => 0]);
            \socket_connect($sock, $this->host, $this->port);

            /* Send request */
            $msg = "\010" . \str_repeat("\0", 47);
            \socket_send($sock, $msg, strlen($msg), 0);

            /* Receive response and close socket */
            if (\socket_recv($sock, $recv, 48, MSG_WAITALL) === false) {
                throw new \Exception(\socket_strerror(\socket_last_error($sock)));
            }
            \socket_close($sock);

            /* Interpret response */
            $data = \unpack('N12', $recv);
            $timestamp = (int) \sprintf('%u', $data[9]);

            /* NTP is number of seconds since 0000 UT on 1 January 1900 Unix time is seconds since 0000 UT on 1 January 1970 */
            return $timestamp - 2208988800;
        } catch (\Exception $e) {
            throw new \Exception(\sprintf('Unable to retrieve time from %s (%s)', $this->host, $e->getMessage()));
        }
    }
}
