<?php

/**
 *
 */

namespace ProvidenceHealthTech\Venom;

use ProvidenceHealthTech\Venom\Bootstrap;

$bootstrap = new Bootstrap($eventDispatcher, $GLOBALS['kernel']);
$bootstrap->subscribeToEvents();
