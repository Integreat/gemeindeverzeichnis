<?php

namespace Integreat\Gemeindeverzeichnis\Import;

use Integreat\Gemeindeverzeichnis\DatabaseConnection;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

class Base implements ImportInterface, LoggerAwareInterface
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
        return 'base';
    }

    public function getPriority() : int
    {
        return 1000;
    }

    public function import()
    {
        $conn = $this->connection;
        $filename = dirname(dirname(__DIR__)) . "/data-base.csv";

        $content = explode("\n", file_get_contents($filename));

        /**
         *  1. Satzkennzeichen
         *  2. Textkennzeichen
         *  3. Regionalschlüssel - Land
         *  4. Regionalschlüssel - Regierungsbezirk
         *  5. Regionalschlüssel - Kreis
         *  6. Regionalschlüssel - Verwaltungsbezirk
         *  7. Regionalschlüssel - Gemeinde
         *  8. Gemeindename
         *  9. Fläche
         * 10. Bevölkerung - insgemsamt
         * 11. Bevölkerung - männlich
         * 12. Bevölkerung - weiblich
         * 13. Bevölkerung - pro km²
         * 14. PLZ
         * 15. Längengrad
         * 16. Breitengrad
         * ...
         */

        $types = array();
        $types[41] = "Kreisfreie Stadt";
        $types[42] = "Stadtkreis";
        $types[43] = "Kreis";
        $types[44] = "Landkreis";
        $types[45] = "Regionalverband";
        $types[60] = "Markt";
        $types[61] = "Kreisfreie Stadt";
        $types[62] = "Stadtkreis";
        $types[63] = "Stadt";
        $types[64] = "Kreisangehörige Gemeinde";
        $types[65] = "gemeindefreies Gebiet, bewohnt";
        $types[66] = "gemeindefreies Gebiet, unbewohnt";
        $types[67] = "große Kreisstadt";

        $state = '';
        $county = '';
        $district = '';

        foreach ($content as $row) {
            $columns = explode(";", $row);
            if ($columns[0] == '10') { //Bundesland
                $state = $columns[7];
                $county = '';
                $district = '';
            } elseif ($columns[0] == '20') { //Regierungsbezirk
                $district = $columns[7];
                $county = '';
            } elseif ($columns[0] == '40') { //Landkreise
                $county = $columns[7];
                $rs = $columns[2] . $columns[3] . $columns[4];
                $name = $columns[7];
                $type = $types[(int)$columns[1]];
                $stmt = $conn->prepare("INSERT INTO `municipalities_core`
                (`key`, `name`, `county`, `state`, `district`, `type`, `type_code`) VALUES
                ( ?   ,  ?    ,  ?      ,  ?     ,  ?        ,  ?    ,  ?         )");
                $stmt->bind_param("sssssss",
                $rs   ,  $name, $county , $state , $district , $type , $columns[1]);
            } elseif ($columns[0] == '50') { //Gemeindeverband
                // Nothing to do so far
            } elseif ($columns[0] == '60') { //Gemeinden
                $type_code = $columns[1];
                $rs = $columns[2] . $columns[3] . $columns[4] . $columns[6];
                $name = $columns[7];
                $zip = $columns[13];
                $type = $types[(int)$columns[1]];
                $pop = str_replace(" ", "", $columns[9]);
                if (!is_numeric($pop)) {
                    $pop = null;
                }
                $pop_male = str_replace(" ", "", $columns[10]);
                if (!is_numeric($pop_male)) {
                    $pop_male = null;
                }
                $pop_female = str_replace(" ", "", $columns[11]);
                if (!is_numeric($pop_female)) {
                    $pop_female = null;
                }
                $longitude = str_replace(",", ".", $columns[14]);
                $latitude = str_replace(",", ".", $columns[15]);
                $stmt = $conn->prepare("REPLACE INTO `municipalities_core`
                (`key`, `name`, `county`, `state`, `district`, `type`, `type_code`, `population`, `population_male`, `population_female`, `longitude` , `latitude`,   `area`) VALUES
                ( ?   ,  ?    ,  ?      ,  ?     ,  ?        ,  ?    ,  ?         ,  ?          ,  ?               ,  ?                 ,  ?          ,  ?        ,    ?    )");
                $stmt->bind_param("sssssssssssss",
                $rs   , $name , $county , $state , $district , $type , $columns[1], $pop        , $pop_male        , $pop_female        , $longitude  , $latitude, $columns[8]);
                if ($stmt->execute()) {
                    $stmt->close();
                    $stmt = $conn->prepare("REPLACE INTO zip_codes (`municipality_key`, `zip`) VALUES (?, ?)");
                    $stmt->bind_param("ss", $rs, $zip);
                    if ($stmt->execute()) {
                        $this->logger->info("Stored $rs");
                    } else {
                        $this->logger->warning("Failed to store ZIP code for $rs");
                    }
                    $stmt->close();
                } else {
                    $this->logger->warning("Failed to store $rs. (" . $stmt->error . ")");
                    $stmt->close();
                }
            }
        }
    }
}