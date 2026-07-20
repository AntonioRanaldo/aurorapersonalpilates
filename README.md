# Aurora Personal Pilates — Sito web

Sito one-page per Aurora Personal Pilates (sedi di **Prato** e **Forte dei Marmi**).
HTML/CSS/JavaScript vanilla in un unico `index.html`, più un piccolo backend PHP per il form di contatto.

---

## Struttura del progetto

```
aurora-site/
├── index.html              ← il sito (HTML + CSS + JS, tutto qui)
├── send.php                ← backend del form di contatto (invio email)
├── config.example.php      ← template credenziali SMTP (da copiare in config.php)
├── config.php              ← credenziali reali — NON è nel repo, lo crei tu
├── lib/PHPMailer/          ← libreria per l'invio email via SMTP
├── img/                    ← immagini, loghi e video hero
├── .github/workflows/      ← workflow deploy FTP (opzionale, di esempio)
├── .gitignore
└── README.md
```

## Requisiti

- Per **vedere il sito**: un qualsiasi browser (basta aprire `index.html`).
- Per **il form**: hosting con **PHP 7.4+** e **OpenSSL** attivo — standard sui piani Aruba "Hosting Linux".

---

## Sviluppo con Claude Code + GitHub

1. Crea un repo su GitHub e carica questa cartella (`git init`, `git add .`, `git commit`, `git push`).
   > `config.php` è nel `.gitignore`: non verrà caricato (le credenziali restano private).
2. Apri la cartella in **Claude Code** per continuare a iterare: ogni modifica resta tracciata in Git.
3. Anteprima locale rapida (le pagine statiche si aprono da sole; per testare anche il PHP serve un server PHP):
   ```bash
   php -S localhost:8000
   ```
   poi visita `http://localhost:8000`. Il form funziona in locale solo se hai creato `config.php` con credenziali SMTP valide.

---

## Form di contatto — come farlo funzionare (Aruba)

Le richieste arrivano via email **direttamente alla casella di Aurora**, senza servizi terzi.
Per non finire in spam, l'email viene spedita da una **casella del dominio Aruba** (SPF corretto) e inoltrata all'indirizzo Gmail.

### Passi

1. Nel pannello Aruba, **crea una casella email** sul dominio (es. `info@iltuodominio.it`).
2. Copia `config.example.php` in **`config.php`** e compila:
   - `smtp_user` / `smtp_pass` → la casella Aruba appena creata e la sua password
   - `from_email` → **la stessa** casella Aruba
   - `to_email` → `aurorapersonalpilates@gmail.com` (dove vuoi ricevere le richieste)
3. Carica `config.php` sul server (via File Manager o FTP). **Solo questo file va messo a mano**, perché è segreto e non sta su GitHub.
4. Prova il form dal sito online: dovresti ricevere l'email. Se non arriva, controlla la cartella spam e i log del server.

> Parametri Aruba tipici: host `smtps.aruba.it`, porta `465`, sicurezza `ssl`.
> In alternativa `smtp.aruba.it`, porta `587`, sicurezza `tls`.

**Anti-spam già incluso:** campo honeypot nascosto + validazione lato client e lato server.

---

## Pubblicazione su Aruba

### Metodo semplice (manuale)
Carica **tutto il contenuto** della cartella nella directory pubblica del sito (via File Manager o FTP: FileZilla).
Riccorda di caricare anche `config.php` (creato al punto sopra). Puoi omettere `README.md`, `config.example.php`,
`istruzioni_vecchio_dev.txt` e la cartella `.github/` (non servono online).

### Metodo automatico (opzionale)
In `.github/workflows/deploy.yml.example` c'è un workflow che, a ogni `push`, carica il sito su Aruba via FTP.
Per attivarlo segui le istruzioni scritte dentro il file (rinominalo in `deploy.yml` e imposta i *secret* FTP su GitHub).

### Dominio e HTTPS
- Collega il dominio dal pannello Aruba e **attiva il certificato SSL** (per l'`https://`).
- Dopo aver scelto il dominio, fai un **find & replace** di `REPLACE_WITH_DOMAIN` in `index.html`
  con il dominio reale (compare nei meta `og:` e nello Schema.org per SEO e anteprime social).

---

## ⚠️ Da sistemare prima del go-live

- [ ] **Dominio**: sostituire `REPLACE_WITH_DOMAIN` in `index.html` (2 punti: meta OG e JSON-LD).
- [ ] **Form**: creare `config.php` con le credenziali SMTP Aruba e caricarlo sul server.
- [ ] **Social**: nel footer di `index.html` i link Instagram e Facebook sono segnaposto
      (cerca i commenti `TODO`). Inserire gli URL reali dei profili di Aurora. WhatsApp è già corretto.

## Da confermare con Aurora (contenuti)

- Sezione **"Come funziona"**: lo step "Prima sessione **conoscitiva**" suona come un incontro
  introduttivo gratuito, ma da indicazioni la consulenza gratuita non esiste. Da chiarire il testo.
  (Inoltre gli step I e II hanno nomi quasi identici: "Prima sessione conoscitiva" / "Prima sessione".)
- Sezione **"Chi sono"**: le foto usate ritraggono Aurora in abito elegante (shooting editoriale).
  Confermare che siano quelle desiderate.

---

## Modifiche applicate in questa revisione

- **Form di contatto reso funzionante**: nuovo `send.php` (PHPMailer + SMTP Aruba), invio via `fetch`
  con validazione, messaggi di successo/errore e honeypot anti-spam. (Prima il form era finto.)
- **Bug colore hero corretto**: titolo e testo dell'hero erano impostati su colore scuro sopra lo
  sfondo scuro (regola CSS duplicata) → riportati a bianco/linen, ora leggibili.
- **Video hero ottimizzato**: convertito da `hero_video.mov` (8.8 MB, non compatibile in autoplay su
  Chrome/Android) a **`hero_video.mp4`** H.264 (~0.56 MB). Aggiornato il tag `<source>`.
- **Galleria Forte dei Marmi ripristinata a 3 foto** (`fdm_studio2`, `fdm_studio1`, `fdm_entrance`),
  come da design originale, sfruttando le foto ora disponibili.
- **Icone social**: sostituiti i simboli generici con vere icone SVG (Instagram, Facebook, WhatsApp).
- **SEO**: Schema.org arricchito (telefono, email, immagine, CAP, tipo attività) e `og:image`/`og:url`
  resi assoluti (con segnaposto dominio); aggiunto `og:locale`.
- **Cartella `img/`** completata e alleggerita: rimosso il `.mov` pesante; totale ~3.2 MB.
# aurorapersonalpilates
