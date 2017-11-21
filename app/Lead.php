<?php

namespace App;
use Exception;

/**
 * Get Leads
 *
 * @author davidcallizaya
 */
class Lead
{
    const COD_AGENDA = 'sci_cod_agenda_c';
    const FULLNAME = 'crm_fullname_c';
    const VARIANT = 'crm_variant_c';
    const AMOUNT = 'crm_amount_c';
    const PHONE = 'phone_mobile';
    const CITY = 'crm_city_c';
    const LANDING_CODE = 'crm_landing_code_c';

    private static $alias = [
        'variant' => 'crm_variant_c',
    ];

    /**
     *
     * @param string $codigo
     */
    public static function findByAgenda($codigo)
    {
        $sugar = Sugar::getConnection();
        $where = self::COD_AGENDA . "='$codigo'";
        $leads = $sugar->get(
                "Leads", ['id', self::COD_AGENDA],
                [
                'where' => $where
                ]
            );
        if (empty($leads)) {
            throw new Exception("Lead not found for " . \App\Lead::COD_AGENDA . "=$codigo");
        }
        return $leads[0];
    }

    public static function update($id, $data)
    {
        $sugar = Sugar::getConnection();
        $where = "id='$id'";
        $leads = $sugar->get(
                "Leads", ['id'],
                [
                'where' => $where
                ]
            );
        if (empty($leads)) {
            throw new Exception("Lead not found $id");
        }
        $data['id'] = $id;
        return $sugar->set("Leads", $data);
    }

    public static function findFromLanding($query)
    {
        $like = '%' . preg_replace('/\s+|\'"/', '%', $query) . '%';
        $sugar = Sugar::getConnection();
        $where = self::FULLNAME . " like '$like' OR "
            . self::PHONE . " like '$like' OR "
            . self::CITY . " like '$like'";
        return $sugar->get(
                "Leads", [
                    'id',
                    self::FULLNAME,
                    self::PHONE,
                    self::CITY,
                    self::AMOUNT,
                    'date_entered',
                    self::LANDING_CODE,
                ],
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

    public static function fromUnbounce($input)
    {
        $data = [];
        foreach ($input as $key => $value) {
            $key = isset(self::$alias[$key]) ? self::$alias[$key] : $key;
            $data[$key] = implode(';', $value);
        }
        return $data;
    }
}
