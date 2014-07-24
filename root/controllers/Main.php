<?php

function getContent($view, $db, $permission, $action, $id, $return = false)
{
    if ($return == true) ob_start();
    /* @var $view string */
    if (isset($view)) {
        switch ($view) {
            case 'db':
                require_once(ROOT . '/panel/models/AlcoholDisplay.class.php');
                $alcoholDisplay = new AlcoholDisplay($db, $permission);
                $alcoholDisplay->startModule($action, $id);
                break;
            case 'register':
                require_once(ROOT . '/panel/models/Registration.class.php');
                $reg = new Registration($db, true);
                $reg->handleRegister(isset($_POST['newLogin']) ? $_POST['newLogin'] : "",
                    isset($_POST['newEmail']) ? $_POST['newEmail'] : "",
                    isset($_POST['newPassword']) ? $_POST['newPassword'] : "",
                    isset($_POST['reNewPassword']) ? $_POST['reNewPassword'] : "");
                break;
            case 'help':
                require_once(ROOT . '/panel/models/help.php');
                break;
            case 'g':
                require_once ROOT . '/panel/models/Gallery.class.php';
                $gallery = new Gallery();
                $gallery->index();
                break;
            case 'a':
                require_once(ROOT . '/panel/models/Activator.class.php');
                $activator = new Activator($db);
                $activator->activate();
                break;
            case 'l':
                if ($permission >= 6)
                    require_once(ROOT . '/panel/models/log.php');
                else
                    showPermissionAlert();
                break;
            case 'lApi':
                if ($permission >= 6)
                    require_once ROOT . '/panel/models/logApi.php';
                else
                    showPermissionAlert();
                break;
            default:
                require_once(ROOT . '/panel/models/hello.php');
                break;
        }
    } else {
        require_once(ROOT . '/panel/models/hello.php');
    }
    if ($return == true) {
        $output = ob_get_contents();
        ob_end_clean();
        return $output;
    }
    return null;
}

function showPermissionAlert()
{
    echo 'Nie posiadasz uprawnień dostępu do tej treści. Prosimy zalogować się z odpowiednim poziomem dostępu';
}

function showPermissionError($required, $users_lvl)
{
    echo 'Nie posiadasz uprawnień dostępu do tej treści. Twój poziom uprawnień to: ' . $users_lvl . ', a wymagany jest:' . $required . '<br>Prosimy zalogować się z wiekszym poziomem dostępu';
}