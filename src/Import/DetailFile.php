<?php

namespace Integreat\Gemeindeverzeichnis\Import;

use Integreat\Gemeindeverzeichnis\DatabaseConnection;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

class DetailFile implements ImportInterface, LoggerAwareInterface
{
    /**
     * @var DatabaseConnection
     */
    protected $connection;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    public function __construct(DatabaseConnection $connection)
    {
        $this->connection = $connection;
        $this->logger = new NullLogger();
    }

    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function getName() : string
    {
        return 'file';
    }

    public function getPriority() : int
    {
        return 900;
    }

    public function import()
    {
        $conn = $this->connection;
        $filename = dirname(dirname(__DIR__)) . "/data-station.csv";

        $this->logger->info("Updating details from file.");
        /**
         *  1. Stand
         *  2. Bundesland
         *  3. Regierungs-Bezirk
         *  4. Kreisname
         *  5. Amtl.Gemeindeschlüssel
         *  6. PLZ Gemeindenamen
         *  7. Gemeindetyp
         *  8. Anschrift der Gemeinde
         *  9. Straße
         * 10. PLZ Ort
         * 11. Fläche km2
         * 12. Einwohner gesamt
         * 13. Einwohner männlich
         * 14. Einwohner weiblich
         * 15. Einwohner je km2
         */
        $content = explode("\n", file_get_contents($filename));
        foreach ($content as $row) {
            $columns = explode(";", $row);

            if (!isset($columns[9])) {
                $this->logger->info("No City identifier found.");
                continue;
            }

            $stmt = $conn->prepare("UPDATE `municipalities_core` SET `address_recipient`=?, `address_street`=?, `address_zip`=?, `address_city`=? WHERE `key` = ?");
            $address_zip = substr($columns[9], 0, 5);
            $address_city = substr($columns[9], 6);
            $stmt->bind_param("sssss", $columns[7], $columns[8], $address_zip, $address_city, $columns[4]);
            if ($stmt->execute()) {
                $this->logger->info("Updated $columns[4]");
            }
        }
    }
}