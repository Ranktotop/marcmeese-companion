# Marc Meese Companion

WordPress-Plugin für marcmeese.de. Bündelt kleine technische Korrekturen, die Theme- und Plugin-Updates überstehen müssen.

## Funktionen

| Modul | Datei | Was es tut |
|---|---|---|
| Reparatur lokaler Google Fonts | `includes/class-font-repair.php` | Das Plugin „Local Google Fonts“ legt variable Schriften mit Leerzeichen im Dateinamen an (`…-400 700.woff2`). Der Browser kann sie dann nicht laden. Das Modul benennt die Dateien um und korrigiert die `font.css` – automatisch beim Ausliefern, täglich per Cron und bei der Aktivierung. |
| Caveat lokal | `includes/class-caveat.php` | Lädt die Handschrift Caveat aus dem Plugin auf der Startseite und den Seiten im neuen Design, auch dort, wo Divi sie nicht anfordert. Keine Verbindung zu Google. |

### Anpassbar per Filter

- `mmc/caveat_page_ids` – Seiten-IDs, auf denen Caveat geladen wird (Standard: 56828, 57207, 57369; die Startseite ist immer dabei).

## Neue Version veröffentlichen

1. Version an **zwei** Stellen in `marcmeese-companion.php` erhöhen: im Header (`Version:`) und bei `MMC_VERSION`.
2. Committen und auf `main` pushen.
3. Die GitHub Action baut die ZIP (inkl. Composer-Abhängigkeiten) und veröffentlicht das Release `v<Version>`.
4. WordPress zeigt das Update unter Plugins an (Prüfung alle 12 Stunden, sofort über „Nach Updates suchen“).

Updates kommen über den [Plugin Update Checker](https://github.com/YahnisElsts/plugin-update-checker) aus den GitHub-Releases.
