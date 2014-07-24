<?php
/* ******************************************************************************
 * Bitbucket External Issue Submission Library
 * Author - Sherri Wheeler - Avinus Web Services - http://SyntaxSeed.com/
 * License - GPLv3 (http://www.gnu.org/licenses/quick-guide-gplv3.html)
 * Version - 1.0
 * ****************************************************************************** */


/* Function submitBug - sends the bug to the bitbucket API. Must contain title and content. User name/email is optional.*/
function submitBug($title, $content, $user = 'Anonymous', $bbAccount, $bbRepo, $basicAuth, $component = '', $status = 'new', $priority = 'major', $kind = 'bug')
{

    $url = 'https://api.bitbucket.org/1.0/repositories/' . $bbAccount . '/' . $bbRepo . '/issues/';
    $ch = curl_init($url);

    if (get_magic_quotes_gpc()) {
        $title = stripslashes($title);
        $content = stripslashes($content);
        $user = stripslashes($user);
        $component = stripslashes($component);
        $bbAccount = stripslashes($bbAccount);
        $bbRepo = stripslashes($bbRepo);
    }

    $fields = array(
        'title' => urlencode($title),
        'content' => urlencode($content . "\n\nSubmitted By: " . $user),
        'status' => urlencode($status),
        'priority' => urlencode($priority),
        'kind' => urlencode($kind),
        // 'component' => urlencode($component)
    );

    $fieldsStr = '';
    foreach ($fields as $key => $value) {
        $fieldsStr .= $key . '=' . $value . '&';
    }
    rtrim($fieldsStr, '&');

    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Basic ' . $basicAuth));
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, count($fields));
    curl_setopt($ch, CURLOPT_POSTFIELDS, $fieldsStr);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);


    $response = curl_exec($ch);
    curl_close($ch);

    //print_r($response);  // Debugging

    if ($response !== FALSE) {
        $response = json_decode($response);
        if (isset($response->local_id) && intval($response->local_id) > 0) {
            $bugurl = "https://bitbucket.org/" . ltrim($response->resource_uri, "/1.0/repositories/");
            $bugurl = str_replace('/issues/', '/issue/', $bugurl);
            return (array('issueid' => $response->local_id, 'issueurl' => $bugurl));
        } else {
            return (FALSE);
        }
    } else {
        return (FALSE);
    }

}

/* Function - sendBugEmail sends an email with details of the submitted bug to the user for their reference. 
 * Leave the $url parameter blank for private repos. 
 */
function sendBugEmail($email, $bugNum, $siteName, $url = FALSE)
{
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $subject = "Bug dodany do " . $siteName . " (# " . $bugNum . ")";
        $body = "Dziękujemy za dodanie buga do  " . $siteName . "\n\n";
        $body .= "Twój bug (# " . $bugNum . ") został odebrany, i zostaniesz informowany na bierząco o jego stanie poprzez maila. Zapamiętaj id buga.\n\n";
        if ($url !== FALSE && !empty($url)) {
            $body .= "Sprawdź status zgłoszenia: " . $url . ".\n\n";
        }

        $body .= "Support Team\n" . $siteName;
        require_once ROOT . '/lib/sendMail.php';
        sendMail($email, $subject, $body);
    }
}
