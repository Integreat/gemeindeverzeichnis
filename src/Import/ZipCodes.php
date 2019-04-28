<?php

namespace Integreat\Gemeindeverzeichnis\Import;

use Integreat\Gemeindeverzeichnis\DatabaseConnection;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

class ZipCodes implements ImportInterface, LoggerAwareInterface
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
        return 'zips';
    }

    public function getPriority() : int
    {
        return 800;
    }

    public function import()
    {
        $conn = $this->connection;
        $filename = dirname(dirname(__DIR__)) . "/data-zips.csv";

        $this->logger->info("Updating zip codes from file.");
        /**
         *  1. ID
         *  2. Stadt
         *  3. PLZ
         *  4. Land
         */
        $data = array();
        $content = explode("\n", file_get_contents($filename));
        foreach ($content as $row) {
            $columns = explode(",", $row);
            if(array_key_exists($columns[0], $data)) {
                $data[$columns[0]]['zip'][] = $columns[2];
            } else {
                $data[$columns[0]]['name'] = $columns[1];
                $data[$columns[0]]['zip'] = array($columns[2]);
                $data[$columns[0]]['state'] = $columns[3];
            }
        }
        foreach($data as $city) {
            $this->logger->info("Searching for " . $city['name']);
            $key = "";
            // First search for matching ZIP code
            $bindString = str_repeat('s', count($city['zip']));
            $clause = implode(',', array_fill(0, count($city['zip']), '?'));
            $stmt = $conn->prepare("SELECT `key` FROM `municipalities_core` WHERE `address_zip` IN (".$clause.")");
            $stmt->bind_param($bindString, ...$city['zip']);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows == 1) {
                $key = $result->fetch_assoc()['key'];
            }
            $stmt->close();
            if (!$key) { // search for literal match
                $stmt = $conn->prepare("SELECT `key` FROM `municipalities_core` WHERE `name` = ?");
                $stmt->bind_param('s', $city['name']);
                $stmt->execute();
                $result = $stmt->get_result();
                if ($result->num_rows == 1) {
                    $key = $result->fetch_assoc()['key'];
                }
                $stmt->close();
            }
            if (!$key) { // search with substring
                $stmt = $conn->prepare("SELECT `key` FROM `municipalities_core` WHERE `name`LIKE ?");
                $search = "%" . $city['name'] . "%";
                $stmt->bind_param('s', $search);
                $stmt->execute();
                $result = $stmt->get_result();
                if ($result->num_rows == 1) {
                    $key = $result->fetch_assoc()['key'];
                }
                $stmt->close();
            }
            if ($key) {
                foreach($city['zip'] as $zip) {
                    $stmt = $conn->prepare("INSERT INTO zip_codes (`municipality_key`, zip) VALUES (?, ?)");
                    $stmt->bind_param("ss", $key, $zip);
                    if ($stmt->execute()) {
                        $this->logger->info("Updated $key $zip");
                    } else {
                        $this->logger->info("Failed $key $zip");
                    }
                    $stmt->close();
                }
            }
        }
    }
}