<?php

namespace App\goHoltz\Utils;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;

class Functions
{
    /**
     * Safe comparison of 2 hash (Timing attack safe string comparison).
     * 
     * Timing Attack: Normally, a string comparison (strcmp or ==) breaks on a 
     * first non-matching char, so if your password is 12345 and the attacker 
     * provides 9xxxx and then 1xxxx, she can measure the time difference between 
     * two comparisons and deduce that the second string is more correct (since the 
     * second comparison took more time). hash_equals eliminates this type of attack 
     * by always comparing all characters of both strings, not matter if they match 
     * or not. So more and less correct strings will take the same time.
     * 
     * @param string $known_string
     * @param string $user_string
     * 
     * @return bool
     */
    public static function isSafeHashEquals(string $known_hash, string $user_hash): bool
    {
        if (\function_exists("hash_equals")) {
            return \hash_equals($known_hash, $user_hash);
        }

        // In general, it's not possible to prevent length leaks. So it's OK to leak the length. The important part is that
        // we don't leak information about the difference of the two strings.
        if (\strlen($known_hash) === \strlen($user_hash)) {
            $result = 0;
            for ($i = 0; $i < \strlen($known_hash); $i++) {
                $result |= (\ord($known_hash[$i]) ^ \ord($user_hash[$i]));
            }
            // They are only identical strings if $result is exactly 0...
            return $result === 0;
        }
        return false;
    }

    /**
     * Safe comparison of 2 strings.
     * 
     * @param string $known_string
     * @param string $user_string
     * 
     * @return bool
     */
    public static function isSafeStringEquals(string $string1, string $string2): bool
    {
        if (!\function_exists("strcmp")) {
            throw new \Exception("The function strcmp() is not exists.");
        }

        return \strcmp($string1, $string2) === 0;
    }

    /**
     * Determine if a string contains a given substring
     * 
     * @param string $haystack
     * @param string $needle
     * 
     * @return bool
     */
    public static function isStringContains(string $haystack, string $needle): bool
    {
        if (\function_exists("str_contains")) {
            return \str_contains($haystack, $needle);
        }

        return \strpos($haystack, $needle) !== false;
    }

    /**
     * Transform a bytes to a human readable format.
     * 
     * @param int $bytes
     * 
     * @return string
     */
    public static function doHumanBytesReadable(int $bytes): string
    {
        if ($bytes === 0) {
            return '0B';
        }

        return \round($bytes / \pow(1024, ($i = \floor(\log($bytes, 1024)))), 2) . ['B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB'][$i];
    }

    /**
     * Check if a string is a valid json.
     * 
     * @param string $json
     * @param mixed $result
     * 
     * @return bool
     */
    public static function isValidJson(string $json, mixed &$result = null): bool
    {
        try {
            $decoded = json_decode($json);

            if ($decoded === null && json_last_error() !== JSON_ERROR_NONE) {
                return false;
            }

            $result = $decoded;
            return true;
        } catch (\Throwable $e) {
            // Lidar com qualquer exceção que possa ocorrer durante a decodificação
            return false;
        }
    }

    /**
     * Calculate the area of a circle.
     * 
     * @param float $radius
     * 
     * @return float
     */
    public static function doCalculateCircleArea(float $radius): float
    {
        return \M_PI * $radius * $radius;
    }

    /**
     * Calculates the great-circle distance between two points, with the Haversine formula.
     * 
     * @param float $latitudeFrom Latitude of start point in [deg decimal]
     * @param float $longitudeFrom Longitude of start point in [deg decimal]
     * @param float $latitudeTo Latitude of target point in [deg decimal]
     * @param float $longitudeTo Longitude of target point in [deg decimal]
     * @param float $earthRadius Mean earth radius in [m]
     * 
     * @return float Distance between points in [m] (same as earthRadius)
     */
    public static function haversineGreatCircleDistance(float $latitudeFrom, float $longitudeFrom, float $latitudeTo, float $longitudeTo, float $earthRadius = 6371000): float
    {
        $latFrom = \deg2rad($latitudeFrom);
        $lonFrom = \deg2rad($longitudeFrom);
        $latTo = \deg2rad($latitudeTo);
        $lonTo = \deg2rad($longitudeTo);
        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;
        $angle = 2 * \asin(\sqrt(\pow(\sin($latDelta / 2), 2) + \cos($latFrom) * \cos($latTo) * \pow(\sin($lonDelta / 2), 2)));
        return $angle * $earthRadius;
    }

    /**
     * Get the current microtime.
     * 
     * @return float
     */
    public static function getMicrotime(): float
    {
        list($u, $s) = \explode(" ", \microtime());
        return \str_pad(\str_replace('.', '', $u + $s), \strlen($s) + 4, 0);
    }

    /**
     * 
     * @param string $content
     * 
     * @return string
     */
    public static function UUIDByContent(string $content): string
    {
        $hash = sha1($content);

        $uuid = substr($hash, 0, 8) . '-';
        $uuid .= substr($hash, 8, 4) . '-';
        $uuid .= substr($hash, 12, 4) . '-';
        $uuid .= substr($hash, 16, 4) . '-';
        $uuid .= substr($hash, 20, 12);

        return $uuid;
    }
}
