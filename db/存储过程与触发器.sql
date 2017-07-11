/*
	简单存储过程例子
	@author chenbin
*/
DELIMITER //
CREATE PROCEDURE `pro_test`(IN `a` INT, IN `b` INT, OUT `sum` INT)
BEGIN
        DECLARE c INT;
        IF a > 10 THEN SET a = 9;
        END IF;

        IF b is null THEN SET b = 10;
        END IF;

        SET `sum` = a + b;
END 
//

/*
	IF语句
	@author chenbin
*/
DELIMITER //
CREATE PROCEDURE `pro_if` (IN `type` INT)
BEGIN
    DECLARE str VARCHAR(255);
    IF `type` = 1 THEN
        SET str = 'param 1';
    ELSEIF `type` = 2 THEN 
        SET str = 'param 2';
    ELSE 
        SET str = 'param each';
    END IF;
    SELECT str;
END 
//

/*
	@author chenbin
*/
CREATE PROCEDURE `procArenaInsertUpdate`(IN `nServerID` int,IN `nGUID` bigint,IN `nFightPower` int,IN `data` mediumblob)
BEGIN
    DECLARE nMaxRank int(11);
    DECLARE nCurRank int(11);
    DECLARE nPlayerCount int(11);

    START TRANSACTION;
        SELECT COUNT(*) INTO nPlayerCount FROM player_arena_data WHERE ServerID = nServerID AND PlayerGUID = nGUID;
        if nPlayerCount > 0 THEN
            UPDATE player_arena_data SET FightPower = nFightPower, PlayerData = data WHERE ServerID = nServerID AND PlayerGUID = nGUID;
        ELSE
            SELECT  COUNT(*) INTO nMaxRank FROM player_arena_data WHERE ServerID = nServerID;
            SET nCurRank = nMaxRank + 1;
            INSERT INTO player_arena_data(ServerID, PlayerGUID, Rank, FightPower, PlayerData,AwardRank,AwardState) 
            VALUES (nServerID, nGUID, nCurRank, nFightPower, data, -1,3);

            SELECT nCurRank;
        END if;
    COMMIT;
END
/*
	@author chenbin
*/
DELIMITER //
CREATE PROCEDURE `use_log` (
	IN `nUserName` VARCHAR(11),
	IN `nRole` TINYINT(2),
	IN `nString` CHAR(10)
	)
BEGIN
	DECLARE nUserNameBak VARCHAR(11);
	DECLARE nDate DATE DEFAULT CURRENT_DATE();
	
	/*这个SELECT语法把选定的列直接存储到变量。因此，只有单一的行可以被取回。
	在使用的时候，要注意，加上LIMIT 1*/
	SELECT `username` INTO nUserNameBak FROM `user_log` WHERE `username` = nUserName LIMIT 1;
	
	IF ISNULL(nUserNameBak) = 1 THEN
		INSERT INTO `user_log`(`username`, `role`, `date`, `string`) VALUES (nUserName, nRole, nDate, nString);
		SELECT 'insert' AS operation;
	ELSE
		UPDATE `user_log` SET `date` = nDate WHERE `username` = nUserName;
		SELECT 'update' AS operation;
	END IF;
END
//

/*
	简单触发器
	@author chenbin
*/
DELIMITER //
CREATE trigger t_user_trigger
AFTER INSERT ON `test_cb`
FOR EACH ROW
BEGIN
        UPDATE `user` SET `sex` = 4 WHERE `id` = 1;
END 
//
