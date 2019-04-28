<?php

namespace Integreat\Gemeindeverzeichnis\Import;

use Integreat\Gemeindeverzeichnis\DatabaseConnection;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

class Homepage implements ImportInterface, LoggerAwareInterface
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
        return 'homepage';
    }

    public function getPriority() : int
    {
        return 700;
    }

    public function import()
    {
        $conn = $this->connection;
        $filename = dirname(dirname(__DIR__)) . "/data-homepages.csv";

        /**
         * Import data from Wikidata query:
         * SELECT DISTINCT ?itemLabel ?website ?zip
         * WHERE {
         * ?item wdt:P31/wdt:P279* wd:Q262166
         * OPTIONAL { ?item wdt:P856 ?website }
         * OPTIONAL { ?item wdt:P281 ?zip }
         * SERVICE wikibase:label { bd:serviceParam wikibase:language "de" }
         * }
         * ORDER BY ?itemLabel
         */
        if (($handle = fopen($filename, "r")) !== false) {
            $row = 0;
            while (($columns = fgetcsv($handle, 1000, ",", '"')) !== false) {
                $row++;
                if ($columns[1] == "") {
                    continue;
                }
                $this->logger->info("Searching for " . $columns[0]);
                $key = "";
                $stmt = $conn->prepare("SELECT `municipality_key` FROM `zip_codes` WHERE `zip`=?");
                $zip = substr($columns[2], 0, 5);
                $stmt->bind_param('s', $zip);
                $stmt->execute();
                $result = $stmt->get_result();
                if ($result->num_rows == 1) {
                    $key = $result->fetch_assoc()['municipality_key'];
                }
                $stmt->close();
                if (!$key) {
                    $stmt = $conn->prepare("SELECT `key` FROM `municipalities_core` WHERE `name`LIKE ?");
                    $search = "%" . $columns[0] . "%";
                    $stmt->bind_param('s', $search);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    if ($result->num_rows == 1) {
                        $key = $result->fetch_assoc()['key'];
                    }
                    $stmt->close();
                }
                if ($key) {
                    $stmt = $conn->prepare("INSERT INTO web_info_crawler (`key`, website_default) VALUES (?, ?) ON DUPLICATE KEY UPDATE website_default = ?");
                    $stmt->bind_param("sss", $key, $columns[1], $columns[1]);
                    if ($stmt->execute()) {
                        $this->logger->info("Updated $key $columns[0] $columns[1]");
                    }
                    $stmt->close();
                }
            }
        }
        fclose($handle);
    }
}