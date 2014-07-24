<?php

namespace Views {

    function create_footer($db)
    {
        #PERFORMANCE MEASUREMENT
        $performanceQuery = $db->query("SHOW STATUS WHERE Variable_name IN ( 'Com_select', 'Com_update', 'Com_insert', 'Com_delete' )");
        $i = 0;
        while ($row = $performanceQuery->fetch_assoc()) {
            $i = $i + $row['Value'];
        }
        return '<span class="sql-performance">' . 'Zapytań SQL:' . $i . '</span>';
        #PERFORMANCE MEASURE END
    }

    function create_menu()
    {
        ob_start();
        ?>
        <ul>
            <li><a href="?" data-v="" title="Strona główna">Strona główna</a></li>
            <?php if ($_SESSION['PERMISSION'] == 0): ?>
                <li><a href="?v=register" data-v="register" title="Rejestracja">Rejestracja</a></li>
            <?php endif; ?>
            <li><a href="?v=help" data-v="help" title="Pomoc">Pomoc</a></li>
            <li><a href="?v=g" data-v="g" title="Galeria">Galeria</a></li>
            <?php if ($_SESSION['PERMISSION'] >= 5): ?>
                <li><a href="?v=db" data-v="db" title="Usuwanie">Bazy Danych</a></li>
            <?php endif;
            if ($_SESSION['PERMISSION'] >= 6):
                ?>
                <li><a href="?v=l" data-v="l" title="Log">Main Log</a></li>
                <li><a href="?v=lApi" data-v="lApi" title="Log">Api Log</a></li>
            <?php endif;
            if ($_SESSION['auth'] == TRUE):
                ?>
                <li><a href="?logout" data-v="logout" id="logout" title="Wyloguj">Wyloguj</a></li>
            <?php endif; ?>
        </ul>
        <?php
        $output = ob_get_contents();
        ob_end_clean();
        return $output;
    }

    function create_login_form()
    {
        if ($_SESSION['auth'] != TRUE) {
            ob_start();
            ?>
            <div class="login_form">
                <form method="POST" action="?" class="login_form"><p style="margin: 0;padding:0;">
                        <label for="login">Login: </label><input type="text" class="holo" name="login" id="login">
                        <label for="password">Hasło: </label><input type="password" class="holo" name="password"
                                                                    id="password">
                        <input type="submit" value="OK">
                    </p>
                </form>
            </div>
            <?php
            $output = ob_get_contents();
            ob_end_clean();
            return $output;
        } else {
            return "";
        }
    }

}
?>