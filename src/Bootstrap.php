<?php
/**
 *
 */

namespace ProvidenceHealthTech\Venom;

use ProvidenceHealthTech\Venom\Service\Setup;
use OpenEMR\Core\Kernel;
use OpenEMR\Events\Codes\ExternalCodesCreatedEvent;
use OpenEMR\Events\Globals\GlobalsInitializedEvent;
use OpenEMR\Menu\MenuEvent;
use OpenEMR\Services\Globals\GlobalSetting;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class Bootstrap
{
    const MODULE_INSTALLATION_PATH = "/interface/modules/custom_modules/";

    const MODULE_NAME = "module-venom-coding";

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var Service
     */
    private $service;

    public function __construct(EventDispatcherInterface $eventDispatcher, ?Kernel $kernel = null)
    {
        global $GLOBALS;
        $this->eventDispatcher = $eventDispatcher;
        $this->service = new Setup();
    }

    public function subscribeToEvents()
    {
        $this->registerMenuItems();
        $this->eventDispatcher->addListener(ExternalCodesCreatedEvent::EVENT_HANDLE, [$this, 'externalCodeEventListener']);
    }

    public function externalCodeEventListener(ExternalCodesCreatedEvent $event)
    {

        $externalCodes = $event->getexternalCodeData();

        // Insert the external code tables into the global external codes array
        $venomCodes = $this->getVenomCodeTableIds();

        foreach ($venomCodes as $id => $title) {
            $externalCodes[$id] = $title;
            $this->service->createExternalTable();
        }

        $event->setExternalCodeData([]);

        return $event;
    }

    public function getVenomCodeTableIds()
    {
        $sql = "SELECT ct_id, ct_label FROM code_types WHERE ct_key LIKE 'venom_%' ORDER BY ct_id ASC";
        $query = sqlStatementNoLog($sql);
        $data = [];

        while ($row = sqlFetchArray($query)) {
            $data[] = $row;
        }

        return $data;
    }

    public function registerMenuItems()
    {
        $this->eventDispatcher->addListener(MenuEvent::MENU_UPDATE, [$this, 'addVenomMenuItem']);
        // if ($this->getGlobalConfig()->getGlobalSetting(GlobalConfig::CONFIG_ENABLE_MENU)) {
        //     /**
        //      * @var EventDispatcherInterface $eventDispatcher
        //      * @var array $module
        //      * @global                       $eventDispatcher @see ModulesApplication::loadCustomModule
        //      * @global                       $module @see ModulesApplication::loadCustomModule
        //      */
        // }
    }

    public function addVenomMenuItem(MenuEvent $event)
	{
	    $menu = $event->getMenu();

	    $menuItem = new \stdClass();
	    $menuItem->requirement = 0;
	    $menuItem->target = 'mod';
	    $menuItem->menu_id = 'mod0';
	    $menuItem->label = xlt("VeNom Coding");
	    // TODO: pull the install location into a constant into the codebase so if OpenEMR changes this location it
        // doesn't break any modules.
	    $menuItem->url = self::MODULE_INSTALLATION_PATH . "/" . self::MODULE_NAME;
	    $menuItem->children = [];

	    /**
	     * This defines the Access Control List properties that are required to use this module.
	     * Several examples are provided
	     */
	    $menuItem->acl_req = [];

	    /**
	     * If you want your menu item to allows be shown then leave this property blank.
	     */
	    $menuItem->global_req = [];

	    foreach ($menu as $item) {
		if ($item->menu_id == 'modimg') {
		    $item->children[] = $menuItem;
		    break;
		}
	    }

	    $event->setMenu($menu);

	    return $event;
	}

    static public function getTemplatePath()
    {
        return \dirname(__DIR__) . DIRECTORY_SEPARATOR . "templates" . DIRECTORY_SEPARATOR;
    }

}
