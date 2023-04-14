<?php

namespace Fintecture\Util;

class Http
{
    /**
     * Return an HTTP query string from an array of params
     *
     * @param array $params Wrapper arround http_build_query to convert booleans in strings.
     *
     * @return string HTTP query string
     */
    public static function buildHttpQuery(array $params): string
    {
        foreach ($params as $key => $value) {
            if (is_bool($value)) {
                $params[$key] = $value ? 'true' : 'false';
            }
        }

        return http_build_query($params);
    }
}
