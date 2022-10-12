<?php
declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';

$logHandler = new \Monolog\Handler\TestHandler();
$logger = new \Monolog\Logger('app', [$logHandler], [new \Monolog\Processor\PsrLogMessageProcessor()]);

$eventManager = new \Doctrine\Common\EventManager();

$dbalConfig = new \Doctrine\DBAL\Configuration();
$dbalConfig->setMiddlewares([new \Doctrine\DBAL\Logging\Middleware($logger)]);

$ormConfig = new \Doctrine\ORM\Configuration();
$ormConfig->setProxyDir(__DIR__ . '/proxies');
$ormConfig->setProxyNamespace('Proxies');
$ormConfig->setAutoGenerateProxyClasses(true);
$ormConfig->setMetadataDriverImpl(new \Doctrine\ORM\Mapping\Driver\AttributeDriver([__DIR__ . '/src/Entity']));

$em = \Doctrine\ORM\EntityManager::create(
    \Doctrine\DBAL\DriverManager::getConnection(
        [
            'driver' => 'pdo_sqlite',
            'path' => __DIR__ . '/db.sqlite',
        ],
        $dbalConfig,
        $eventManager
    ),
    $ormConfig,
    $eventManager
);

$provider = new \Doctrine\ORM\Tools\Console\EntityManagerProvider\SingleManagerProvider($em);

\Doctrine\ORM\Tools\Console\ConsoleRunner::createApplication(
    $provider,
    [
        new \App\Command\SeedDatabaseCommand($provider),
        new \App\Command\UpdateAuctionNameCommand($logHandler, $provider),
    ]
)->run();
