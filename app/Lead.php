<?php

namespace App;

/**
 * Get Leads
 *
 * @author davidcallizaya
 */
class Lead
{
    const COD_AGENDA = 'sci_cod_agenda';

    /**
     *
     * @param string $codigo
     */
    public static function findByAgenda($codigo)
    {
        $sugar = Sugar::getConnection();
        $where = self::COD_AGENDA . "='$codigo'";
        return $sugar->get(
            "Leads", ['id', self::COD_AGENDA],
            [
            'where' => $where
            ]
        );
    }

    public static function save($data)
    {
        $sugar = Sugar::getConnection();
        return $sugar->set("Leads", $data);
    }
}
