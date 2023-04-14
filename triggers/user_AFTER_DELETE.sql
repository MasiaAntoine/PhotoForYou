CREATE DEFINER=`root`@`localhost` TRIGGER `sab_photoforyou`.`user_AFTER_DELETE` AFTER DELETE ON `user` FOR EACH ROW
BEGIN
    INSERT INTO log (date, table_name, type, detail)
    VALUES (CURRENT_TIMESTAMP(), 'user', 'D', JSON_OBJECT('idUser', OLD.idUser, 'rankUser', OLD.rankUser));
END