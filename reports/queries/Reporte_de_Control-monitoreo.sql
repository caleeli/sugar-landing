select
    date_entered as "Fecha de Creación",
    date_modified as "Última Modificación",
    crm_landing_code_c as "Código landing",
    cc_producto_descripcion_c as "Descripción",
    deleted as "Eliminado",
    salutation as "Saludo",
    first_name as "Nombre",
    last_name as "Apellidos",
    status as "Estado",
    sfi_nro_prestamo_c as "Nro. préstamo",
    crm_amount_c as "Monto en Bs.",
    sfi_nro_cuenta_c as "Nro. cuenta",
    fecha_conversion_c as "Fecha y hora",
    do_not_call as "No llamar",
    phone_home as "Tel. casa",
    phone_mobile as "Móvil",
    phone_work as "Tel. oficina",
    phone_other as "Tel. alternativo",
    crm_email_c as "Correo electrónico",
    crm_fullname_c as "Nombre de cuenta",
    birthdate as "Cumpleaños",
    crm_enviado_a_sci_c as "Enviado a SCI",
    cc_usuario_nombre_c as "Personal asignado",
    crm_city_c as "Ciudad",
    sci_cod_agenda_c as "Código Agenda SCI",

    sci_estado_credito_desc_c as "Estado crédito",
    sci_estado_credito_char_c as "Cod estado crédito",
    sci_subestado_credito_desc_c as "Sub estado créditos",
    sci_subestado_credito_char_c as "Cod subestado créditos",

    cc_priorizar_c as "¿Priorizar?",
    cc_actividad_c as "Actividad Económica"

from 
    leads left join leads_cstm on (leads.id=leads_cstm.id_c)
where
    date_entered >= DATE_FORMAT(NOW() ,'%Y-01-01')
order by date_entered
