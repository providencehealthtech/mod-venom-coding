<?php
/**
 *
 */

namespace ProvidenceHealthTech\Venom\Service;

use PhpOffice\PhpSpreadsheet\Reader\Xlsx;

require_once "{$GLOBALS['srcdir']}/standard_tables_capture.inc";
require_once "{$GLOBALS['fileroot']}/custom/code_types.inc.php";

class Setup
{

    const VENOM_FILENAME_REGEX = "/VeNom_Data_Dictionary_V([0-9])_release_([a-z])_([a-zA-Z]{3})([0-9]{2}).xlsx/";

    const VENOM_TMP_DIR = "VENOM";

    private $externalCodeTables;

    public function __construct()
    {
        $this->externalCodeTables = [
            'venom_dx' => 'VeNom Diagnoses',
            'venom_dx_test' => 'VeNom Diagnostic Tests',
            'venom_proc' => 'VeNom Procedures',
            'venom_admin' => 'VeNom Admin Tasks',
        ];
    }

    public function install($theFile)
    {
        if ($this->isInstalled()) {
            error_log("Venom is already installed");
            return false;
        }

        if (!$this->validateFile($theFile)) {
            error_log("Could not validate the file");
            return false;
        }

        if (!$this->createExternalTable()) {
            error_log("Could not create table");
            return false;
        }

        if (!$this->addVenomCodeType()) {
            error_log("Could not create code_type");
            return false;
        }

        // Ready to install, make sure we do not timeout
        set_time_limit(0);
        if (!$this->importCodes($theFile)) {
            error_log("Could not import data");
            return false;
        }

        $versionInfo = $this->getVersionInfo($theFile);
        if (!update_tracker_table("VENOM", $versionInfo['revision'], $versionInfo['version'], $versionInfo['checksum'], true)) {
            error_log("Could not update the update_tracker_table");
            return false;
        }

        return true;
    }

    public function getVersionInfo($file)
    {
        if (preg_match(self::VENOM_FILENAME_REGEX, $file, $matches)) {
            $version = $matches[1];
            $monthStrArr = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
            $monthNum = array_search($matches[2], $monthStrArr);
            $monthNum = $monthNum + 1;
            $date_release = "20{$matches[4]}-{$monthNum}-01";
            $return = [
                'revision' => $date_release,
                'version' => $version,
                'checksum' => "0",
            ];
            return $return;
        } else {
            return false;
        }
    }

    public function isInstalled()
    {
        $sql = "SELECT * FROM standardized_tables_track WHERE name = 'venom'";
        $res = sqlStatementNoLog($sql);
        $data = [];

        while ($row = sqlFetchArray($res)) {
            $data[] = $row;
        }

        if (count($data) > 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Inject the venom code types to the code_types table
     *
     * Increment the ct_id by one and insert the venom code. This attempts to
     * avoid creating a hard dependency to core by not forcing a specific ID
     *
     * @return bool
     */
    public function addVenomCodeType()
    {
        $sql = "SELECT ct_id, ct_seq FROM code_types ORDER BY ct_id DESC LIMIT 1";
        $res = sqlStatementNoLog($sql);
        $data = [];

        while ($row = sqlFetchArray($res)) {
            $data[] = $row;
        };

        if (count($data) > 1) {
            error_log("Too many rows");
            return false;
        }

        if (count($data) == 0) {
            error_log("Count is 0");
            $nextCtId = 1;
            $nextCtSeq = 1;
        } else {
            $nextCtId = (int) $data[0]["ct_id"] + 1;
            $nextCtSeq = (int) $data[0]["ct_seq"] + 1;
        }

        foreach ($this->externalCodeTables as $table => $title) {
            $sql = 'INSERT INTO `code_types`
                (ct_key, ct_id, ct_seq, ct_mod, ct_just, ct_fee, ct_rel, ct_nofs, ct_diag, ct_active, ct_label, ct_external, ct_claim, ct_proc, ct_term, ct_problem)
                VALUES(?, ?, ?, 0, "", 1, 0, 0, 1, 1, ?, 1, 1, 1, 1, 1);';
            sqlStatementNoLog($sql, [$table, $nextCtId, $nextCtSeq, $title]);
            $nextCtId++;
            $nextCtSeq++;
        }

        return true;
    }

    /**
     * Create the external table structure
     *
     * Creates the 4 venom tables and registers the association in $ct_external_options
     *
     * @return bool
     */
    public function createExternalTable()
    {
        global $code_external_tables;
        global $ct_external_options;

        $lastCtExternalOption = array_key_last($ct_external_options);

        if (!is_int($lastCtExternalOption)) {
            error_log("Last key not an integer");
            return false;
        }

        $nextCtExternalOption = $lastCtExternalOption + 1;

        foreach ($this->externalCodeTables as $table => $title) {
            $ct_external_options[$nextCtExternalOption] = $title;
            define_external_table($code_external_tables, $nextCtExternalOption, $table, 'dict_id', 'term', 'term', ['active=1', 'approved=1']);
            $nextCtExternalOption++;
        }

        return true;
    }

    public function validateFile($file)
    {
        $mathces = [];
        if (preg_match(self::VENOM_FILENAME_REGEX, $file, $matches)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Insert the actual VeNom Codes into the database
     *
     * @return bool
     */
    public function importCodes($file)
    {
        if (!temp_copy($file, "VENOM")) {
            error_log("Unable to copy file");
            temp_dir_cleanup("venom");
        }

        $dir_venom = $GLOBALS['temporary_files_dir'] . "/" . self::VENOM_TMP_DIR . "/";
        $dir = str_replace('\\', '/', $dir_venom);

        // Settings to drastically speed up import with InnoDB
        sqlStatementNoLog("SET autocommit=0");
        sqlStatementNoLog("START TRANSACTION");
        if (is_dir($dir) && $handle = opendir($dir)) {
            while (false !== ($filename = readdir($handle))) {
                if (!stripos($filename, ".xlsx")) {
                    continue;
                }
                $xlsx = new Xlsx();
                $spreadsheet = $xlsx->load("{$dir}{$filename}");
                $dataArray = $spreadsheet->getActiveSheet()->toArray();
                // Start at $i = 1 to avoid the header row
                for ($i = 1; $i < count($dataArray); $i++) {
                    if ($dataArray[$i][3] == "0") {
                        continue; // Ignore inactive codes
                    }

                    if ($dataArray[$i][8] == "0" && $dataArray[$i][9] == "0") {
                        continue; // Ignore the row if not applicable to small or large animals
                    }

                    if ($dataArray[$i][5] == "Ignore") {
                        continue; // Ignore rows with a subset of Ignore
                    }

                    $tableName = false;
                    if ($dataArray[$i][4] == "14") {
                        $tableName = "venom_dx";
                    } elseif ($dataArray[$i][4] == "11") {
                        $tableName = "venom_dx_test";
                    } elseif ($dataArray[$i][4] == "4") {
                        $tableName = "venom_proc";
                    } elseif ($dataArray[$i][4] == "15") {
                        $tableName = "venom_admin";
                    }

                    if ($tableName) {
                        $sql = "INSERT INTO `$tableName` SET dict_id = ?, term = ?, approved = ?, active = ?, subset_id = ?, subset = ?, first_release = ?, top_level_model = ?, large = ?, small = ?, farm = ?, exotic = ?, equine = ?";
                        sqlStatementNoLog($sql, $dataArray[$i]);
                    }
                }
            }
        } else {
            error_log("Could not open {$dir} or {$dir} does not exist");
        }

        // Settings to drastically speed up import with InnoDB
        sqlStatementNoLog("COMMIT");
        sqlStatementNoLog("SET autocommit=1");

        temp_dir_cleanup(self::VENOM_TMP_DIR);

        return true;
    }
}
