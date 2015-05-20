<?php
/**
 * 1.1.0:
 */

defined('COT_CODE') or die('Wrong URL');
global $db, $db_users;
if (!$db->fieldExists($db_users, "user_prjsenderlocation"))
{
	$db->query("ALTER TABLE `$db_users` ADD COLUMN `user_prjsenderlocation` MEDIUMTEXT collate utf8_unicode_ci NOT NULL");
}