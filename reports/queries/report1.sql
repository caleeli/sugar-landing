select
    cc_ciudad_nombre_c,
    cc_agencia_nombre_c,
	cc_usuario_nombre_c,
	mobile_phone,
	email1
from 
    leads left join leads_cstm on (leads.id=leads_cstm-id_c)
where
    date_entered >= '2018-01-01'
