<?php
/**
 * TEMPLATE di configurazione.
 *
 * 1) Copia questo file e rinominalo in "config.php"
 * 2) Inserisci i dati reali della casella email creata sul dominio Aruba
 * 3) NON caricare config.php su GitHub (è già nel .gitignore)
 *
 * IMPORTANTE: la casella mittente deve essere una email del TUO dominio Aruba
 * (es. info@aurorapersonalpilates.it), NON una Gmail. Le richieste possono poi
 * essere INOLTRATE a qualsiasi indirizzo (anche Gmail) tramite 'to_email'.
 */

return [
    // --- SMTP Aruba ---
    'smtp_host'   => 'smtps.aruba.it',
    'smtp_port'   => 465,
    'smtp_secure' => 'ssl',                       // 'ssl' (porta 465) oppure 'tls' (porta 587)

    'smtp_user'   => 'info@iltuodominio.it',      // la casella Aruba COMPLETA
    'smtp_pass'   => 'LA_TUA_PASSWORD',           // password della casella

    // --- Mittente mostrato (deve coincidere con la casella Aruba) ---
    'from_email'  => 'info@iltuodominio.it',
    'from_name'   => 'Sito Aurora Personal Pilates',

    // --- Destinatario: dove arrivano le richieste ---
    'to_email'    => 'aurorapersonalpilates@gmail.com',
    'to_name'     => 'Aurora',
];
