<?php

return [
    'sections' => [
        1 => "Titolare del trattamento",
        2 => "Dati che raccogliamo",
        3 => "Perché li utilizziamo",
        4 => "Destinatari dei vostri dati",
        5 => "Periodo di conservazione",
        6 => "Cookie",
        7 => "I vostri diritti",
        8 => "Sicurezza",
        9 => "Trasferimenti fuori dall'Unione europea",
        10 => "Contatti e reclami",
        11 => "Modifiche alla presente informativa",
    ],
    'intro' => "Questa informativa descrive i dati che :name: raccoglie quando visitate questo sito, ci contattate o tracciate una spedizione, perché, per quanto tempo, e come farli valere. Riguarda il sito pubblico — non l'area di amministrazione riservata al personale autorizzato.",
    'table' => [
        'headers' => ["Dato", "Finalità", "Base giuridica"],
        'rows' => [
            ["Modulo di contatto", "Rispondere alla vostra richiesta", "Interesse legittimo a gestire le richieste che ci vengono rivolte"],
            ["Dati di spedizione", "Organizzare, eseguire e fatturare il trasporto", "Esecuzione del contratto di trasporto"],
            ["Fatture e documenti contabili", "Tenuta della contabilità", "Obbligo legale"],
            ["Indirizzo IP (modulo di contatto)", "Prevenire abusi (spam, invii automatizzati)", "Interesse legittimo a proteggere il servizio"],
        ],
    ],
    'articles' => [
        1 => "<p>Il titolare del trattamento dei dati descritti di seguito è :name:, la cui identità completa figura nelle :notice_link:. Per qualsiasi domanda relativa ai vostri dati, scrivete a :email_link:.</p>",
        2 => "<p>Raccogliamo solo i dati necessari alle funzioni sottostanti.</p>
              <ul>
                <li><strong>Modulo di contatto.</strong> Nome, e-mail, telefono (facoltativo), oggetto e messaggio, oltre all'indirizzo IP e al browser utilizzato al momento dell'invio — utili per individuare un uso abusivo del modulo.</li>
                <li><strong>Spedizioni.</strong> Quando una spedizione viene registrata per vostro conto, conserviamo nome, e-mail, telefono e indirizzo (via, città, paese) del mittente e del destinatario, oltre al contenuto e al percorso dell'invio.</li>
                <li><strong>Tracciamento spedizione.</strong> La pagina di tracciamento mostra l'intero record di una spedizione a chiunque inserisca il relativo numero di tracciamento — tale numero funge da credenziale di accesso. Comunicatelo solo alle persone coinvolte nella spedizione.</li>
                <li><strong>Dati tecnici.</strong> Un cookie di sessione strettamente necessario al funzionamento del sito (vedi §6). Non utilizziamo cookie pubblicitari né strumenti di analisi del traffico.</li>
              </ul>",
        3 => "<p>Trattiamo ogni categoria di dati per un motivo preciso, mai per una finalità che non lo giustifica:</p>",
        4 => "<p>I vostri dati sono accessibili al personale autorizzato di :name:, e vengono trasmessi a:</p>
              <ul>
                <li><strong>Resend</strong> (fornitore di invio e-mail) — per recapitare le e-mail di conferma e notifica che vi dobbiamo.</li>
                <li><strong>OpenStreetMap</strong> — quando viene visualizzata una mappa (pagina di contatto, tracciamento spedizione), il vostro browser carica le immagini della mappa direttamente dai server di OpenStreetMap, il che comunica loro il vostro indirizzo IP. Nessun altro dato viene condiviso con questo servizio.</li>
                <li><strong>Geoapify / LocationIQ</strong> — strumenti di ricerca indirizzi utilizzati internamente dal nostro team durante la creazione di una spedizione, per convertire un indirizzo in una posizione sulla mappa.</li>
              </ul>
              <p>Non vendiamo né affittiamo i vostri dati a terzi, e non li utilizziamo a fini pubblicitari.</p>",
        5 => "<ul>
                <li><strong>Messaggi del modulo di contatto:</strong> fino a 24 mesi dall'ultimo scambio, poi eliminati.</li>
                <li><strong>Dati di spedizione e documenti di fatturazione:</strong> conservati per la durata legale di conservazione dei documenti contabili, 10 anni in Francia.</li>
                <li><strong>Cookie di sessione:</strong> eliminato alla chiusura del browser o dopo :hours: ora/e di inattività.</li>
              </ul>",
        6 => "<p>Questo sito deposita un unico cookie, strettamente necessario al suo funzionamento: un cookie di sessione che vi mantiene connessi tra una pagina e l'altra e protegge i moduli da invii fraudolenti (token CSRF). Non serve né al tracciamento pubblicitario né alla misurazione del traffico, e non richiede quindi consenso.</p>
              <p>Non utilizziamo alcuno strumento di analisi del traffico (Google Analytics o equivalenti), né cookie pubblicitari o di social network. Se ciò dovesse cambiare, verrà predisposto un banner di consenso conforme prima di depositare qualsiasi cookie non essenziale.</p>",
        7 => "<p>Ai sensi del Regolamento Generale sulla Protezione dei Dati (RGPD), disponete dei seguenti diritti sui vostri dati:</p>
              <ul>
                <li><strong>Accesso:</strong> ottenere una copia dei dati che deteniamo su di voi.</li>
                <li><strong>Rettifica:</strong> far correggere dati inesatti o incompleti.</li>
                <li><strong>Cancellazione:</strong> chiedere la cancellazione dei vostri dati, nei limiti dei nostri obblighi legali di conservazione.</li>
                <li><strong>Limitazione:</strong> chiedere la sospensione temporanea di un trattamento.</li>
                <li><strong>Portabilità:</strong> ricevere i vostri dati in un formato strutturato e riutilizzabile.</li>
                <li><strong>Opposizione:</strong> opporvi a un trattamento basato sul nostro interesse legittimo.</li>
              </ul>
              <p>Per esercitare uno di questi diritti, scrivete a :email_link:. Rispondiamo entro un mese. Se ritenete che i vostri diritti non siano rispettati, potete presentare un reclamo alla :cnil_link: (l'autorità francese di protezione dei dati).</p>",
        8 => "<p>Gli scambi con questo sito sono cifrati (HTTPS). L'accesso all'area di amministrazione è riservato al personale autorizzato, protetto da password, e ogni account può essere disattivato singolarmente. Nessun sistema è infallibile, per questo limitiamo la raccolta ai soli dati necessari descritti sopra.</p>",
        9 => "<p>Cerchiamo di conservare i vostri dati all'interno dell'Unione europea. OpenStreetMap, utilizzato per la visualizzazione delle mappe, è gestito da una fondazione con sede nel Regno Unito, paese riconosciuto dalla Commissione europea come garante di un livello adeguato di protezione dei dati personali.</p>",
        10 => "<p>Per qualsiasi domanda su questa informativa o sui vostri dati, scrivete a :email_link:. Data la dimensione dell'azienda e la natura dei trattamenti sopra descritti, la designazione di un responsabile della protezione dei dati (DPO) non è richiesta dalla normativa.</p>",
        11 => "<p>Questa informativa può essere aggiornata, in particolare per riflettere un'evoluzione del sito o della normativa. La data dell'ultimo aggiornamento è indicata in cima a questa pagina; vi invitiamo a consultarla periodicamente.</p>",
    ],
    'link_notice' => "note legali",
];
