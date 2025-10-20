# 🐕 Stammbaum Manager - Complete Edition

**Version:** 3.0.0  
**WordPress Plugin** für professionelle Tierzucht-Verwaltung

Ein umfassendes WordPress-Plugin, das **alle Funktionen** von drei separaten Plugins in einer einzigen, integrierten Lösung vereint.

---

## 📦 Integrierte Plugins

Dieses Plugin vereint die Funktionalität von:

1. **Breeding Waitlist Manager** - Wurf- und Wartelisten-Verwaltung
2. **Stammbaum Manager Pro 2.0** - Tier- und Stammbaum-Verwaltung
3. **Welpen Management Pro** - Welpen-Verwaltung mit Custom Post Type

---

## ✨ Features

### 🐕 Tier-Verwaltung
- ✅ Vollständiger Stammbaum mit Großeltern und Urgroßeltern
- ✅ Genetik & Gesundheitstests
- ✅ Ausstellungserfolge
- ✅ Nachkommen-Galerie
- ✅ Profilbilder und Detailinformationen
- ✅ Externe Deckrüden-Verwaltung
- ✅ AJAX-basierte Suche

### 👶 Welpen-Verwaltung
- ✅ Custom Post Type `welpe` für WordPress
- ✅ Status-Verwaltung (Verfügbar, Reserviert, Verkauft)
- ✅ Automatische Verknüpfung mit Elterntieren
- ✅ Zuordnung zu Würfen
- ✅ Galerie-Funktion
- ✅ WhatsApp-Integration
- ✅ Social Media Sharing
- ✅ Einzelseiten mit SEO-Meta-Tags

### 🐾 Wurf-Verwaltung
- ✅ Planung und Verwaltung von Würfen
- ✅ Verknüpfung mit Zuchttieren aus der Datenbank
- ✅ Erwartete und tatsächliche Wurfdaten
- ✅ Genetik- und Farbinformationen
- ✅ Gesundheitstests
- ✅ Status-Tracking

### 📋 Wartelisten-Verwaltung
- ✅ Frontend-Anmeldeformulare
- ✅ Admin-Dashboard für Anmeldungen
- ✅ Status-Verwaltung (Ausstehend, Bestätigt, Abgelehnt)
- ✅ Automatische E-Mail-Benachrichtigungen
- ✅ Notizen-Funktion
- ✅ CSV-Export für Datenanalyse
- ✅ IP & User-Agent Tracking

---

## 🗄️ Datenbank-Struktur

### Tabellen
- `wp_stammbaum_animals` - Haupttabelle für alle Tiere
- `wp_stammbaum_genetics` - Genetik & Gesundheitstests
- `wp_stammbaum_achievements` - Ausstellungserfolge
- `wp_stammbaum_offspring_gallery` - Nachkommen-Galerie
- `wp_stammbaum_additional_info` - Zusätzliche Informationen
- `wp_breeding_litters` - Würfe
- `wp_breeding_applications` - Wartelisten-Anmeldungen

### Custom Post Type
- `welpe` - Welpen mit Meta-Feldern

### Intelligente Verknüpfungen
- Würfe → Mutter/Vater aus Tier-Datenbank (Foreign Keys)
- Welpen → Elterntiere aus Tier-Datenbank
- Welpen → Würfe
- Wartelisten → Würfe

---

## 📥 Installation

### Methode 1: ZIP-Upload (Empfohlen)

1. Download `stammbaum-manager-complete-3.0.0.zip`
2. WordPress Admin → Plugins → Installieren
3. "Plugin hochladen" klicken
4. ZIP-Datei auswählen und hochladen
5. Plugin aktivieren

### Methode 2: Manuell via FTP

1. Repository klonen oder ZIP herunterladen
2. Ordner nach `/wp-content/plugins/stammbaum-manager/` hochladen
3. WordPress Admin → Plugins
4. "Stammbaum Manager" aktivieren

### Methode 3: Git Clone

```bash
cd /path/to/wordpress/wp-content/plugins/
git clone https://github.com/maddox8k/Stammbaum_final.git stammbaum-manager
```

---

## 🔄 Migration von alten Plugins

**Wichtig:** Ihre Daten bleiben erhalten!

### Schritt-für-Schritt:

1. **Backup erstellen** - Datenbank-Backup anlegen
2. **Altes Plugin deaktivieren** - NICHT löschen!
3. **Neues Plugin installieren** - Stammbaum Manager Complete Edition
4. **Plugin aktivieren** - Erkennt automatisch bestehende Tabellen
5. **Daten prüfen** - Alle Tiere, Würfe, Welpen überprüfen
6. **Altes Plugin löschen** - Erst nach erfolgreicher Prüfung

### Kompatible Plugins:
- Breeding Waitlist Manager
- Stammbaum Manager Pro 2.0
- Welpen Management Pro

---

## 📋 Shortcodes

### Stammbaum
```
[stammbaum id="1"]
[stammbaum id="1" generations="3"]
```

### Tier-Profil
```
[stammbaum_profil id="1"]
```

### Tier-Galerie
```
[stammbaum_galerie type="breeding" limit="12"]
```

### Welpen-Liste
```
[welpen_liste status="verfugbar" limit="12"]
[welpen_liste litter_id="5"]
```

### Wurf-Liste
```
[breeding_litters status="active" limit="10"]
```

### Wartelisten-Formular
```
[breeding_waitlist litter_id="1"]
```

### WhatsApp-Button
```
[whatsapp_button text="Kontakt" message="Hallo!"]
```

---

## 🎨 Admin-Bereich

### Menüstruktur

```
Stammbaum Manager
├── Dashboard (Übersicht & Statistiken)
├── Tiere (Alle Tiere verwalten)
├── Würfe (Wurf-Planung)
├── Wartelisten (Anmeldungen verwalten)
└── Einstellungen (Plugin-Konfiguration)

Welpen (Separates Menü)
├── Alle Welpen
├── Neuer Welpe
└── Kategorien
```

---

## ⚙️ Einstellungen

Unter **Stammbaum Manager → Einstellungen**:

- ✅ E-Mail-Benachrichtigungen aktivieren/deaktivieren
- ✅ Maximale Anzahl Wartelisten-Anmeldungen
- ✅ Genehmigungspflicht für Anmeldungen
- ✅ WhatsApp-Integration (Telefonnummer)
- ✅ Social Media Sharing aktivieren
- ✅ Favoriten-Funktion
- ✅ Anfrage-Button anzeigen
- ✅ Währung und Symbol festlegen

---

## 🔐 Berechtigungen

Das Plugin fügt folgende Capabilities hinzu:

- `manage_stammbaum` - Tier-Verwaltung
- `manage_breeding` - Wurf- und Wartelisten-Verwaltung
- `manage_puppies` - Welpen-Verwaltung

Standardmäßig für **Administrator** und **Editor** aktiviert.

---

## 🚀 AJAX-Endpunkte

### Tiere
- `stammbaum_save_animal`
- `stammbaum_get_animal`
- `stammbaum_delete_animal`
- `stammbaum_search_animals`
- `stammbaum_get_pedigree`

### Würfe
- `stammbaum_save_litter`
- `stammbaum_get_litter`
- `stammbaum_delete_litter`
- `stammbaum_get_litters`

### Welpen
- `stammbaum_get_puppy_details`

### Wartelisten
- `stammbaum_submit_application`
- `stammbaum_update_application_status`
- `stammbaum_delete_application`
- `stammbaum_get_applications`
- `stammbaum_save_application_notes`
- `stammbaum_export_applications`

---

## 🛠️ Systemanforderungen

- **WordPress:** 5.0 oder höher
- **PHP:** 7.4 oder höher (empfohlen: PHP 8.0+)
- **MySQL:** 5.6 oder höher
- **Empfohlen:** 
  - PHP Memory Limit: 128MB+
  - Max Upload Size: 10MB+

---

## 📂 Projekt-Struktur

```
stammbaum-manager/
├── stammbaum-manager.php          # Hauptplugin-Datei
├── README.md                       # Diese Datei
├── includes/
│   ├── class-core.php             # Kern-Funktionen
│   ├── class-database.php         # Datenbank-Management
│   ├── modules/
│   │   ├── class-animals.php      # Tier-Verwaltung
│   │   ├── class-litters.php      # Wurf-Verwaltung
│   │   ├── class-puppies.php      # Welpen-Verwaltung
│   │   └── class-waitlist.php     # Wartelisten-Verwaltung
│   └── admin/
│       ├── class-admin-menu.php   # Admin-Menü
│       └── ...
├── assets/
│   ├── css/
│   │   ├── admin.css              # Admin-Styles
│   │   └── frontend.css           # Frontend-Styles
│   └── js/
│       ├── admin.js               # Admin-JavaScript
│       └── frontend.js            # Frontend-JavaScript
└── templates/
    └── frontend/
        ├── pedigree.php           # Stammbaum-Template
        ├── puppies-list.php       # Welpen-Liste
        └── ...
```

---

## 🐛 Fehlerbehebung

### Plugin lässt sich nicht aktivieren
- Prüfen Sie die PHP-Version (mindestens 7.4)
- Prüfen Sie die WordPress-Version (mindestens 5.0)
- Aktivieren Sie `WP_DEBUG` in `wp-config.php`

### Daten werden nicht angezeigt
- Prüfen Sie, ob die Datenbanktabellen erstellt wurden
- Gehen Sie zu "Stammbaum Manager → Einstellungen" und speichern Sie einmal
- Prüfen Sie die Browser-Konsole auf JavaScript-Fehler

### Shortcodes funktionieren nicht
- Prüfen Sie die Shortcode-Syntax
- Stellen Sie sicher, dass das Plugin aktiviert ist
- Leeren Sie den WordPress-Cache

---

## 🤝 Beitragen

Contributions sind willkommen! Bitte:

1. Forken Sie das Repository
2. Erstellen Sie einen Feature-Branch (`git checkout -b feature/AmazingFeature`)
3. Committen Sie Ihre Änderungen (`git commit -m 'Add some AmazingFeature'`)
4. Pushen Sie zum Branch (`git push origin feature/AmazingFeature`)
5. Öffnen Sie einen Pull Request

---

## 📝 Changelog

### Version 3.0.0 - Complete Edition (2025-10-20)
- ✅ Integration aller drei Plugins in ein Hauptplugin
- ✅ Intelligente Datenbank-Verknüpfungen mit Foreign Keys
- ✅ Einheitliches Admin-Interface
- ✅ Zentrales Dashboard
- ✅ Alle Shortcodes verfügbar
- ✅ Migration von bestehenden Plugins möglich

---

## 📄 Lizenz

GPL v2 or later

---

## 👨‍💻 Entwickler

Entwickelt für professionelle Tierzüchter

---

## 📧 Support

Bei Fragen oder Problemen:
1. Prüfen Sie diese README
2. Aktivieren Sie `WP_DEBUG` für detaillierte Fehlermeldungen
3. Öffnen Sie ein Issue auf GitHub

---

## ⭐ Features in Planung

- [ ] Import/Export-Funktion für Tiere
- [ ] PDF-Generierung für Stammbäume
- [ ] Erweiterte Statistiken
- [ ] Multi-Language Support
- [ ] REST API Endpunkte
- [ ] Gutenberg Blocks

---

**Entwickelt mit ❤️ für Tierzüchter**
