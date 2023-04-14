CREATE DEFINER=`root`@`localhost` TRIGGER `sab_photoforyou`.`tag_AFTER_DELETE` AFTER DELETE ON `tag` FOR EACH ROW
BEGIN
    INSERT INTO log (date, table_name, type, detail)
    VALUES (CURRENT_TIMESTAMP(), 'tag', 'D', JSON_OBJECT('idTag', OLD.idTag));
END