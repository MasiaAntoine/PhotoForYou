CREATE DEFINER=`root`@`localhost` TRIGGER `sab_photoforyou`.`user_AFTER_INSERT` AFTER INSERT ON `user` FOR EACH ROW
BEGIN
    INSERT INTO log (date, table_name, type, detail)
    VALUES (CURRENT_TIMESTAMP(), 'user', 'A', JSON_OBJECT('idUser', NEW.idUser, 'rankUser', NEW.rankUser));
END