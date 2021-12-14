<?php
/**
 *
 */

namespace ProvidenceHealthTech\Venom\Controller;

use ProvidenceHealthTech\Venom\Controller\Controller;
use ProvidenceHealthTech\Venom\Controller\ControllerInterface;
use ProvidenceHealthTech\Venom\Service\Venom;

class VenomController extends Controller implements ControllerInterface
{
    private $service;

    public function __construct()
    {
        $this->service = new Venom();
        $this->templateName = "admin.html.twig";
    }

    public function index()
    {
        $versions = $this->service->getVersion();
        return [
            'installURL' => '/interface/modules/custom_modules/venom/index.php?controller=setup&action=install',
            'versions' => $versions
        ];
    }
}
