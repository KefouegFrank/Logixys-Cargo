<?php

return [
    'sections' => [
        1 => "Responsable del tratamiento",
        2 => "Datos que recopilamos",
        3 => "Por qué los utilizamos",
        4 => "Destinatarios de sus datos",
        5 => "Plazo de conservación",
        6 => "Cookies",
        7 => "Sus derechos",
        8 => "Seguridad",
        9 => "Transferencias fuera de la Unión Europea",
        10 => "Contacto y reclamaciones",
        11 => "Modificaciones de la presente política",
    ],
    'intro' => "Esta política describe los datos que :name: recopila cuando visita este sitio, nos contacta o realiza el seguimiento de un envío, por qué, durante cuánto tiempo, y cómo hacer valer sus derechos. Se aplica al sitio público, no al área de administración reservada al personal autorizado.",
    'table' => [
        'headers' => ["Dato", "Finalidad", "Base jurídica"],
        'rows' => [
            ["Formulario de contacto", "Responder a su solicitud", "Interés legítimo en atender las solicitudes que se nos dirigen"],
            ["Datos de envío", "Organizar, ejecutar y facturar el transporte", "Ejecución del contrato de transporte"],
            ["Facturas y documentos contables", "Llevanza de la contabilidad", "Obligación legal"],
            ["Dirección IP (formulario de contacto)", "Prevenir abusos (spam, envíos automatizados)", "Interés legítimo en proteger el servicio"],
        ],
    ],
    'articles' => [
        1 => "<p>El responsable del tratamiento de los datos descritos a continuación es :name:, cuya identidad completa figura en el :notice_link:. Para cualquier pregunta relativa a sus datos, escriba a :email_link:.</p>",
        2 => "<p>Recopilamos únicamente los datos necesarios para las funciones siguientes.</p>
              <ul>
                <li><strong>Formulario de contacto.</strong> Nombre, correo electrónico, teléfono (opcional), asunto y mensaje, junto con la dirección IP y el navegador utilizado al enviar el formulario — útiles para detectar un uso abusivo del mismo.</li>
                <li><strong>Envíos.</strong> Cuando se registra un envío en su nombre, conservamos el nombre, el correo electrónico, el teléfono y la dirección (calle, ciudad, país) del remitente y del destinatario, así como el contenido y la ruta del envío.</li>
                <li><strong>Seguimiento de envíos.</strong> La página de seguimiento muestra el registro completo de un envío a cualquiera que introduzca su número de seguimiento — dicho número actúa como credencial de acceso. Compártalo solo con las personas implicadas en el envío.</li>
                <li><strong>Datos técnicos.</strong> Una cookie de sesión estrictamente necesaria para el funcionamiento del sitio (véase el §6). No utilizamos cookies publicitarias ni herramientas de medición de audiencia.</li>
              </ul>",
        3 => "<p>Tratamos cada categoría de datos por un motivo concreto, nunca para una finalidad que no lo justifique:</p>",
        4 => "<p>Sus datos son accesibles para el personal autorizado de :name:, y se transmiten a:</p>
              <ul>
                <li><strong>Resend</strong> (proveedor de envío de correos electrónicos) — para hacerle llegar los correos de confirmación y notificación que le debemos.</li>
                <li><strong>OpenStreetMap</strong> — cuando se muestra un mapa (página de contacto, seguimiento de envío), su navegador carga las imágenes del mapa directamente desde los servidores de OpenStreetMap, lo que les revela su dirección IP. No se comparte ningún otro dato con este servicio.</li>
                <li><strong>Geoapify / LocationIQ</strong> — herramientas de búsqueda de direcciones que nuestro equipo utiliza internamente al registrar un envío, para convertir una dirección en una posición en el mapa.</li>
              </ul>
              <p>No vendemos ni alquilamos sus datos a terceros, ni los utilizamos con fines publicitarios.</p>",
        5 => "<ul>
                <li><strong>Mensajes del formulario de contacto:</strong> hasta 24 meses desde el último intercambio, y después se eliminan.</li>
                <li><strong>Datos de envío y documentos de facturación:</strong> conservados durante el plazo legal de conservación de los documentos contables, 10 años en Francia.</li>
                <li><strong>Cookie de sesión:</strong> se elimina al cerrar el navegador o tras :hours: hora(s) de inactividad.</li>
              </ul>",
        6 => "<p>Este sitio utiliza una única cookie, estrictamente necesaria para su funcionamiento: una cookie de sesión que le mantiene conectado de una página a otra y protege los formularios contra envíos fraudulentos (token CSRF). No sirve ni para el seguimiento publicitario ni para la medición de audiencia, por lo que no requiere consentimiento.</p>
              <p>No utilizamos ninguna herramienta de medición de audiencia (Google Analytics o similar), ni cookies publicitarias o de redes sociales. Si esto cambiara, se implementaría un banner de consentimiento conforme antes de instalar cualquier cookie no esencial.</p>",
        7 => "<p>De conformidad con el Reglamento General de Protección de Datos (RGPD), usted dispone de los siguientes derechos sobre sus datos:</p>
              <ul>
                <li><strong>Acceso:</strong> obtener una copia de los datos que tenemos sobre usted.</li>
                <li><strong>Rectificación:</strong> corregir datos inexactos o incompletos.</li>
                <li><strong>Supresión:</strong> solicitar la eliminación de sus datos, dentro de los límites de nuestras obligaciones legales de conservación.</li>
                <li><strong>Limitación:</strong> solicitar la suspensión temporal de un tratamiento.</li>
                <li><strong>Portabilidad:</strong> recibir sus datos en un formato estructurado y reutilizable.</li>
                <li><strong>Oposición:</strong> oponerse a un tratamiento basado en nuestro interés legítimo.</li>
              </ul>
              <p>Para ejercer cualquiera de estos derechos, escriba a :email_link:. Respondemos en un plazo de un mes. Si considera que no se respetan sus derechos, puede presentar una reclamación ante la :cnil_link: (la autoridad francesa de protección de datos).</p>",
        8 => "<p>Los intercambios con este sitio están cifrados (HTTPS). El acceso al área de administración está reservado al personal autorizado, protegido por contraseña, y cada cuenta puede desactivarse individualmente. Ningún sistema es infalible, por lo que limitamos la recopilación a los datos estrictamente necesarios descritos anteriormente.</p>",
        9 => "<p>Procuramos conservar sus datos dentro de la Unión Europea. OpenStreetMap, utilizado para mostrar los mapas, está operado por una fundación con sede en el Reino Unido, país reconocido por la Comisión Europea como garante de un nivel adecuado de protección de datos personales.</p>",
        10 => "<p>Para cualquier pregunta sobre esta política o sobre sus datos, escriba a :email_link:. Dado el tamaño de la empresa y la naturaleza de los tratamientos descritos anteriormente, la normativa no exige designar un delegado de protección de datos (DPO).</p>",
        11 => "<p>Esta política puede actualizarse, en particular para reflejar una evolución del sitio o de la normativa. La fecha de la última actualización figura en la parte superior de esta página; le invitamos a consultarla periódicamente.</p>",
    ],
    'link_notice' => "aviso legal",
];
