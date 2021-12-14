<?php
/**
 *
 */

namespace ProvidenceHealthTech\Venom\Controller;

use ProvidenceHealthTech\Venom\Controller\Controller;
use ProvidenceHealthTech\Venom\Controller\ControllerInterface;
use ProvidenceHealthTech\Venom\Service\Setup;

class SetupController extends Controller implements ControllerInterface
{

    private $service;

    public function __construct()
    {
        parent::__construct();
        $this->service = new Setup();
    }

    public function getTemplateName()
    {
        return 'install.html.twig';
    }

    public function index()
    {
        # code...
    }

    public function install()
    {
        $mainPATH = $GLOBALS['fileroot'] . "/contrib/venom";

        $files_array = scandir($mainPATH);
        array_shift($files_array); // get rid of "."
        array_shift($files_array); // get rid of ".."
        $count = 0;

        for ($i=0; $i < count($files_array); $i++) {
            if ($files_array[$i] === "README") {
                unset($files_array[$i]);
            }
        }

        foreach ($files_array as $file) {
            $this_file = $mainPATH . "/" . $file;
            if (strpos($file, ".xlsx") !== false) {
                $count++;
            }
        }

        if ($count > 1) {
            error_log('Too many files in contrib directory');
            die();
        }

        if ($count == 0) {
            error_log('Nothing in directory');
            die();
        }

        $install = $this->service->install($this_file);

        $return = [];
        if ($install == true) {
            $return['result'] = "success";
        } else {
            $return['result'] = "error";
        }

        return $return;
    }
}
