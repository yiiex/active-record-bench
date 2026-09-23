<?php

declare(strict_types=1);

namespace Bench\Bootstrap;

final class Yii2Bootstrap
{
    private static bool $booted = false;

    public static function boot(string $dbPath): void
    {
        if (self::$booted) {
            return;
        }
        self::$booted = true;

        // Do not let Yii2 install global error/exception handlers: this would
        // intercept warnings from the other ORMs running in the same process.
        if (!defined('YII_ENABLE_ERROR_HANDLER')) {
            define('YII_ENABLE_ERROR_HANDLER', false);
        }
        if (!defined('YII_DEBUG')) {
            define('YII_DEBUG', false);
        }

        if (!class_exists('Yii', false)) {
            $yii = dirname(__DIR__, 2) . '/vendor/yiisoft/yii2/Yii.php';
            if (!file_exists($yii)) {
                throw new \RuntimeException("Yii2 bootstrap file not found: $yii");
            }
            require_once $yii;
        }

        new \yii\console\Application([
            'id' => 'bench',
            'basePath' => dirname(__DIR__, 2),
            'components' => [
                'db' => [
                    'class' => \yii\db\Connection::class,
                    'dsn' => 'sqlite:' . $dbPath,
                ],
            ],
        ]);
    }
}
