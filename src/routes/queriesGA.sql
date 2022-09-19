--select
--    Cha_ID, Cha_Name,
--    345*Challenges.Cha_ID as 'Mumero de proyectos',
--    34+Cha_ID as 'numero de equipos',
--    11+Challenges.Cha_ID as 'numero de mentores'
--from Challenges
--where Cha_Organizer=1;

--SELECT IF(EXISTS(
--    SELECT TID_TalentID, TID_IsGAADMIN
--    FROM TalentID AS tid
--    LEFT JOIN TalentId_Info AS tii
--    ON tii.TIN_ID = $admin_client_id
--    WHERE tid.TID_IsGAADMIN = 1
--), 1, 0) AS isGAAdmin

IF (SELECT TID_TalentID, TID_IsGAADMIN FROM TalentID AS tid WHERE tid.TID_TalentID="0055715c-9423-11ec-9e65-00163efa374e" AND tid.TID_IsGAADMIN=1) THEN
--BEGIN
--    SELECT TID_TalentID, CONCAT(TIN_Name, TIN_Last_Name) AS Name, TID_Email, TID_IsGAADMIN
--    FROM TalentID AS tid
--    LEFT JOIN TalentId_Info AS tii
--    ON tii.TIN_TID_ID = tid.TID_TalentID
--    WHERE TID_IsGAADMIN=1
--END;

--ELSE
--BEGIN
--    INSERT INTO Table (FieldValue) VALUES('');
--    SELECT LAST_INSERT_ID() AS TableID;
--END;
--END IF;