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
            <span class="bloque"><a href="/contact-center.html?t=<?=microtime(true) ?>">Volver</span>
        </div>
        <?php
        foreach (glob(base_path('reports/queries/*.sql')) as $file) {
            $id = basename($file, '.sql');
            echo '<span class="bloque"><h1>&#128220;</h1><a target="_blank" download href="report?id=' . htmlentities($id, ENT_QUOTES) . '">' . str_replace('_', ' ', htmlentities($id)) . '</span>';
        }
        ?>
    </body>
</html>