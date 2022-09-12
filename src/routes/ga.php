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
// Endpoint de producción: /organizations/getOrg
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

// Estadisticas de los challenges
// cambiar por organizacion en lugar de challenge id  y me traigas la lista de los challenges que tiene.
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

$app->get(
    '/api/GA/orgStats',
    function (Request $request, Response $response) {
        $body = json_decode($request->getBody()->getContents(), true);
        $org_stats_id = isset($body['org_id_from_client']) ? $body['org_id_from_client'] : null;
        if (is_null($org_stats_id)) {
            $response->getBody()->write(json_encode(array('error' => 'No se ha enviado el dato correctamente')));
            return $response->withStatus(400);
        }

        $sql = "
        SELECT
            o.Org_ID AS 'Id de la Organización',
            o.Org_Name AS 'Nombre de la Organización',
            o.Org_Logo AS 'Logo de la Organización',
            COUNT(distinct c.Cha_ID) AS 'Número de Challenges',
            COUNT(distinct ao.AO_TID_ID) AS 'Número de Admins',
            COUNT(distinct ps.RPA_Part_ID) AS 'Número de Participantes',
            COUNT(distinct rt.RTE_Team_ID) AS 'Número de Equipos',
            COUNT(distinct pp.PPRO_ID) AS 'Número de Proyectos',
            COUNT(distinct rm.RM_Talent_ID) AS 'Número de Mentores'
        FROM Organizers AS o
        LEFT JOIN Challenges AS c
            ON c.Cha_Organizer = o.Org_ID
        LEFT JOIN Admin_Org AS ao
            ON ao.AO_Org_ID = o.Org_ID
        LEFT JOIN Registered_Participants AS ps
            ON ps.RPA_Cha_ID = c.Cha_ID
        LEFT JOIN Registered_Teams AS rt
            ON rt.RTE_Cha_ID = c.Cha_ID
        LEFT JOIN Participant_Project AS pp
            ON pp.PPRO_Cha_ID = c.Cha_ID
        LEFT JOIN Registered_Mentors AS rm
            ON rm.RM_Cha_ID = c.Cha_ID
        WHERE c.Cha_Organizer = '$org_stats_id'
                ";

        $db = new db();
        $db = $db->connectBD();
        $stm = $db->prepare($sql);
        $stm->execute([]);
        $org_stats = $stm->fetchAll(PDO::FETCH_OBJ);
        $response->getBody()->write(json_encode($org_stats));
    }
);

$app->get(
    '/api/organizations/getOrgStats',
    function (Request $request, Response $response) {
        $body = json_decode($request->getBody()->getContents(), true);
        $org_stats_id = isset($body['org_id_from_client']) ? $body['org_id_from_client'] : null;
        if (is_null($org_stats_id)) {
            $response->getBody()->write(json_encode(array('error' => 'No se ha enviado el dato correctamente')));
            return $response->withStatus(400);
        }

        $sql="
            SELECT
                c.Cha_ID,
                c.Cha_Name,
                c.Cha_Cover_Page_Img,
                COUNT(distinct rm.RM_Talent_ID) AS T_Mentors,
                COUNT(distinct ps.Part_Talent_ID) AS T_Participants,
                COUNT(distinct rt.RTE_Team_ID) AS 'T_Teams',
                COUNT(distinct pp.PPRO_ID) AS 'T_Projects'
             FROM Challenges AS c
        LEFT JOIN Registered_Mentors AS rm
               ON rm.RM_Cha_ID = c.Cha_ID
        LEFT JOIN Participants AS ps
               ON ps.Part_ID = c.Cha_ID
        LEFT JOIN Registered_Teams AS rt
               ON rt.RTE_Cha_ID = c.Cha_ID
        LEFT JOIN Participant_Project AS pp
               ON pp.PPRO_Cha_ID = c.Cha_ID
            WHERE c.Cha_Organizer = '$org_stats_id'
            GROUP BY c.Cha_ID
        ";
        $db = new db();
        $db = $db->connectBD();
        $stm = $db->prepare($sql);
        $stm->execute([]);
        $org_stats = $stm->fetchAll(PDO::FETCH_OBJ);
        $response->getBody()->write(json_encode($org_stats));
    }
);

// In this query i will do a join to AdminGA 