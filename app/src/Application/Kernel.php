<?php

declare(strict_types=1);

namespace App\Application;

use App\Application\Bootloader\AppBootloader;
use App\Application\Bootloader\AttributesBootloader;
use App\Application\Bootloader\AuthBootloader;
use App\Application\Bootloader\BroadcastingBootloader;
use App\Application\Bootloader\HttpHandlerBootloader;
use App\Application\Bootloader\MongoDBBootloader;
use App\Application\Bootloader\PersistenceBootloader;
use Modules\Events\Application\EventsBootloader;
use Modules\Inspector\Application\InspectorBootloader;
use Modules\Profiler\Application\ProfilerBootloader;
use Modules\Projects\Application\ProjectBootloader;
use Modules\Ray\Application\RayBootloader;
use Modules\HttpDumps\Application\HttpDumpsBootloader;
use Modules\Sentry\Application\SentryBootloader;
use Modules\Smtp\Application\SmtpBootloader;
use Modules\VarDumper\Application\VarDumperBootloader;
use Modules\Webhooks\Application\WebhooksBootloader;
use Spiral\Boot\Bootloader\CoreBootloader;
use Spiral\Bootloader as Framework;
use Spiral\Cycle\Bootloader as CycleBridge;
use Spiral\Distribution\Bootloader\DistributionBootloader;
use Spiral\DotEnv\Bootloader\DotenvBootloader;
use Spiral\Monolog\Bootloader\MonologBootloader;
use Spiral\RoadRunnerBridge\Bootloader as RoadRunnerBridge;
use Spiral\Serializer\Symfony\Bootloader\SerializerBootloader;
use Spiral\Stempler\Bootloader\StemplerBootloader;
use Spiral\Storage\Bootloader\StorageBootloader;
use Spiral\Tokenizer\Bootloader\TokenizerListenerBootloader;
use Spiral\Validation\Bootloader\ValidationBootloader;
use Spiral\Validator\Bootloader\ValidatorBootloader;

class Kernel extends \Spiral\Framework\Kernel
{
    protected const SYSTEM = [
        CoreBootloader::class,
        AttributesBootloader::class,
        TokenizerListenerBootloader::class,
        DotenvBootloader::class,
    ];

    protected function defineBootloaders(): array
    {
        return [
            Bootloader\ExceptionHandlerBootloader::class,

            // RoadRunner
            RoadRunnerBridge\CacheBootloader::class,
            RoadRunnerBridge\HttpBootloader::class,
            RoadRunnerBridge\QueueBootloader::class,
            RoadRunnerBridge\TcpBootloader::class,
            RoadRunnerBridge\LoggerBootloader::class,

            MonologBootloader::class,

            // Core Services
            Framework\SnapshotsBootloader::class,

            // Security and validation
            Framework\Security\EncrypterBootloader::class,
            Framework\Security\FiltersBootloader::class,
            ValidationBootloader::class,
            ValidatorBootloader::class,

            StemplerBootloader::class,

            // Console commands
            Framework\CommandBootloader::class,
            CycleBridge\CommandBootloader::class,
            RoadRunnerBridge\CommandBootloader::class,

            // Configure route groups, middleware for route groups
            Bootloader\RoutesBootloader::class,

            StorageBootloader::class,
            DistributionBootloader::class,
            SerializerBootloader::class,
            BroadcastingBootloader::class,

            HttpHandlerBootloader::class,
            AppBootloader::class,
            InspectorBootloader::class,
            SentryBootloader::class,
            SmtpBootloader::class,
            VarDumperBootloader::class,
            RayBootloader::class,
            HttpDumpsBootloader::class,
            ProfilerBootloader::class,
            MongoDBBootloader::class,
            PersistenceBootloader::class,
            AuthBootloader::class,
            WebhooksBootloader::class,
            ProjectBootloader::class,
            EventsBootloader::class,
        ];
    }
}
