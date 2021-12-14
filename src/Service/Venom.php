<?php
/**
 *
 */

namespace ProvidenceHealthTech\Venom\Service;

class Venom
{

    public function __construct()
    {

    }

    public function getVersion()
    {
        $sql = "SELECT DATE_FORMAT(`revision_date`,'%Y-%m-%d') as `revision_date`
                , `revision_version`
                , `name`
                , `file_checksum`
            FROM `standardized_tables_track`
            WHERE upper(`name`) = 'venom'
            ORDER BY `imported_date` DESC,
                `revision_date` DESC";
        $sqlReturn = sqlStatementNoLog($sql);

        $results = [];

        while ($row = sqlFetchArray($sqlReturn)) {
            $results[] = $row;
        }

        return $results;
    }
}
