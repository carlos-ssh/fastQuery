<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app = new \Slim\App;


// Admins del challenge
$app->get(
    '/api/GA/admins',
    function (Request $request, Response $response) {
        $body = json_decode($request->getBody()->getContents(), true);
        $Admin_ID = isset($body['admin_from_client']) ? $body['admin_from_client'] : null;
        if (is_null($Admin_ID)) {
            $response->getBody()->write(json_encode(array('error' => 'No se ha enviado el dato')));
            return $response->withStatus(400);
        }
        $sql = "
            SELECT 
                CONCAT(ti.TIN_Name, ' ', ti.TIN_Last_Name) AS 'Nombre del Admin', 
                c.Cha_ID AS 'Id del Challenge', c.Cha_Name AS 'Nombre del Challenge', 
                c.Cha_Type AS 'Tipo de Challenge', 
                c.Cha_Cover_Page_Img AS 'Imagen', 
                Cha_Slug AS 'Slug del Challenge'
            FROM Admin_Cha AS ac
            JOIN TalentID AS tid
            ON tid.TID_TalentID = ac.AC_TID_ID
            JOIN TalentId_Info AS ti
            ON ti.TIN_TID_ID = tid.TID_TalentID
            JOIN Challenges AS c
            ON c.Cha_ID = ac.AC_Cha_ID
            WHERE ti.TIN_TID_ID = '$Admin_ID'
        ";

        $db = new db();
        $db = $db->connectBD();
        $stm = $db->prepare($sql);
        $stm->execute([]);
        $mentor_challenges = $stm->fetchAll(PDO::FETCH_OBJ);
        $response->getBody()->write(json_encode($mentor_challenges));
    }
);


// mentores del challenge
$app->get(
    '/api/GA/mentors',
    function (Request $request, Response $response) {
        $body = json_decode($request->getBody()->getContents(), true);
        $mentor_id = isset($body['mentor_from_client']) ? $body['mentor_from_client'] : null;
        if (is_null($mentor_id)) {

            $response->getBody()->write(json_encode(array('error' => 'No se ha enviado el dato')));
            return $response->withStatus(400);
        }
        $sql = "
            SELECT
                CONCAT(ti.TIN_Name, ' ', ti.TIN_Last_Name) AS 'Nombre del Mentor',
                c.Cha_ID AS 'Id del Challenge', c.Cha_Name AS 'Nombre del Challenge', 
                c.Cha_Type AS 'Tipo de Challenge', 
                c.Cha_Cover_Page_Img AS 'Imagen', 
                Cha_Slug AS 'Slug del Challenge' 
            FROM Registered_Mentors AS rm
            JOIN TalentID AS tid
                ON tid.TID_TalentID = rm.RM_Talent_ID
            JOIN TalentId_Info AS ti
                ON ti.TIN_TID_ID = rm.RM_Talent_ID
            JOIN Challenges AS c
                ON c.Cha_ID = rm.RM_Cha_ID
            WHERE tid.TID_TalentID = '$mentor_id'
        ";

        $db = new db();
        $db = $db->connectBD();
        $stm = $db->prepare($sql);
        $stm->execute([]);
        $mentor_challenges = $stm->fetchAll(PDO::FETCH_OBJ);
        $response->getBody()->write(json_encode($mentor_challenges));
    }
);

// Admins por organizacion
$app->get(
    '/api/GA/admin_by_org',
    function (Request $request, Response $response) {
        $body = json_decode($request->getBody()->getContents(), true);
        $org_id = isset($body['admin_by_org_from_client']) ? $body['admin_by_org_from_client'] : null;
        if (is_null($org_id)) {
            $response->getBody()->write(json_encode(array('error' => 'No se ha enviado el dato')));
            return $response->withStatus(400);
        }

        $sql = "
            SELECT 
                CONCAT(ti.TIN_Name, ' ', ti.TIN_Last_Name) AS 'Nombre del Admin', 
                o.Org_Name AS 'Nombre de la Organización', 
                o.Org_ID AS 'Id de la Organización'
            FROM TalentId_Info AS ti
            JOIN TalentID AS tid
            ON tid.TID_TalentID = ti.TIN_TID_ID
            JOIN Admin_Org AS ao
            ON ao.AO_TID_ID = tid.TID_TalentID
            JOIN Organizers AS o
            ON o.Org_ID = ao.AO_Org_ID
            WHERE tid.TID_TalentID = '$org_id'
        ";

        $db = new db();
        $db = $db->connectBD();
        $stm = $db->prepare($sql);
        $stm->execute([]);
        $mentor_challenges = $stm->fetchAll(PDO::FETCH_OBJ);
        $response->getBody()->write(json_encode($mentor_challenges));
    }
);


// SELECT
//     c.Cha_ID, c.Cha_Name, c.Cha_Cover_Page_Img, COUNT(rm.RM_Talent_ID) AS 'Numero de Mentores',
//     COUNT(ru.RU_Talent_ID) AS 'Numero de Usuarios',
//     COUNT(rt.RT_Team_ID) AS 'Numero de Equipos',
//     COUNT(rp.RP_Project_ID) AS 'Numero de Proyectos'
// FROM Challenges AS c
// JOIN Registered_Mentors AS rm
//   ON rm.RM_Cha_ID = c.Cha_ID
// JOIN Registered_Users AS ru
//   ON ru.RU_Cha_ID = c.Cha_ID
// JOIN Registered_Teams AS rt
//   ON rt.RT_Cha_ID = c.Cha_ID
// JOIN Registered_Projects AS rp
//   ON rp.RP_Cha_ID = c.Cha_ID GROUP BY c.Cha_ID
// WHERE c.Cha_ID = 1

// Estadisticas de los challenges
$app->get(
    '/api/GA/chaStats',
    function (Request $request, Response $response) {
        $body = json_decode($request->getBody()->getContents(), true);
        $cha_stats_id = isset($body['cha_stats_from_client']) ? $body['cha_stats_from_client'] : null;
        if (is_null($cha_stats_id)) {
            $response->getBody()->write(json_encode(array('error' => 'No se ha enviado el dato correctamente')));
            return $response->withStatus(400);
        }

        $sql = "
            SELECT
                c.Cha_ID,
                c.Cha_Name,
                c.Cha_Cover_Page_Img,
                COUNT(distinct rm.RM_Talent_ID),
                COUNT(distinct ps.RPA_Part_ID),
                COUNT(distinct rt.RTE_Team_ID),
                COUNT(distinct pp.PPRO_ID)
            FROM Challenges AS c
            left JOIN Registered_Mentors AS rm
                   ON rm.RM_Cha_ID = c.Cha_ID
            left JOIN Registered_Participants AS ps
                   ON ps.RPA_Cha_ID = c.Cha_ID
            left JOIN Registered_Teams AS rt
                   ON rt.RTE_Cha_ID = c.Cha_ID
            left JOIN Participant_Project AS pp
                   ON pp.PPRO_Cha_ID = c.Cha_ID
            where c.Cha_ID = '$cha_stats_id'
        ";

        $db = new db();
        $db = $db->connectBD();
        $stm = $db->prepare($sql);
        $stm->execute([]);
        $cha_stats = $stm->fetchAll(PDO::FETCH_OBJ);
        $response->getBody()->write(json_encode($cha_stats));
    }
);
