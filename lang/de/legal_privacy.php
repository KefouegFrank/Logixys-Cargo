<?php

return [
    'sections' => [
        1 => "Verantwortlicher",
        2 => "Welche Daten wir erheben",
        3 => "Warum wir sie nutzen",
        4 => "Wer Ihre Daten erhält",
        5 => "Wie lange wir sie aufbewahren",
        6 => "Cookies",
        7 => "Ihre Rechte",
        8 => "Sicherheit",
        9 => "Übermittlungen außerhalb der Europäischen Union",
        10 => "Kontakt und Beschwerden",
        11 => "Änderungen dieser Erklärung",
    ],
    'intro' => "Diese Erklärung beschreibt, welche Daten :name: erhebt, wenn Sie diese Website besuchen, uns kontaktieren oder eine Sendung verfolgen, warum, wie lange, und wie Sie Ihre Rechte ausüben können. Sie gilt für die öffentliche Website – nicht für den dem autorisierten Personal vorbehaltenen Verwaltungsbereich.",
    'table' => [
        'headers' => ["Daten", "Zweck", "Rechtsgrundlage"],
        'rows' => [
            ["Kontaktformular", "Ihre Anfrage beantworten", "Berechtigtes Interesse an der Bearbeitung eingehender Anfragen"],
            ["Sendungsdaten", "Transport organisieren, durchführen und abrechnen", "Erfüllung des Transportvertrags"],
            ["Rechnungen und Buchhaltungsunterlagen", "Buchführung", "Rechtliche Verpflichtung"],
            ["IP-Adresse (Kontaktformular)", "Missbrauch verhindern (Spam, automatisierte Übermittlungen)", "Berechtigtes Interesse an der Absicherung des Dienstes"],
        ],
    ],
    'articles' => [
        1 => "<p>Verantwortlicher für die unten beschriebenen Daten ist :name:, dessen vollständige Angaben im :notice_link: zu finden sind. Bei Fragen zu Ihren Daten schreiben Sie an :email_link:.</p>",
        2 => "<p>Wir erheben ausschließlich die für die folgenden Funktionen erforderlichen Daten.</p>
              <ul>
                <li><strong>Kontaktformular.</strong> Name, E-Mail, Telefon (optional), Betreff und Nachricht, sowie IP-Adresse und verwendeter Browser beim Absenden — nützlich, um einen Missbrauch des Formulars zu erkennen.</li>
                <li><strong>Sendungen.</strong> Wird eine Sendung für Sie angelegt, speichern wir Name, E-Mail, Telefon und Anschrift (Straße, Stadt, Land) von Absender und Empfänger sowie Inhalt und Route der Sendung.</li>
                <li><strong>Sendungsverfolgung.</strong> Die Sendungsverfolgungsseite zeigt den vollständigen Sendungsdatensatz jedem an, der die zugehörige Sendungsnummer eingibt — diese Nummer dient als Zugangsberechtigung. Geben Sie sie nur an Personen weiter, die an der Sendung beteiligt sind.</li>
                <li><strong>Technische Daten.</strong> Ein für den Betrieb der Website unbedingt erforderliches Sitzungs-Cookie (siehe §6). Wir verwenden weder Werbe-Cookies noch Analysetools.</li>
              </ul>",
        3 => "<p>Wir verarbeiten jede Datenkategorie aus einem konkreten Grund, niemals für einen Zweck, den sie nicht rechtfertigt:</p>",
        4 => "<p>Ihre Daten sind für autorisiertes Personal von :name: zugänglich und werden weitergegeben an:</p>
              <ul>
                <li><strong>Resend</strong> (E-Mail-Versanddienst) — um Ihnen die geschuldeten Bestätigungs- und Benachrichtigungs-E-Mails zuzustellen.</li>
                <li><strong>OpenStreetMap</strong> — sobald eine Karte angezeigt wird (Kontaktseite, Sendungsverfolgung), lädt Ihr Browser die Kartenbilder direkt von den Servern von OpenStreetMap, wodurch diesen Ihre IP-Adresse offengelegt wird. Es werden keine weiteren Daten mit diesem Dienst geteilt.</li>
                <li><strong>Geoapify / LocationIQ</strong> — Adresssuchwerkzeuge, die unser Team intern bei der Anlage einer Sendung nutzt, um eine Adresse in eine Position auf der Karte umzuwandeln.</li>
              </ul>
              <p>Wir verkaufen oder vermieten Ihre Daten nicht an Dritte und nutzen sie nicht zu Werbezwecken.</p>",
        5 => "<ul>
                <li><strong>Nachrichten aus dem Kontaktformular:</strong> bis zu 24 Monate ab dem letzten Austausch, danach gelöscht.</li>
                <li><strong>Sendungs- und Rechnungsdaten:</strong> für die gesetzliche Aufbewahrungsfrist von Buchhaltungsunterlagen, in Frankreich 10 Jahre.</li>
                <li><strong>Sitzungs-Cookie:</strong> wird beim Schließen des Browsers oder nach :hours: Stunde(n) Inaktivität gelöscht.</li>
              </ul>",
        6 => "<p>Diese Website setzt ein einziges, für ihren Betrieb unbedingt erforderliches Cookie: ein Sitzungs-Cookie, das Sie seitenübergreifend angemeldet hält und Formulare vor gefälschten Übermittlungen schützt (CSRF-Token). Es dient weder der Werbeverfolgung noch der Reichweitenmessung und erfordert daher keine Einwilligung.</p>
              <p>Wir verwenden kein Analysetool (Google Analytics oder Ähnliches) sowie keine Werbe- oder Social-Media-Cookies. Sollte sich dies ändern, würde vor dem Setzen eines nicht unbedingt erforderlichen Cookies ein konformer Einwilligungsbanner eingerichtet.</p>",
        7 => "<p>Gemäß der Datenschutz-Grundverordnung (DSGVO) haben Sie folgende Rechte in Bezug auf Ihre Daten:</p>
              <ul>
                <li><strong>Auskunft:</strong> eine Kopie der über Sie gespeicherten Daten erhalten.</li>
                <li><strong>Berichtigung:</strong> unrichtige oder unvollständige Daten korrigieren lassen.</li>
                <li><strong>Löschung:</strong> die Löschung Ihrer Daten verlangen, im Rahmen unserer gesetzlichen Aufbewahrungspflichten.</li>
                <li><strong>Einschränkung:</strong> die vorübergehende Aussetzung einer Verarbeitung verlangen.</li>
                <li><strong>Übertragbarkeit:</strong> Ihre Daten in einem strukturierten, wiederverwendbaren Format erhalten.</li>
                <li><strong>Widerspruch:</strong> einer auf unserem berechtigten Interesse beruhenden Verarbeitung widersprechen.</li>
              </ul>
              <p>Um eines dieser Rechte auszuüben, schreiben Sie an :email_link:. Wir antworten innerhalb eines Monats. Wenn Sie der Ansicht sind, dass Ihre Rechte nicht gewahrt werden, können Sie sich mit einer Beschwerde an die :cnil_link: (die französische Datenschutzbehörde) wenden.</p>",
        8 => "<p>Die Kommunikation mit dieser Website ist verschlüsselt (HTTPS). Der Zugang zum Verwaltungsbereich ist autorisiertem Personal vorbehalten, passwortgeschützt, und jedes Konto kann einzeln deaktiviert werden. Da kein System unfehlbar ist, beschränken wir die Erhebung auf die oben beschriebenen, notwendigen Daten.</p>",
        9 => "<p>Wir bemühen uns, Ihre Daten innerhalb der Europäischen Union zu speichern. OpenStreetMap, das für die Kartenanzeige verwendet wird, wird von einer im Vereinigten Königreich ansässigen Stiftung betrieben — einem Land, das von der Europäischen Kommission als Land mit angemessenem Datenschutzniveau anerkannt ist.</p>",
        10 => "<p>Bei Fragen zu dieser Erklärung oder zu Ihren Daten schreiben Sie an :email_link:. Angesichts der Unternehmensgröße und der Art der oben beschriebenen Verarbeitungen ist die Benennung eines Datenschutzbeauftragten (DSB) gesetzlich nicht vorgeschrieben.</p>",
        11 => "<p>Diese Erklärung kann aktualisiert werden, insbesondere um Änderungen der Website oder der Rechtslage widerzuspiegeln. Das Datum der letzten Aktualisierung finden Sie oben auf dieser Seite; wir empfehlen, sie regelmäßig zu prüfen.</p>",
    ],
    'link_notice' => "Impressum",
];
