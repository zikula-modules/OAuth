<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitbd1f4778e273b44d5745b4e75c5c4117
{
    public static $files = array (
        'a0edc8309cc5e1d60e3047b5df6b7052' => __DIR__ . '/..' . '/guzzlehttp/psr7/src/functions_include.php',
        'c964ee0ededf28c96ebd9db5099ef910' => __DIR__ . '/..' . '/guzzlehttp/promises/src/functions_include.php',
        '37a3dc5111fe8f707ab4c132ef1dbc62' => __DIR__ . '/..' . '/guzzlehttp/guzzle/src/functions_include.php',
        'b45b351e6b6f7487d819961fef2fda77' => __DIR__ . '/..' . '/jakeasmith/http_build_url/src/http_build_url.php',
    );

    public static $prefixLengthsPsr4 = array (
        'Z' => 
        array (
            'Zikula\\OAuthModule\\' => 19,
        ),
        'P' => 
        array (
            'Psr\\Http\\Message\\' => 17,
        ),
        'L' => 
        array (
            'League\\OAuth2\\Client\\' => 21,
        ),
        'G' => 
        array (
            'GuzzleHttp\\Psr7\\' => 16,
            'GuzzleHttp\\Promise\\' => 19,
            'GuzzleHttp\\' => 11,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Zikula\\OAuthModule\\' => 
        array (
            0 => __DIR__ . '/../..' . '/',
        ),
        'Psr\\Http\\Message\\' => 
        array (
            0 => __DIR__ . '/..' . '/psr/http-message/src',
        ),
        'League\\OAuth2\\Client\\' => 
        array (
            0 => __DIR__ . '/..' . '/league/oauth2-google/src',
            1 => __DIR__ . '/..' . '/league/oauth2-client/src',
            2 => __DIR__ . '/..' . '/league/oauth2-facebook/src',
            3 => __DIR__ . '/..' . '/league/oauth2-github/src',
            4 => __DIR__ . '/..' . '/league/oauth2-instagram/src',
            5 => __DIR__ . '/..' . '/league/oauth2-linkedin/src',
        ),
        'GuzzleHttp\\Psr7\\' => 
        array (
            0 => __DIR__ . '/..' . '/guzzlehttp/psr7/src',
        ),
        'GuzzleHttp\\Promise\\' => 
        array (
            0 => __DIR__ . '/..' . '/guzzlehttp/promises/src',
        ),
        'GuzzleHttp\\' => 
        array (
            0 => __DIR__ . '/..' . '/guzzlehttp/guzzle/src',
        ),
    );

    public static $prefixesPsr0 = array (
        'S' => 
        array (
            'SecurityLib' => 
            array (
                0 => __DIR__ . '/..' . '/ircmaxell/security-lib/lib',
            ),
        ),
        'R' => 
        array (
            'RandomLib' => 
            array (
                0 => __DIR__ . '/..' . '/ircmaxell/random-lib/lib',
            ),
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitbd1f4778e273b44d5745b4e75c5c4117::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitbd1f4778e273b44d5745b4e75c5c4117::$prefixDirsPsr4;
            $loader->prefixesPsr0 = ComposerStaticInitbd1f4778e273b44d5745b4e75c5c4117::$prefixesPsr0;

        }, null, ClassLoader::class);
    }
}
