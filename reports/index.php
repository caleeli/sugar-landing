<html>
    <head>
        <style>
            .bloque {
                display: inline-block;
            }
        </style>
    </head>
    <body>
        <div>
            <span class="bloque"><h1>	&#128281;</h1><a href="http://192.168.200.191:8080/contact-center.html?t=<?=microtime(true) ?>">Volver</span>
        </div>
        <?php
        foreach (glob('queries/*.sql') as $file) {
            $id = basename($file, '.sql');
            echo '<span class="bloque"><h1>&#128220;</h1><a target="_blank" download href="report.php?id=' . $id . '">' . $id . '</span>';
        }
        ?>
    </body>
</html>