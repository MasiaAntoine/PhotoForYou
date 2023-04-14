CREATE DEFINER=`root`@`localhost` TRIGGER `sab_photoforyou`.`user_AFTER_UPDATE` AFTER UPDATE ON `user` FOR EACH ROW
BEGIN
    -- Vérifier si la colonne "isBanUser" a été modifiée et est égale à 1 (ban)
    IF NEW.isBanUser = 1 AND (OLD.isBanUser IS NULL OR OLD.isBanUser = 0) THEN
        INSERT INTO log (date, table_name, type, detail)
        VALUES (CURRENT_TIMESTAMP(), 'user', 'M', JSON_OBJECT('idUser', NEW.idUser, 'reason', 'ban'));
    END IF;
    
    -- Vérifier si la colonne "isBanUser" a été modifiée et est égale à 0 (unban)
    IF NEW.isBanUser = 0 AND OLD.isBanUser = 1 THEN
        INSERT INTO log (date, table_name, type, detail)
        VALUES (CURRENT_TIMESTAMP(), 'user', 'M', JSON_OBJECT('idUser', NEW.idUser, 'reason', 'unban'));
    END IF;
END