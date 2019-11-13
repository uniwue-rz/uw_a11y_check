#
# Table structure for table 'tx_a11ycheck_result'
#
CREATE TABLE tx_a11ycheck_result (
	uid int(11) NOT NULL auto_increment,

	check_date int(11) unsigned DEFAULT '0' NOT NULL,
	pid int(11) DEFAULT '0' NOT NULL,

	record_uid int(11) DEFAULT '0' NOT NULL,
	preset_id varchar(255) DEFAULT '' NOT NULL,
	table_name varchar(255) DEFAULT '' NOT NULL,
	resultset mediumtext,

	PRIMARY KEY (uid),
	KEY page (pid)
);