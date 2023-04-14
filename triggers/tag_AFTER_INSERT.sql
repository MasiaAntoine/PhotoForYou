CREATE DEFINER=`root`@`localhost` TRIGGER `sab_photoforyou`.`tag_AFTER_INSERT` AFTER INSERT ON `tag` FOR EACH ROW
BEGIN
    INSERT INTO log (date, table_name, type, detail)
    VALUES (CURRENT_TIMESTAMP(), 'tag', 'A', JSON_OBJECT('idTag', NEW.idTag));
END