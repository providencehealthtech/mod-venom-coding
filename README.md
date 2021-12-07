# VeNom Coding Support

Copyright (c) 2021 Providence HealthTech. All Rights Reserved.

This module creates the table structure for VeNom coding support. Three tables are created, venom_dx, venom_dx_test, and venom_proc. Currently, this module just secures a namespace and installs the tables. The ability to actually import records requires changes to the codebase (which will be submitted) via a PR to allow support or by improving the Event dispatcher.

## This Module Does Not Do What You Think It Does
This module, by itself, will not provide full support at this time. You MUST have the correct changes in the core codebase to support the actual insertion of these codes.

## Pre-requisites

* [OpenEMR](https://github.com/openemr/openemr) v6.0.0 or later
* [oe-module-installer-plugin](https://github.com/openemr/oe-module-installer-plugin) ^0.1.0
* Access to the [VeNom Codes](http://venomcoding.org/)
* This repository added to composer

## Installation
* From the root directory of your OpenEMR Installation

    * ```composer require providencehealthtech/mod-venom-coding```

* In OpenEMR

    * Go to Modules Manager
    * Register, Install, and Enable

## Table Structure

### venom_dx
This table includes all codes with a subset of diagnosis.

### venom_dx_test
This table includes all codes with a subset of diagnostic tests.

### venom_proc
This table includes all codes with a subset of procedure

---

Note, this module does not provide the actual VeNom codes, those must be
secured by the end user separately and placed in a specific location on the
filesystem in OpenEMR. To acquire the VeNom code set, visit
http://venomcoding.org/
