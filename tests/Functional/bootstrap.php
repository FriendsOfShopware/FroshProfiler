<?php
/**
 * Shopware 5
 * Copyright (c) shopware AG.
 *
 * According to our dual licensing model, this program can be used either
 * under the terms of the GNU Affero General Public License, version 3,
 * or under a proprietary license.
 *
 * The texts of the GNU Affero General Public License with an additional
 * permission and of our proprietary license can be found at and
 * in the LICENSE file you have received along with this program.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * "Shopware" is a registered trademark of shopware AG.
 * The licensing of the program under the AGPLv3 does not imply a
 * trademark license. Therefore any rights, title and interest in
 * our trademarks remain entirely with us.
 */

define('TESTS_RUNNING', true);
$_SERVER['REMOTE_ADDR'] = '127.0.0.1';
$_SERVER['SERVER_REQUEST_METHOD'] = 'GET';

$searchDirectory = dirname(dirname(__DIR__));
while (true) {
    $newSearchDirectory = realpath($searchDirectory . DIRECTORY_SEPARATOR . '..');
    if ($searchDirectory === false || strlen($searchDirectory) < 3 || $newSearchDirectory === $searchDirectory) {
        throw new RuntimeException('No autoloader found');
    }
    $searchDirectory = $newSearchDirectory;

    if (file_exists($autoloadFile = implode(DIRECTORY_SEPARATOR, [$searchDirectory, 'tests', 'Functional', 'bootstrap.php']))) {
        require $autoloadFile;
        break;
    }
}
