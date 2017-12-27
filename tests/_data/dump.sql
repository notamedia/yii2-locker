/* Replace this file with actual dump of your database */

DROP TABLE IF EXISTS `model`;
CREATE TABLE `model` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT
);

DROP TABLE IF EXISTS `lock`;
CREATE TABLE `lock` (
  `hash` TEXT,
  `locked_at` TEXT,
  `locked_by` INTEGER
);