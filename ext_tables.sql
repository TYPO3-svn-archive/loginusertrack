CREATE TABLE tx_loginusertrack_stat (
	uid int(11) unsigned DEFAULT '0' NOT NULL auto_increment,
	pid int(11) unsigned DEFAULT '0' NOT NULL,
#	crdate int(11) unsigned DEFAULT '0' NOT NULL,
#	tstamp int(11) unsigned DEFAULT '0' NOT NULL,
	fe_user int(11) unsigned DEFAULT '0' NOT NULL,
	session_login int(11) unsigned DEFAULT '0' NOT NULL,
	last_page_hit int(11) unsigned DEFAULT '0' NOT NULL,
	session_hit_counter int(11) unsigned DEFAULT '0' NOT NULL,

	PRIMARY KEY (uid),
	KEY fe_user (fe_user)
);

CREATE TABLE tx_loginusertrack_pagestat (
	uid int(11) unsigned DEFAULT '0' NOT NULL auto_increment,
	pid int(11) unsigned DEFAULT '0' NOT NULL,
	crdate int(11) unsigned DEFAULT '0' NOT NULL,
	tstamp int(11) unsigned DEFAULT '0' NOT NULL,
	fe_user int(11) unsigned DEFAULT '0' NOT NULL,
	sesstat_uid int(11) unsigned DEFAULT '0' NOT NULL,
	page_id int(11) unsigned DEFAULT '0' NOT NULL,
	hits int(11) unsigned DEFAULT '1' NOT NULL,

	PRIMARY KEY (uid),
	KEY fe_user (fe_user)
);
