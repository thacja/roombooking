<?php

declare(strict_types=1);

use App\Controllers\RoomController;
use App\Helpers\e;

require_once __DIR__ . '/../bootstrap.php';

$controller = new RoomController();
$controller->index();
