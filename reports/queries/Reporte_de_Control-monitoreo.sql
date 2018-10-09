select
    DATE_FORMAT(CONVERT_TZ(date_entered,'+00:00','-04:00'), '%Y-%m-%d') as "Fecha de Creación",
    DATE_FORMAT(CONVERT_TZ(date_entered,'+00:00','-04:00'), '%H:%i:%s') as "Hora de Creación",
    DATE_FORMAT(CONVERT_TZ(date_modified,'+00:00','-04:00'), '%Y-%m-%d') as "Última Modificación",
    DATE_FORMAT(CONVERT_TZ(date_modified,'+00:00','-04:00'), '%H:%i:%s') as "Hora ultima modificación",
    crm_landing_code_c as "Código landing",
    crm_canal_ingreso_c as "Canal de ingreso",
    cc_producto_descripcion_c as "Descripción",
    first_name as "Nombre",
    last_name as "Apellidos",
    (case status
        when 'New' then 'No filtrado'
        when 'Assigned' then 'Asignado'
        when 'In Process' then 'En gestión'
        when 'Converted' then 'Convertido'
        when 'Recycled' then 'Reciclado'
        when 'Dead' then 'Expirado'
        when 'NoDesea' then 'No desea'
        when 'NoCalifica' then 'No califica'
        when 'Inubicable' then 'Inubicable'
        when 'Anulado' then 'Anulado'
        when 'Duplicado' then 'Duplicado'
        when 'Guardado' then 'Guardado'
        when 'Equivocado' then 'Número Equivocado'
        when 'ConversionDirecta' then 'Conversión Directa'
        when 'EnTransito' then 'En tránsito'
        else status
        end
    ) as "Estado",
    sfi_nro_prestamo_c as "Nro. préstamo",
    crm_amount_c as "Monto en Bs.",
    sfi_nro_cuenta_c as "Nro. cuenta",
    DATE_FORMAT(CONVERT_TZ(fecha_conversion_c,'+00:00','-04:00'), '%Y-%m-%d') as "Fecha Conversion",
    DATE_FORMAT(CONVERT_TZ(fecha_conversion_c,'+00:00','-04:00'), '%H:%i:%s') as "Hora Conversion",
    phone_home as "Tel. casa",
    phone_mobile as "Móvil",
    crm_email_c as "Correo electrónico",
    (case status
        when 'Converted' then concat(first_name, ' ', last_name)
        else ''
        end
    ) as "Nombre de cuenta",
    birthdate as "Cumpleaños",
    DATE_FORMAT(CONVERT_TZ(sci_fecha_asignacion_c,'+00:00','-04:00'), '%Y-%m-%d') as "Fecha Asignacion",
    DATE_FORMAT(CONVERT_TZ(sci_fecha_asignacion_c,'+00:00','-04:00'), '%H:%i:%s') as "Hora Asignacion",
    cc_usuario_cargo_c as "Cargo personal asignado",
    cc_usuario_nombre_c as "Personal asignado",
    cc_agencia_nombre_c as "Agencia",
    cc_ciudad_nombre_c as "Sucursal",
    DATE_FORMAT(CONVERT_TZ(sci_oficial_asignado_fecha_c,'+00:00','-04:00'), '%Y-%m-%d') as "Fecha Oficial Asignado",
    DATE_FORMAT(CONVERT_TZ(sci_oficial_asignado_fecha_c,'+00:00','-04:00'), '%H:%i:%s') as "Hora Oficial Asignado",
    sci_oficial_asignado_c as "Oficial asignado",
    crm_city_c as "Ciudad",
    sci_cod_agenda_c as "Código Agenda SCI",

    sci_estado_credito_desc_c as "Estado crédito",
    sci_estado_credito_char_c as "Cod estado crédito",
    sci_subestado_credito_desc_c as "Sub estado créditos",
    sci_subestado_credito_char_c as "Cod subestado créditos",
    sfi_monto_c as "Saldo/Monto",

    (case cc_priorizar_c
        when 1 then 'SI'
        else 'NO'
        end
    ) as "¿Priorizar?",
    cc_actividad_c as "Actividad Económica",

    (select concat(first_name, ' ', last_name) from users where users.id=cc_usuario_cc_c) as "Usuario contact center",
    leads.id as "Nro de oportunidad"

from 
    leads left join leads_cstm on (leads.id=leads_cstm.id_c)
where
    date_entered >= DATE_FORMAT(NOW() ,'%Y-01-01')
order by date_entered
