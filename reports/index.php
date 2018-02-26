<html>
    <head>
        <style>
            .bloque {
                display: inline-block;
            }
        </style>
    </head>
    <body>
        <?php
        foreach (glob('queries/*.sql') as $file) {
            $id = basename($file, '.sql');
            echo '<span class="bloque"><h1>&#128220;</h1><a target="_blank" download href="report.php?id=' . $id . '">' . $id . '</span>';
        }
        ?>
    </body>
</html>