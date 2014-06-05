CREATE TABLE IF NOT EXISTS #__content_crawled (
  id BIGINT NOT NULL ,
  origin_link VARCHAR(500) NULL ,
  crawled_time DATETIME NULL ,
  next_crawled_time DATETIME NULL ,
  crawler_attempted tinyint(4) DEFAULT '0',
  PRIMARY KEY (id)
) ENGINE = InnoDB DEFAULT CHARACTER SET = utf8;