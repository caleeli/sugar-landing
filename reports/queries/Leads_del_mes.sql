select
    cc_ciudad_nombre_c as "Sucursal",
    cc_agencia_nombre_c as "Agencia/Oficina	Cargo",
	cc_usuario_nombre_c as "Nombre Funcionario",
	phone_mobile as "WIN",
	cc_usuario_email_c as "Correo",
    cc_producto_descripcion_c as "Producto",
    cc_edad_c as "Edad",
    cc_actividad_c as "Actividad",
    date_entered as "Creado",
    date_modified as "Guardado",
    sci_fecha_asignacion_c as "Enviado"

from 
    leads left join leads_cstm on (leads.id=leads_cstm.id_c)
where
    date_modified >= DATE_FORMAT(NOW() ,'%Y-%m-01')
    and cc_usuario_email_c is not null and cc_usuario_email_c!=''
order by date_entered
