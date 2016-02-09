# TeamSpeak3-stats

Statistiche per TeamSpeak-3!

# Installazione
- Scaricare il repository
- Installare le dipendenze necessarie
    - `php` per le analisi e le pagine web
    - `bower` per la gesione delle dipendenze
    - Avviare `bower install` per scaricare le dipendenze
- Configurare l'applicazione
    - Copiare `config/example.config.php` in `config/config.php`
    - Modificare i parametri necessari nel file `config/config.php`
- Creare il database as esempio importando `db/ts3stats.sql` in MySQL
- Assicurarsi che il file di log specificato sia scrivibile da php e i file di log di ts siano leggibili
- Avviare l'analisi completa almeno 2 volte per stabilizzare gli analizzatori
    - `php run_analysis.php --debug --passcode [passcode]`

# Licenza
Progetto rilasciato sotto licenza GPLv2

# Progetto realizzato da:

- Edoardo Morassutto <edoardo.morassutto@gmail.com>
- Marco Civettini <marco.cive.civettini@gmail.com>
- Fabio Luccon <fabio.luccon@hotmail.it>
