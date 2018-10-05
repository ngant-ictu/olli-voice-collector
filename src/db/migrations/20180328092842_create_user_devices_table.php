<?php


use Phinx\Migration\AbstractMigration;

class CreateUserDevicesTable extends AbstractMigration
{
    public function up()
    {
        $this->execute("
            CREATE TABLE `fly_user_devices` (
            `u_id` int(11) DEFAULT '0',
            `ud_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
            `ud_platform` varchar(50) DEFAULT '',
            `ud_device` varchar(50) DEFAULT '',
            `ud_browser` varchar(50) DEFAULT '',
            `ud_version` varchar(50) DEFAULT '',
            `ud_is_robot` tinyint(1) DEFAULT '0',
            `ud_user_agent` varchar(255) DEFAULT '',
            `ud_ip_address` int(11) DEFAULT NULL,
            `ud_date_created` int(10) DEFAULT '0',
            `ud_date_modified` int(10) DEFAULT '0',
            PRIMARY KEY (`ud_id`),
            KEY `ud_id` (`ud_id`),
            KEY `u_id` (`u_id`),
            KEY `ud_date_created` (`ud_date_created`),
            KEY `ud_browser` (`ud_browser`),
            KEY `ud_ip_address` (`ud_ip_address`),
            KEY `ud_is_robot` (`ud_is_robot`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        ");
    }
}
