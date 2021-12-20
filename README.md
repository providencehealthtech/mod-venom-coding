# VeNom Coding Support

Copyright (c) 2021 Providence HealthTech. All Rights Reserved.

This module creates the table structure for VeNom coding support. Several tables are created: venom_dx, venom_dx_test, venom_admin and venom_proc. If the actual VeNom codes file is placed into `contrib/venom/` of the main OpenEMR installation, this module will differentiate the Excel file into the appropriate tables. Additionally, the Code Types list is appended, inserting the various VeNom codes.

## To-Do
* Expand UI of module
* Allow for each breed and species to be added as Lists
* Automate the assigning of the Code Type list item to the External Code Type

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
        * Register, Install, and Enable the module
    * Go to Admin -> Forms -> Lists
        * Select Code Types List
        * Corelate the Venom rows to the appropriate External code type

## Table Structure

### venom_dx
This table includes all codes with a subset of diagnosis.

### venom_dx_test
This table includes all codes with a subset of diagnostic tests.

### venom_proc
This table includes all codes with a subset of procedure

### venom_admin
This table includes all codes with a subset of administrative tasks

---

Note, this module does _not_ provide the actual VeNom codes, those must be
secured by the end user separately and placed in a specific location on the
filesystem in OpenEMR. To acquire the VeNom code set, visit
http://venomcoding.org/
