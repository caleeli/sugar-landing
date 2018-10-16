<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

/**
 * Description of ReporteController
 *
 * @author David Callizaya <davidcallizaya@gmail.com>
 */
class ReporteController extends Controller
{

    private $verificacion;
    private $verificacionMsg;

    public function index()
    {
        return view('report');
    }

    public function report(Request $request)
    {
        if (!$request->has('id')) {
            die('missing argument');
        }
        $id = $request->input('id');
        
        $connection = DB::connection()->getPdo();

        $query = file_get_contents(base_path('reports/queries/' . $id . '.sql'));
        $stmt = $connection->prepare($query);
        $res = $stmt->execute([]);

//header("Content-type: text/csv; charset=UTF-8");
//header("Content-Disposition: attachment; filename=$id.csv");
        header("Content-type: application/vnd.ms-excel; name='excel'");
        header("Content-Disposition: attachment; filename=$id.xls");
        header("Pragma: no-cache");
        header("Expires: 0");

        $encloseAll = false;
        $enclosure = '"';
        $delimiter = ',';
        $delimiter_esc = preg_quote($delimiter, '/');
        $enclosure_esc = preg_quote($enclosure, '/');
        $first = true;
        echo "<html><body><table>\n";
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $output = [];
            if ($first) {
                $header = array_keys($row);
                //echo implode($delimiter, $header), "\n";
                echo '<tr><th>', implode('</th><th>', $header), "</th></tr>\n";
                $first = false;
            }
            foreach ($row as $field) {
                if ($encloseAll || preg_match("/(?:${delimiter_esc}|${enclosure_esc}|\s)/",
                        $field)) {
                    $output[] = $enclosure . str_replace($enclosure,
                            $enclosure . $enclosure,
                            str_replace("\t", ' ', $field)) . $enclosure;
                } else {
                    $output[] = $field;
                }
            }
            //echo implode($delimiter, ($output)), "\n";
            echo '<tr><td>', implode('</td><td>', ($output)), "</td></tr>\n";
        }
        echo '</table></body></html>';
    }
}
