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
    const FULLNAME = 'crm_fullname_c';
    const VARIANT = 'crm_variant_c';
    const PHONE = 'crm_phone_c';
    const CITY = 'crm_city_c';
    const LANDING_CODE = 'crm_landing_code_c';

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
            )[0];
    }

    public static function save($data)
    {
        $sugar = Sugar::getConnection();
        return $sugar->set("Leads", $data);
    }

    public static function findFromLanding($query)
    {
        $like = '%' . preg_replace('/\s+|\'"/', '%', $query) . '%';
        $sugar = Sugar::getConnection();
        $where = self::FULLNAME . " like '$like' OR"
            . self::PHONE . " like '$like' OR"
            . self::CITY . " like '$like'";
        return $sugar->get(
                "Leads", ['id', self::FULLNAME, self::PHONE, self::CITY],
                [
                'where' => $where
                ]
            );
    }
}
