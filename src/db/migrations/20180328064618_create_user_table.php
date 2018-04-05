<?php
use Phinx\Migration\AbstractMigration;

class CreateUserTable extends AbstractMigration
{
    public function up()
    {
        $this->execute("
        CREATE TABLE `fly_user` (
            `u_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
            `u_screen_name` varchar(32) DEFAULT '',
            `u_full_name` varchar(100) DEFAULT '',
            `u_email` varchar(100) DEFAULT '',
            `u_address` varchar(255) DEFAULT NULL,
            `u_password` varchar(100) NOT NULL,
            `u_groupid` varchar(100) DEFAULT '0',
            `u_avatar` varchar(255) DEFAULT '',
            `u_gender` varchar(5) DEFAULT NULL,
            `u_status` int(2) DEFAULT '0',
            `u_oauth_provider` varchar(20) DEFAULT '',
            `u_oauth_uid` varchar(50) DEFAULT '',
            `u_oauth_access_token` varchar(255) DEFAULT '',
            `u_onesignal_id` varchar(255) DEFAULT '',
            `u_state` int(2) DEFAULT '0',
            `u_dob` int(10) DEFAULT '0',
            `u_date_created` int(10) DEFAULT '0',
            `u_date_last_change_password` int(10) DEFAULT '0',
            `u_date_modified` int(10) DEFAULT '0',
            `u_mobile_number` varchar(20) DEFAULT NULL,
            `u_is_verified` tinyint(1) DEFAULT '0',
            `u_verify_type` tinyint(1) DEFAULT '0',
            PRIMARY KEY (`u_id`),
            KEY `u_email` (`u_email`),
            KEY `u_password` (`u_password`),
            KEY `u_status` (`u_status`),
            KEY `u_group` (`u_groupid`),
            KEY `u_mobile_number` (`u_mobile_number`),
            KEY `u_is_verified` (`u_is_verified`),
            KEY `u_gender` (`u_gender`)
            ) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;
        ");
    }
}
