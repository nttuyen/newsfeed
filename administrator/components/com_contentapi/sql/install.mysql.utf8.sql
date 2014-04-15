CREATE TABLE IF NOT EXISTS #__content_crawled (
  id BIGINT NOT NULL ,
  origin_link VARCHAR(500) NULL ,
  crawled_time DATETIME NULL ,
  PRIMARY KEY (id)
) ENGINE = InnoDB DEFAULT CHARACTER SET = utf8;