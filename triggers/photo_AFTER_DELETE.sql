CREATE DEFINER=`root`@`localhost` TRIGGER `sab_photoforyou`.`photo_AFTER_DELETE` AFTER DELETE ON `photo` FOR EACH ROW
BEGIN
    INSERT INTO log (date, table_name, type, detail)
    VALUES (CURRENT_TIMESTAMP(), 'photo', 'D', JSON_OBJECT('idPhoto', OLD.idPhoto, 'idPhotographe', OLD.idUserPhotographer));
END