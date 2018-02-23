<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit478f2749b4766991aa1921b2939f8c6b
{
    public static $files = array (
        '036ec262f4bcc331fe80b225c0cd7b46' => __DIR__ . '/..' . '/terranc/blade/src/helpers.php',
        'c594688b3441835d5575f3085da4a242' => __DIR__ . '/..' . '/webonyx/graphql-php/src/deprecated.php',
        'ddc3cd2a04224f9638c5d0de6a69c7e3' => __DIR__ . '/..' . '/topthink/think-migration/src/config.php',
    );

    public static $prefixLengthsPsr4 = array (
        't' => 
        array (
            'think\\migration\\' => 16,
            'think\\composer\\' => 15,
            'think\\' => 6,
            'terranc\\Blade\\' => 14,
        ),
        'l' => 
        array (
            'luoyt\\auth\\' => 11,
        ),
        'W' => 
        array (
            'WXPay\\' => 6,
        ),
        'P' => 
        array (
            'Phinx\\' => 6,
        ),
        'O' => 
        array (
            'OSS\\' => 4,
        ),
        'J' => 
        array (
            'JPush\\' => 6,
        ),
        'G' => 
        array (
            'GraphQL\\' => 8,
        ),
        'F' => 
        array (
            'Flc\\Dysms\\' => 10,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'think\\migration\\' => 
        array (
            0 => __DIR__ . '/..' . '/topthink/think-migration/src',
        ),
        'think\\composer\\' => 
        array (
            0 => __DIR__ . '/..' . '/topthink/think-installer/src',
        ),
        'think\\' => 
        array (
            0 => __DIR__ . '/../..' . '/thinkphp/library/think',
        ),
        'terranc\\Blade\\' => 
        array (
            0 => __DIR__ . '/..' . '/terranc/blade/src',
        ),
        'luoyt\\auth\\' => 
        array (
            0 => __DIR__ . '/..' . '/luoyt/auth/src',
        ),
        'WXPay\\' => 
        array (
            0 => __DIR__ . '/..' . '/wxpay/wxpay/src',
        ),
        'Phinx\\' => 
        array (
            0 => __DIR__ . '/..' . '/topthink/think-migration/phinx/src/Phinx',
        ),
        'OSS\\' => 
        array (
            0 => __DIR__ . '/..' . '/aliyuncs/oss-sdk-php/src/OSS',
        ),
        'JPush\\' => 
        array (
            0 => __DIR__ . '/..' . '/jpush/jpush/src/JPush',
        ),
        'GraphQL\\' => 
        array (
            0 => __DIR__ . '/..' . '/webonyx/graphql-php/src',
        ),
        'Flc\\Dysms\\' => 
        array (
            0 => __DIR__ . '/..' . '/flc/dysms/src',
        ),
    );

    public static $classMap = array (
        'think\\view\\driver\\Blade' => __DIR__ . '/..' . '/terranc/think-blade/drivers/thinkphp5/Blade.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit478f2749b4766991aa1921b2939f8c6b::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit478f2749b4766991aa1921b2939f8c6b::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit478f2749b4766991aa1921b2939f8c6b::$classMap;

        }, null, ClassLoader::class);
    }
}
