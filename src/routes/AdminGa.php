<?php
// header("Access-Control-Allow-Origin: *");
// header("Access-Control-Allow-Headers: access");
// header("Access-Control-Allow-Methods: POST");
// header("Content-Type: application/json; charset=UTF-8");
// header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app = new \Slim\App;

//GET /ga-admins/all
$app->get(
    '/api/ga-admins/all',
    function (Request $request, Response $response) {
        $body = json_decode($request->getBody()->getContents(), true);
        $admin_client_id = isset($body['ga_admin_from_client']) ? $body['ga_admin_from_client'] : null;
        $haveThePower = isset($body['TID_IsGAADMIN']) ? $body['TID_IsGAADMIN'] : null;

        if (is_null($admin_client_id)) {
            $response->getBody()->write(json_encode(array('error' => 'No se ha enviado el dato correctamente')));
            return $response->withStatus(400);
        } elseif ($admin_client_id && $haveThePower == 1) {
            $sql="
                SELECT TID_TalentID, CONCAT(TIN_Name, TIN_Last_Name) AS name, TIN_Email, TID_IsGAADMIN
                FROM TalentID AS tid
                LEFT JOIN TalentId_Info AS tii
                ON tii.TIN_ID = tid.TID_TalentID
                WHERE tid.TID_TalentID
            ";
            $db = new db();
            $db = $db->connectBD();
            $stm = $db->prepare($sql);
            $stm->execute([]);
            echo $stm->execute([]);
            $isAdmin = $stm->fetchAll(PDO::FETCH_OBJ);
            $response->getBody()->write(json_encode($isAdmin));
        } else {
            $response->getBody()->write(json_encode(array('error' => 'No tienes permiso para esta operacion.')));
            return $response;
        }
    }
);

// GET /ga-admins/{id}
$app->get(
    '/api/ga-admins/{id}',
    function (Request $request, Response $response) {
        $body = json_decode($request->getBody()->getContents(), true);
        $admin_client_id = isset($body['ga_admin_from_client']) ? $body['ga_admin_from_client'] : null;
        $id = $request->getAttribute($admin_client_id);
        $haveThePower = isset($body['TID_IsGAADMIN']) ? $body['TID_IsGAADMIN'] : null;

        if (is_null($id)) {
            $response->getBody()->write(json_encode(array('error' => 'No se ha enviado el dato correctamente aqui')));
            return $response->withStatus(400, $id);
        } elseif ($id && $haveThePower == 1) {
            $sql="
                SELECT TID_TalentID, CONCAT(TIN_Name, TIN_Last_Name) AS name, TIN_Email, TID_IsGAADMIN
                FROM TalentID AS tid
                LEFT JOIN TalentId_Info AS tii
                ON tii.TIN_ID = tid.TID_TalentID
                WHERE tid.TID_TalentID = $id
            ";
            $db = new db();
            $db = $db->connectBD();
            $stm = $db->prepare($sql);
            $stm->execute(['id' => $id]);
            echo $stm->execute(['id' => $id]);
            $org_stats = $stm->fetchAll(PDO::FETCH_OBJ);
            $response->getBody()->write(json_encode($org_stats));
        } elseif($haveThePower == 0) {
            $response->getBody()->write(json_encode(array('error' => 'Lo siento. No tienes suficientes permisos.')));
            return $response;
        } else {
            $response->getBody()->write(json_encode(array('error' => 'No tienes permiso para esta operación.')));
            return $response;
        }
    }
);

// POST /ga-admins/create
$app->post(
    '/api/ga-admins/create',
    function (Request $request, Response $response) {
        $body = $request->getParsedBody();
        $new_admin_client = isset($body['ga_admin_from_client']) ? $body['ga_admin_from_client'] : null;
        $id = $request->getAttribute($new_admin_client);
        $sql="
            INSERT INTO 
                TalentId_Info (TIN_Name, TIN_Last_Name, TIN_Email)
                TalentID (TID_IsGAADMIN)
            VALUES (:TIN_Name :TIN_Last_Name, :TIN_Email, :TID_IsGAADMIN)
        ";
        $db = new db();
        $db = $db->connectBD();
        $stm = $db->prepare($sql);
        $stm->execute(['id' => $id]);
        echo $stm->execute(['id' => $id]);
        $org_stats = $stm->fetchAll(PDO::FETCH_OBJ);
        $response->getBody()->write(json_encode($org_stats));
    }
);

// create a new admin account and send data into the database
// POST /ga-admins/create

// PUT /ga-admins/edit/{id}

// DELETE /ga-admins/delete/{id}

// admin: 003d2335-9422-11ec-9e65-00163efa374e
// non-admin: 9f318f44-9522-11ec-9e65-00163efa374e


// {
//     "ga_admin_from_client": "9f318f44-9522-11ec-9e65-00163efa374e",
//     "TID_IsGAADMIN": 0
// }
// 
// {
//     "ga_admin_from_client": "003d2335-9422-11ec-9e65-00163efa374e",
//     "TID_IsGAADMIN": 1
// }