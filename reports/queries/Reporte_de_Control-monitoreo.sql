select
    date_entered as "Fecha de Creación",
    date_modified as "Última Modificación",
    crm_landing_code_c as "Código landing",
    cc_producto_descripcion as "Descripción",
    deleted as "Eliminado",
    salutation as "Saludo",
    first_name as "Nombre",
    last_name as "Apellidos",
    status as "Estado",
    sfi_nro_prestamo_c as "Nro. préstamo",
    crm_amount_c as "Monto en Bs.",
    sfi_nro_cuenta_c as "Nro. cuenta",
    crm_datetime_c as "Fecha y hora",
    do_not_call as "No llamar",
    phone_home as "Tel. casa",
    phone_mobile as "Móvil",
    phone_work as "Tel. oficina",
    phone_other as "Tel. alternativo",
    (select email_address 
        from email_addr_bean_rel left join email_addresses on (email_addr_bean_rel.email_address_id=email_addresses.id)
        where email_addr_bean_rel.bean_id = leads.id
    ) as "Correo electrónico",
    crm_fullname_c as "Nombre de cuenta",
    birthdate as "Cumpleaños",
    crm_enviado_a_sci_c as "Enviado a SCI",
    crm_city_c as "Ciudad",
    sci_cod_agenda_c as "Código Agenda SCI",

    sci_estado_credito_desc_c as "sci estado credito desc c",
    sci_estado_credito_char_c as "sci estado credito char c",
    sci_subestado_credito_desc_c as "Sub estado creditos",
    sci_subestado_credito_char_c as "sci subestado credito char c",

    cc_priorizar_c as "¿Priorizar?",
    cc_actividad_c as "Actividad Económica"

from 
    leads left join leads_cstm on (leads.id=leads_cstm.id_c)
where
    date_entered >= DATE_FORMAT(NOW() ,'%Y-01-01')
order by date_entered
