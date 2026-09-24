<?php

declare(strict_types=1);

use Database\Database;

/**
 * Return singleton PDO connection instance.
 */
return Database::getInstance();
