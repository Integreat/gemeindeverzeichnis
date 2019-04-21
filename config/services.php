<?php

use Psr\Container\ContainerInterface;
use Integreat\Gemeindeverzeichnis;

$config = parse_ini_file(__DIR__ . '/config.ini');

return $services = [
    \Psr\Log\LoggerInterface::class => function(ContainerInterface $container) {
        return $container->get(Gemeindeverzeichnis\Logger::class);
    },
    Gemeindeverzeichnis\Logger::class => function(ContainerInterface $container) use ($config) {
        return new Gemeindeverzeichnis\Logger();
    },
    Gemeindeverzeichnis\DatabaseConnection::class => function(ContainerInterface $container) use ($config) {
        return new Gemeindeverzeichnis\DatabaseConnection(
            $config['host'], $config['user'], $config['password'], $config['database']
        );
    },
    Gemeindeverzeichnis\Import\Base::class => function(ContainerInterface $container) use ($config) {
        $import = new Gemeindeverzeichnis\Import\Base(
            $container->get(Gemeindeverzeichnis\DatabaseConnection::class)
        );
        $import->setLogger($container->get(\Psr\Log\LoggerInterface::class));
        return $import;
    },
    Gemeindeverzeichnis\Import\DetailFile::class => function(ContainerInterface $container) use ($config) {
        $import = new Gemeindeverzeichnis\Import\DetailFile(
            $container->get(Gemeindeverzeichnis\DatabaseConnection::class)
        );
        $import->setLogger($container->get(\Psr\Log\LoggerInterface::class));
        return $import;
    },
    Gemeindeverzeichnis\Import\DetailWWW::class => function(ContainerInterface $container) use ($config) {
        $import = new Gemeindeverzeichnis\Import\DetailWWW(
            $container->get(Gemeindeverzeichnis\DatabaseConnection::class)
        );
        $import->setLogger($container->get(\Psr\Log\LoggerInterface::class));
        return $import;
    },
    Gemeindeverzeichnis\Import\Homepage::class => function(ContainerInterface $container) use ($config) {
        $import = new Gemeindeverzeichnis\Import\Homepage(
            $container->get(Gemeindeverzeichnis\DatabaseConnection::class)
        );
        $import->setLogger($container->get(\Psr\Log\LoggerInterface::class));
        return $import;
    },
    Gemeindeverzeichnis\Command\ImportCommand::class => function(ContainerInterface $container) use (&$services) {
        $importers = [];
        foreach (array_keys($services) as $service) {
            if (is_a($service, Gemeindeverzeichnis\Import\ImportInterface::class, true)) {
                $importers[] = $container->get($service);
            }
        }

        $import = new Gemeindeverzeichnis\Command\ImportCommand($importers);
        $import->setLogger($container->get(\Psr\Log\LoggerInterface::class));
        return $import;
    },
    \Symfony\Component\Console\Application::class => function(ContainerInterface $container) use ($config) {
        $application = new \Symfony\Component\Console\Application();
        $application->add($container->get(Gemeindeverzeichnis\Command\ImportCommand::class));
        return $application;
    }
];
