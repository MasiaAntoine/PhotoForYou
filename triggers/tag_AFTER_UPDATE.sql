CREATE DEFINER=`root`@`localhost` TRIGGER `sab_photoforyou`.`tag_AFTER_UPDATE` AFTER UPDATE ON `tag` FOR EACH ROW
BEGIN
    INSERT INTO log (date, table_name, type, detail)
    VALUES (CURRENT_TIMESTAMP(), 'tag', 'M', JSON_OBJECT('idTag', NEW.idTag));
END