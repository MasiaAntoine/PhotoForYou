CREATE DEFINER=`root`@`localhost` TRIGGER `sab_photoforyou`.`photo_AFTER_INSERT` AFTER INSERT ON `photo` FOR EACH ROW
BEGIN
    INSERT INTO log (date, table_name, type, detail)
    VALUES (CURRENT_TIMESTAMP(), 'photo', 'A', JSON_OBJECT('idPhoto', NEW.idPhoto, 'idPhotographe', NEW.idUserPhotographer));
END