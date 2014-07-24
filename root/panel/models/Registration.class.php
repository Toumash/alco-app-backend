<?php
date_default_timezone_set('UTC');

class Registration
{
    /**
     * @var Database mysqli
     */
    var $db;
    var $normalOrAPI;

    public static function validateRegister($login, $email, $password,
                                            $rePassword)
    {
        $error = array();

        if (!preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/",
            $email)
        ) {
            $error[] = 'Błędny adres email<br/>';
        }
        if ($password != $rePassword) {
            $error[] = 'Hasłą różnią się<br/>';
        }
        if (strlen($password) <= 4) {
            $error[] = 'Hasło za krótkie. Minimum 5 znaków<br/>';
        }
        if (strlen($login) <= 3) {
            $error[] = 'Login za krótki. Minimum 4 znaki<br/>';
        }

        return $error;
    }

    public static function sendMail($email, $subject, $content,
                                    $contentNonHTML = '')
    {
        require_once(ROOT . '/lib/phpmailer/class.phpmailer.php');

        $mail = new PHPMailer;

        $mail->IsMail(); // Set mailer to use SMTP
        $mail->Host = 'localhost'; // Specify main and backup server
        $mail->SMTPAuth = true; // Enable SMTP authentication
        $mail->Username = 'registration@code-sharks.pl'; // SMTP username
        $mail->Password = 'VaderSeeYou'; // SMTP password
        $mail->SMTPSecure = 'ssl';
        $mail->SMTPDebug = 1; // Enable encryption, 'ssl' also accepted
        $mail->CharSet = "UTF-8";

        $mail->From = 'registration@code-sharks.pl';
        $mail->FromName = 'CodeSharks Team';
        $mail->AddAddress($email);
//$mail->AddReplyTo('info@example.com', 'Information');

        $mail->WordWrap = 50; // Set word wrap to 50 characters
//$mail->AddAttachment('/var/tmp/file.tar.gz');         // Add attachments
//$mail->AddAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name
        $mail->IsHTML(true); // Set email format to HTML

        $mail->Subject = $subject;
        $mail->Body = $content;
        if ($content != '')
            $mail->AltBody = $contentNonHTML;

        if (!$mail->Send()) {
            echo 'Message could not be sent.';
            echo 'Mailer Error: ' . $mail->ErrorInfo;
            return false;
        } else {
            return true;
        }
    }

    public function echo_contact_admin()
    {
        if ($this->normalOrAPI)
            echo ' Przepraszamy, wystąpił błąd, prosimy spróbować ponownie. Jeśli ten problem się powtarza skontaktuj sie z administratorem: admin@code-sharks.pl';
        else {
            return array('result' => 'ERROR');
        }
    }

    public function __construct($_db, $normalOrAPI)
    {
        $this->db = $_db;
        $this->normalOrAPI = $normalOrAPI;
    }

    public function handleRegister($newLogin, $newEmail, $newPassword,
                                   $reNewPassword)
    {
        $displayed = false;
        if (!empty($newLogin) && !empty($newEmail) && !empty($newPassword)) {

            $register_errors = self::validateRegister($newLogin, $newEmail,
                $newPassword, $reNewPassword);

            if (empty($register_errors)) {
                $REGF = array(
                    "login" => $this->db->real_escape_string($newLogin),
                    "email" => $this->db->real_escape_string($newEmail),
                    "password" => $this->db->real_escape_string($newPassword),
                    "rePassword" => $this->db->real_escape_string($reNewPassword)
                );
                // Make sure the email address is available:
                $query_verify_email = "SELECT * FROM USERS  WHERE EMAIL ='" . $REGF['email'] . "'";
                $result_verify_email = $this->db->query($query_verify_email);
                if (!$result_verify_email) {
                    $this->echo_contact_admin();
                }

                if (mysqli_num_rows($result_verify_email) == 0) {

                    $activation = md5(uniqid(rand(), true));
                    $finalPassword = md5($REGF['password']);
                    $resultInsert = $this->db->query("INSERT INTO users(login,email,password,activation) VALUES(' {$REGF['login']}','{$REGF['email']}','{$finalPassword}','{$activation}')");
                    if (!$resultInsert) {
                        $this->echo_contact_admin();
                        Log::d('REGISTER::' . $REGF['login'] . ': ERROR REGISTERING: DB no connection');
                    }
                    if ($this->db->affected_rows == 1) {
                        $message = " Aby aktywować konto, kliknij na poniższy link:\n\n";
                        $message .= '<a href="http://dev.code-sharks.pl/manage?v=a&email=' . urlencode($newEmail) . '&key=' . $activation . '">Aktywuj</a>';

                        if (self::sendMail($newEmail,
                            'Potwierdzenie Rejestracji', $message)
                        ) {
                            if ($this->normalOrAPI) {
                                echo '<div class="success">Dziękujemy za rejestrację. Email z kodem aktywacyjnym dla twojego konta został wysłany na ' . $newEmail
                                    . ' Kliknij go aby aktywować konto </div>';
                            } else {
                                return array('result' => 'ok');
                            }
                            Log::d('REGISTER::' . $REGF['login'] . ': REGISTERED');
                        } else {
                            $this->echo_contact_admin();
                            Log::d('REGISTER::' . $REGF['login'] . ': ERROR REGISTERING: sending email error');
                        }
                    } else {
                        $this->echo_contact_admin();
                        Log::d('REGISTER::' . $REGF['login'] . ': ERROR REGISTERING: no rows affected');
                    }
                } else {
                    if ($this->normalOrAPI)
                        $register_errors[] = 'mail juz w użyciu';
                    else {
                        $register_errors[] = 'EMAIL_IN_USE';
                    }
                }
            } else {
                foreach ($register_errors as $error) {
                    echo $error . '</br>';
                }
                $displayed = true;
                echo '<a href="?v=register">Wróć</a>';
            }

            if (!empty($register_errors)) {
                if (!$displayed) {
                    if ($this->normalOrAPI) {
                        foreach ($register_errors as $error) {
                            echo $error . '</br>';
                        }
                    } else {
                        return array('nfo' => $register_errors, 'result' => 'ERROR');
                    }
                }
                echo '<a href="?v=register">Wróć</a>';
            }
            echo '</center>';
        } else {
            if ($this->normalOrAPI) {
                ?>

                <h2>Stwórz nowe konto</h2>
                <fieldset style="width: 30%;padding:0;padding-left:0.5em;">
                    <legend>Rejestracja</legend>
                    <form action=" " method="POST">
                        <table>
                            <tr>
                                <td><label for="newLogin">Login: </label></td>
                                <td><input type="text" name="newLogin" id="newLogin" placeholder="J@nK0w@75ki"></td>
                            </tr>
                            <tr>
                                <td><label for="newEmail">Email: </label></td>
                                <td><input type="email" name="newEmail" id="newEmail" placeholder="someone@example.com">
                                </td>
                            </tr>
                            <tr>
                                <td><label for="newPassword">Hasło: </label></td>
                                <td><input type="password" name="newPassword" id="newPassword" placeholder="Pa55wOrd">
                                </td>
                            </tr>
                            <tr>
                                <td><label for="reNewPassword">Powtórz hasło: </label></td>
                                <td><input type="password" name="reNewPassword" id="reNewPassword"
                                           placeholder="Pa55word"></td>
                            </tr>
                            <tr>
                                <td><input type="submit" value="Zarejestruj"></td>
                                <td></td>
                            </tr>
                        </table>
                    </form>
                </fieldset>
            <?php
            } else {
                return array('result' => 'ERROR', 'nfo' => 'NO_DATA');
            }
        }
    }

}
