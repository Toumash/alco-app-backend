<?php
header('Content-Type: text/html; charset=utf-8');
require_once(ROOT . '/lib/Database.class.php');
require_once(ROOT . '/panel/models/Api.class.php');
require_once(ROOT . '/panel/Log.class.php');
require_once(ROOT . '/panel/login.function.php');
require_once(ROOT . '/panel/models/Registration.class.php');
require_once(ROOT . '/lib/bitbucket/bitbucket.lib.php');

$db = new Database();
$api = new Api($db);

function runAPI()
{
    ob_start();
    if (($input = file_get_contents("php://input")) != null) {
        $JSON = json_decode($input, true);
        $JSON_dump = $JSON;
        if (isset($JSON_dump['password']))
            $JSON_dump['password'] = 'HIDDEN';
        Log::d('API RQ: ' . json_encode($JSON_dump), Log::$API_LOG);
        global $api;
        global $db;
        $result = array();
        $JSON['action'] = isset($JSON['action']) ? $JSON['action'] : "";
        switch ($JSON['action']) {
            case 'login':
                $result['result'] = $api->login($JSON['login'], $JSON['password']);
                break;
            case 'register':
                $reg = new Registration($db, false);
                $result = $reg->handleRegister($JSON['login'], $JSON['email'],
                    $JSON['password'], $JSON['password']);
                break;
            case 'upload':
                $result['result'] = $api->userUpload($JSON);
                break;
            case 'flag':
                $result['result'] = $api->flagAlcohol($JSON);
                break;
            case 'comment':
                $result['result'] = $api->commentAlcohol($JSON);
                break;
            case 'fetchComments':
                $result = $api->fetchComments($JSON);
                break;
            case 'issue':
                $result['result'] = $api->reportIssue($JSON);
                break;
            case 'profileDownload':
                $result['result'] = $api->downloadProfile($JSON);
                $result['profile'] = $api->profileData;
                break;
            default :
                $result['result'] = 'EMPTY_ACTION';
        }
        $result['ACTION'] = $JSON['action'];
        echo '<json>';
        echo json_encode($result);
        echo '</json>';
    } else if (isset($_GET['db'])) {
        global $api;
        $result = array();
        switch ($_GET['db']) {
            case 'main':
                $result = $api->fetchMainDB();
                break;
            case 'users':
                $result = $api->fetchUserDB();
                break;
        }
        echo '<json>';
        echo json_encode($result);
        echo '</json>';
    }
    $output = ob_get_contents();
    ob_end_clean();
    return $output;
}

?>
<!doctype html>
<html>
<head>
    <meta http-equiv="Content-Type" content="application/json; charset=utf-8">
    <title>CS_API</title>
</head>
<body style="background-color:black;color:green;">

<content>
    <?php
    echo runAPI();

    $db->close();
    ?>
</content>
</body>
</html>