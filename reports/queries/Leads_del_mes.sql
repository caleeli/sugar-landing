select
    cc_ciudad_nombre_c as "Sucursal",
    cc_agencia_nombre_c as "Agencia/Oficina	Cargo",
	cc_usuario_nombre_c as "Nombre Funcionario",
	phone_mobile as "WIN",
	cc_usuario_email_c as "Correo",
    date_entered as "Creado",
    crm_datetime_c as "Guardado",
    sci_fecha_asignacion_c as "Enviado"

from 
    leads left join leads_cstm on (leads.id=leads_cstm.id_c)
where
    date_entered >= DATE_FORMAT(NOW() ,'%Y-%m-01')
order by date_entered
