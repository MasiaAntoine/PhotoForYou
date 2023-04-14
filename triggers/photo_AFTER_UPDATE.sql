CREATE DEFINER=`root`@`localhost` TRIGGER `sab_photoforyou`.`photo_AFTER_UPDATE` AFTER UPDATE ON `photo` FOR EACH ROW
BEGIN
    -- Cela vérifie si la photo a été achetée par un client.
    IF NEW.isBuyPhoto IS NOT NULL THEN
        INSERT INTO log (date, table_name, type, detail)
        VALUES (CURRENT_TIMESTAMP(), 'photo', 'M', JSON_OBJECT('idPhoto', NEW.idPhoto, 'idClient', NEW.isBuyPhoto));
    END IF;
END