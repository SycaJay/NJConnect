<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInita1cdf9466dd3a8aa18eb550fac2ee40f
{
    public static $prefixLengthsPsr4 = array (
        'P' => 
        array (
            'PHPMailer\\PHPMailer\\' => 20,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'PHPMailer\\PHPMailer\\' => 
        array (
            0 => __DIR__ . '/..' . '/phpmailer/phpmailer/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInita1cdf9466dd3a8aa18eb550fac2ee40f::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInita1cdf9466dd3a8aa18eb550fac2ee40f::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInita1cdf9466dd3a8aa18eb550fac2ee40f::$classMap;

        }, null, ClassLoader::class);
    }
}
