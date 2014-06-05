CREATE TABLE IF NOT EXISTS #__site (
  id bigint(20) NOT NULL AUTO_INCREMENT,
  domain varchar(200) NOT NULL,
  state int(11) DEFAULT '0',
  description varchar(500) DEFAULT NULL,
  created datetime DEFAULT NULL,
  created_by bigint(20) DEFAULT NULL,
  modified datetime DEFAULT NULL,
  modified_by bigint(20) DEFAULT NULL,
  checked_out bigint(20) DEFAULT NULL,
  checked_out_time datetime DEFAULT NULL,
  PRIMARY KEY (id)
) ENGINE = InnoDB DEFAULT CHARACTER SET = utf8;

CREATE TABLE #__feed (
  id bigint(20) NOT NULL AUTO_INCREMENT,
  site bigint(20) NOT NULL,
  link varchar(500) NOT NULL,
  state int(11) DEFAULT '0',
  description varchar(500) DEFAULT NULL,
  created datetime DEFAULT NULL,
  created_by bigint(20) DEFAULT NULL,
  modified datetime DEFAULT NULL,
  modified_by bigint(20) DEFAULT NULL,
  checked_out bigint(20) DEFAULT NULL,
  checked_out_time datetime DEFAULT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE #__feed_reader (
  id bigint(20) NOT NULL AUTO_INCREMENT,
  object_id bigint(20) NOT NULL,
  object_type char(20) NOT NULL,
  feed_type varchar(250) DEFAULT NULL,
  state int(11) DEFAULT '0',
  description varchar(500) DEFAULT NULL,
  processing varchar(1000) DEFAULT NULL,
  created datetime DEFAULT NULL,
  created_by bigint(20) DEFAULT NULL,
  modified datetime DEFAULT NULL,
  modified_by bigint(20) DEFAULT NULL,
  checked_out bigint(20) DEFAULT NULL,
  checked_out_time datetime DEFAULT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE #__scheduler (
  id bigint(20) NOT NULL AUTO_INCREMENT,
  object_id bigint(20) NOT NULL,
  object_type char(20) NOT NULL,
  state int(11) DEFAULT '0',
  start datetime DEFAULT NULL,
  time_interval int(11) DEFAULT '15',
  created datetime DEFAULT NULL,
  created_by bigint(20) DEFAULT NULL,
  modified datetime DEFAULT NULL,
  modified_by bigint(20) DEFAULT NULL,
  checked_out bigint(20) DEFAULT NULL,
  checked_out_time datetime DEFAULT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE #__crawler (
  id bigint(20) NOT NULL AUTO_INCREMENT,
  object_id bigint(20) NOT NULL,
  object_type char(20) NOT NULL,
  state int(11) DEFAULT '0',
  description varchar(500) DEFAULT NULL,
  configuration varchar(1000) DEFAULT NULL,
  created datetime DEFAULT NULL,
  created_by bigint(20) DEFAULT NULL,
  modified datetime DEFAULT NULL,
  modified_by bigint(20) DEFAULT NULL,
  checked_out bigint(20) DEFAULT NULL,
  checked_out_time datetime DEFAULT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


