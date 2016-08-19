<?php
namespace App\Utils;
class Http
{
    /**
     * @codeCoverageIgnore
     * return string
     */
    public static function getClientIP()
    {
        $ip = "Unknown";
		if (isset($_SERVER["REMOTE_ADDR"])) {
            $ip = $_SERVER["REMOTE_ADDR"];
		}

		if (Config::get('enable_forward_detect') == "false") {
			return $ip;
		}

        if (isset($_SERVER)) {
            if (isset($_SERVER["HTTP_CF_CONNECTING_IP"])) {
                $ip = $_SERVER["HTTP_CF_CONNECTING_IP"];
            } elseif (isset($_SERVER["HTTP_X_FORWARDED_FOR"])) {
                $ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
            } elseif (isset($_SERVER["HTTP_CLIENT_IP"])) {
                $ip = $_SERVER["HTTP_CLIENT_IP"];
            }
        } else {
            if (getenv('HTTP_X_FORWARDED_FOR')) {
                $ip = getenv('HTTP_X_FORWARDED_FOR');
            } elseif (getenv('HTTP_CLIENT_IP')) {
                $ip = getenv('HTTP_CLIENT_IP');
            } else {
                $ip = getenv('REMOTE_ADDR');
            }
        }

		/*
         * 访问时用localhost访问的，读出来的是“::1”是正常情况。
         * ::1说明开启了IPv6支持，这是IPv6下的本地回环地址的表示。
         * 使用IPv4地址访问或者关闭IPv6支持都可以不显示这个。
         * */
        if (trim($ip) == "::1") {
            $ip = "127.0.0.1";
		}
        return $ip;
    }
}
