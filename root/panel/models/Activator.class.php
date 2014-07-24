<?php

class Activator
{
    /**
     * @var Database mysqli
     */
    var $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function activate()
    {
        if (isset($_GET['email']) && preg_match('/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/',
                $_GET['email'])
        ) {
            $email = $_GET['email'];
        }
        if (isset($_GET['key']) && (strlen($_GET['key']) == 32)) {
            //The Activation key will always be 32 since it is MD5 Hash
            $key = $_GET['key'];
        }

        if (isset($email) && isset($key)) {

            // Update the database to set the "activation" field to null

            $query_activate_account = "UPDATE USERS SET Activation=NULL WHERE(EMAIL ='$email' AND ACTIVATION='$key')LIMIT 1";
            $result_activate_account = $this->db->query($query_activate_account);

            // Print a customized message:
            if ($this->db->affected_rows == 1) { //if update query was successfull
                echo '<div>Twoje konto jest od teraz aktywne. Zaloguj się <a href="?v">Zaloguj się</a></div>';
            } else {
                echo '<div>Oops ! Link aktywacyjny wygasł. Został już wykorzystany. Jeżeli nie możesz sie zalogować to skontaktuj się z administratorem systemu.</div>';
            }
        } else {
            echo '<div>Błąd. Spradz adres Url (link) czy nie został uszkodzony. Prosimy wchodzić bezposrednio na linki z maili (nie kopiowac).</div>';
        }
    }

}