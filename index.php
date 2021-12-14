<?php
/**
 *
 */

use OpenEMR\Common\Acl\AclMain;
use OpenEMR\Common\Twig\TwigContainer;
use ProvidenceHealthTech\Venom\Bootstrap;
use ProvidenceHealthTech\Venom\Controller;
use Symfony\Component\HttpFoundation\Request;
use Twig\Extension\DebugExtension;

require_once "../../../globals.php";

// Control access
if (!AclMain::aclCheckCore('admin', 'super')) {
    echo xlt('Not Authorized');
    exit;
}

$templateDir = Bootstrap::getTemplatePath();
$twigContainer = new TwigContainer($templateDir);
$twig = $twigContainer->getTwig();
$twig->addExtension(new DebugExtension());
$twig->enableDebug();

$request = Request::createFromGlobals();

$r_controller = $request->get('controller', 'venom');
$r_action = $request->get('action', 'index');

$routes = [
    'venom' => 'ProvidenceHealthTech\Venom\Controller\VenomController',
    'setup' => 'ProvidenceHealthTech\Venom\Controller\SetupController',
];

if (array_key_exists($r_controller, $routes) == true) {
    $reflection = new ReflectionClass($routes[$r_controller]);

    if ($reflection->hasMethod($r_action)) {
        /**
         * @var Controller
         */
        $instance = $reflection->newInstance();
        $results = call_user_func([$instance, $r_action]);
        echo $twig->render($instance->getTemplateName(), $results);
    }

}
