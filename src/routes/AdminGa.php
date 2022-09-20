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
//$app->get(
//    '/api/ga-admins/all',
//    function (Request $request, Response $response) {
//        $body = json_decode($request->getBody()->getContents(), true);
//        $admin_client_id = isset($body['ga_admin_from_client']) ? $body['ga_admin_from_client'] : null;
//        $haveThePower = isset($body['TID_IsGAADMIN']) ? $body['TID_IsGAADMIN'] : null;
//
//        if (is_null($admin_client_id)) {
//            $response->getBody()->write(json_encode(array('error' => 'No se ha enviado el dato correctamente')));
//            return $response->withStatus(400);
//        } elseif ($admin_client_id && $haveThePower == 1) {
//            $sql="
//                SELECT TID_TalentID, CONCAT(TIN_Name, TIN_Last_Name) AS name, TIN_Email, TID_IsGAADMIN
//                FROM TalentID AS tid
//                LEFT JOIN TalentId_Info AS tii
//                ON tii.TIN_ID = tid.TID_TalentID
//                WHERE tid.TID_TalentID
//            ";
//            $db = new db();
//            $db = $db->connectBD();
//            $stm = $db->prepare($sql);
//            $stm->execute([]);
//            echo $stm->execute([]);
//            $isAdmin = $stm->fetchAll(PDO::FETCH_OBJ);
//            $response->getBody()->write(json_encode($isAdmin));
//        } else {
//            $response->getBody()->write(json_encode(array('error' => 'No tienes permiso para esta operacion.')));
//            return $response;
//        }
//    }
//);

//$app->get(
//    '/api/ga-admins/{id}',
//    function (Request $request, Response $response) {
//        $body = json_decode($request->getBody()->getContents(), true);
//        $admin_client_id = isset($body['ga_admin_from_client']) ? $body['ga_admin_from_client'] : null;
//        $id = $request->getAttribute($admin_client_id);
//        $haveThePower = isset($body['TID_IsGAADMIN']) ? $body['TID_IsGAADMIN'] : null;
//
//        if (is_null($id)) {
//            $response->getBody()->write(json_encode(array('error' => 'No se ha enviado el dato correctamente aqui')));
//            return $response->withStatus(400, $id);
//        } elseif ($id && $haveThePower == 1) {
//            $sql="
//                SELECT TID_TalentID, CONCAT(TIN_Name, TIN_Last_Name) AS name, TIN_Email, TID_IsGAADMIN
//                FROM TalentID AS tid
//                LEFT JOIN TalentId_Info AS tii
//                ON tii.TIN_ID = tid.TID_TalentID
//                WHERE tid.TID_TalentID = $id
//            ";
//            $db = new db();
//            $db = $db->connectBD();
//            $stm = $db->prepare($sql);
//            $stm->execute(['id' => $id]);
//            echo $stm->execute(['id' => $id]);
//            $org_stats = $stm->fetchAll(PDO::FETCH_OBJ);
//            $response->getBody()->write(json_encode($org_stats));
//        } elseif($haveThePower == 0) {
//            $response->getBody()->write(json_encode(array('error' => 'Lo siento. No tienes suficientes permisos.')));
//            return $response;
//        } else {
//            $response->getBody()->write(json_encode(array('error' => 'No tienes permiso para esta operación.')));
//            return $response;
//        }
//    }
//);
// GET /ga-admins/{id}

// POST /ga-admins/create
//$app->post(
//    '/api/ga-admins/create',
//    function (Request $request, Response $response) {
//        $body = $request->getParsedBody();
//        $new_admin_client = isset($body['ga_admin_from_client']) ? $body['ga_admin_from_client'] : null;
//        $id = $request->getAttribute($new_admin_client);
//        $sql="
//            INSERT INTO
//                TalentId_Info (TIN_Name, TIN_Last_Name, TIN_Email)
//                TalentID (TID_IsGAADMIN)
//            VALUES (:TIN_Name :TIN_Last_Name, :TIN_Email, :TID_IsGAADMIN)
//        ";
//        $db = new db();
//        $db = $db->connectBD();
//        $stm = $db->prepare($sql);
//        $stm->execute(['id' => $id]);
//        echo $stm->execute(['id' => $id]);
//        $org_stats = $stm->fetchAll(PDO::FETCH_OBJ);
//        $response->getBody()->write(json_encode($org_stats));
//    }
//);

$app->get(
    '/api/ga-admins/all',
    function (Request $request, Response $response) {
        $body = json_decode($request->getBody()->getContents(), true);
        $admin_id = $body['ga_admin'] ?? null;

        if (is_null($admin_id)) {
            $response->getBody()->write(json_encode(array('error' => 'No se ha enviado el dato correctamente')));
            return $response->withStatus(400);
        } elseif ($admin_id) {
            // validate if the id that comes from the client is an admin
            $sql = "
            SELECT EXISTS (SELECT TID_TalentID, TID_IsGAADMIN FROM TalentID WHERE TID_IsGAADMIN=1 AND TID_TalentID='$admin_id' ) AS isAdmin
            ";
            $db_access = new db();
            $db_access = $db_access->connectBD();

            $stm_admin = $db_access->prepare($sql);
            $stm_admin->execute();
            $canAccess = $stm_admin->fetch(PDO::FETCH_OBJ);
//            if ($canAccess['isAdmin'] == 1) {
            if ($canAccess and $canAccess->isAdmin== 1) {
                $sql="
                    SELECT TID_TalentID, CONCAT(TIN_Name,' ', TIN_Last_Name) AS Name, TID_Email, TID_IsGAADMIN, TIN_Photo
                    FROM TalentID AS tid
                    LEFT JOIN TalentId_Info AS tii
                    ON tii.TIN_TID_ID = tid.TID_TalentID
                    WHERE tid.TID_IsGAADMIN=1
                ";
                $db = new db();
                $db = $db->connectBD();
                $stm = $db->prepare($sql);
                $stm->execute([]);
                $isAdmin = $stm->fetchAll(PDO::FETCH_OBJ);
                $response->getBody()->write(json_encode($isAdmin));
            } else {
                $response->getBody()->write(json_encode(array('error' => 'No tienes permiso para esta operacion.')));
                return $response;
            }
        } else {
            $response->getBody()->write(json_encode(array('error' => 'No tienes permiso para esta operacion.')));
            return $response;
        }
    }
);

// create a post request to create a new organization and validate if the user has permission to do it
// utilizamos la tabla Organizers. Por ejemplo:
// POST /organizations/create
// INSERT INTO Organizers( Org_OT_ID, Org_Leader_ID, Org_Level, Org_Name, Org_Description, Org_Logo, Org_Url) ;
$app->post(
    '/api/organizations/create',
    function (Request $request, Response $response) {
        $body=json_encode($request->getBody()->getContents(), true);
        $Org_OT_ID = $body['Org_OT_ID'] ?? null;
        $Org_Leader_ID = $body['Org_Leader_ID'] ?? null;
        $Org_Level = $body['Org_Level'] ?? null;
        $Org_Name = $body['Org_Name'] ?? null;
        $Org_Description = $body['Org_Description'] ?? null;
        $Org_Logo = $body['Org_Logo'] ?? null;
        $Org_Url = $body['Org_Url'] ?? null;

        if (is_null($Org_Leader_ID) || is_null($Org_Level) || is_null($Org_Name) || is_null($Org_Description) || is_null($Org_Logo) || is_null($Org_Url)) {
            $response->getBody()->write(json_encode(array('error' => 'No se ha enviado el dato correctamente')));
            return $response->withStatus(400);
        } elseif ($Org_Leader_ID) {
            $sql = "
            SELECT EXISTS (SELECT TID_TalentID, TID_IsGAADMIN FROM TalentID WHERE TID_IsGAADMIN=1 AND TID_TalentID='$Org_Leader_ID' ) AS isAdmin
            ";
            $db_access = new db();
            $db_access = $db_access->connectBD();

            $stm_admin = $db_access->prepare($sql);
            $stm_admin->execute();
            $canAccess = $stm_admin->fetch();
            if ($canAccess['isAdmin'] == 1) {
                $sql="
                    INSERT INTO Organizers( Org_OT_ID, Org_Leader_ID, Org_Level, Org_Name, Org_Description, Org_Logo, Org_Url)
                    VALUES (:Org_OT_ID, :Org_Leader_ID, :Org_Level, :Org_Name, :Org_Description, :Org_Logo, :Org_Url)
                ";
                $db = new db();
                $db = $db->connectBD();
                $stm = $db->prepare($sql);
                $stm->execute(['Org_OT_ID' => $Org_OT_ID, 'Org_Leader_ID' => $Org_Leader_ID, 'Org_Level' => $Org_Level, 'Org_Name' => $Org_Name, 'Org_Description' => $Org_Description, 'Org_Logo' => $Org_Logo, 'Org_Url' => $Org_Url]);
                echo $stm->execute(['Org_OT_ID' => $Org_OT_ID, 'Org_Leader_ID' => $Org_Leader_ID, 'Org_Level' => $Org_Level, 'Org_Name' => $Org_Name, 'Org_Description' => $Org_Description, 'Org_Logo' => $Org_Logo, 'Org_Url' => $Org_Url]);
                $isAdmin = $stm->fetchAll(PDO::FETCH_OBJ);
                $response->getBody()->write
                (json_encode($isAdmin));
            } else {
                $response->getBody()->write(json_encode(array('error' => 'No tienes permiso para esta operacion.')));
                return $response;
            }
        } else {
            $response->getBody()->write(json_encode(array('error' => 'No tienes permiso para esta operacion.')));
            return $response;
        }
    }

);

























//$app->post(
//    '/api/organizations/create',
//    function (Request $request, Response $response) {
//        $body = json_decode($request->getBody()->getContents(), true);
//        $org_name = $body['org_name'] ?? null;
//        $org_email = $body['org_email'] ?? null;
//        $org_phone = $body['org_phone'] ?? null;
//        $org_address = $body['org_address'] ?? null;
//        $org_city = $body['org_city'] ?? null;
//        $org_state = $body['org_state'] ?? null;
//        $org_zip = $body['org_zip'] ?? null;
//        $org_country = $body['org_country'] ?? null;
//        $org_admin = $body['org_admin'] ?? null;
//
//        if (is_null($org_name) || is_null($org_email) || is_null($org_phone) || is_null($org_address) || is_null($org_city) || is_null($org_state) || is_null($org_zip) || is_null($org_country) || is_null($org_admin)) {
//            $response->getBody()->write(json_encode(array('error' => 'No se ha enviado el dato correctamente')));
//            return $response->withStatus(400);
//        } elseif ($org_name && $org_email && $org_phone && $org_address && $org_city && $org_state && $org_zip && $org_country && $org_admin) {
//            // validate if the id that comes from the client is an admin
//            $sql = "
//            SELECT EXISTS (SELECT TID_TalentID, TID_IsGAADMIN FROM TalentID WHERE TID_IsGAADMIN=1 AND TID_TalentID='$org_admin' ) AS isAdmin
//            ";
//            $db_access = new db();
//            $db_access = $db_access->connectBD();
//
//            $stm_admin = $db_access->prepare($sql);
//            $stm_admin->execute();
//            $canAccess = $stm_admin->fetch();
//            if ($canAccess['isAdmin'] == 1) {
//                $sql="
//                    INSERT INTO
//                        Organization (ORG_Name, ORG_Email, ORG_Phone, ORG_Address, ORG_City, ORG_State, ORG_Zip, ORG_Country)
//                    VALUES (:ORG_Name, :ORG_Email, :ORG_Phone, :ORG_Address, :ORG_City, :ORG_State, :ORG_Zip, :ORG_Country)
//                ";
//                $db = new db();
//                $db = $db->connectBD();
//                $stm = $db->prepare($sql);
//                $stm->execute(['org_name' => $org_name, 'org_email' => $org_email, 'org_phone' => $org_phone, 'org_address' => $org_address, 'org_city' => $org_city, 'org_state' => $org_state, 'org_zip' => $org_zip, 'org_country' => $org_country]);
//                echo $stm->execute(['org_name' => $org_name, 'org_email' => $org_email, 'org_phone' => $org_phone, 'org_address' => $org_address, 'org_city' => $org_city, 'org_state' => $org_state, 'org_zip' => $org_zip, 'org_country' => $org_country]);
//                $org_stats = $stm->fetchAll(PDO::FETCH_OBJ);
//                $response->getBody()->write(json_encode($org_stats));
//            } else {
//                $response->getBody()->write(json_encode(array('error' => 'No tienes permiso para esta operacion.')));
//                return $response;
//            }
//        } else {
//            $response->getBody()->write(json_encode(array('error' => 'No tienes permiso para esta operacion.')));
//            return $response;
//        }
//    }
//);