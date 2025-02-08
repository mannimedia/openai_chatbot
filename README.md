# TYPO3 OpenAI Chatbot Extension

## Übersicht

Die OpenAI Chatbot Extension integriert einen KI-gestützten Chatbot in TYPO3-Websites. Die Extension nutzt die OpenAI API und unterstützt persistente Konversationen durch Session-Management.

![TYPO3 Chat Bot](Documentation/Images/chatbot-preview.png)

## Features

- 🤖 Interaktiver KI-Chatbot basierend auf OpenAI's GPT-Modellen
- 💬 Session-basierte Gesprächsverläufe
- 📱 Vollständig responsives Design
- ⚙️ Umfangreiche Konfigurationsmöglichkeiten
- 🔒 Sichere API-Kommunikation
- 📝 Markdown & HTML Formatierung
- 🎨 Anpassbares Styling

## Systemanforderungen

- TYPO3 11.5 LTS oder höher
- PHP 8.1 oder höher
- OpenAI API Key
- Composer

## Installation

### Via Composer

```bash
composer require mannimedia/openai-chatbot
```

### Manuell

1. Extension aus dem TYPO3 Extension Repository herunterladen
2. Extension im Extension Manager aktivieren
3. Include Static TypoScript Template
4. TYPO3 und PHP Cache leeren

## Konfiguration

### TypoScript Setup

```typoscript
plugin.tx_openaichatbot {
    settings {
        apiKey = {$plugin.tx_openaichatbot.settings.apiKey}
        model = gpt-4-turbo-preview
        temperature = 0.7
        maxTokens = 1000
    }
}
```

### Environment-Variablen

Erstellen Sie eine `.env` Datei im Root-Verzeichnis:

```env
OPENAI_API_KEY=your-api-key-here
```

## Integration

### Als Content Element

1. Neues Content Element erstellen
2. "Plugins" Tab wählen
3. "OpenAI Chatbot" auswählen
4. Konfigurationsoptionen nach Bedarf anpassen

### Via TypoScript

```typoscript
page.10 = FLUIDTEMPLATE
page.10 {
    file = EXT:your_sitepackage/Resources/Private/Templates/Page/Default.html
    variables {
        chatbot = USER
        chatbot {
            userFunc = TYPO3\CMS\Extbase\Core\Bootstrap->run
            extensionName = OpenaiChatbot
            pluginName = Chat
            vendorName = Mannimedia
        }
    }
}
```

## Styling

Das Styling kann über SCSS/CSS angepasst werden:

```scss
.tx-openai-chatbot {
    // Ihre individuellen Styling-Anpassungen
}
```

## API Referenz

### Verfügbare Endpoints


### Request Format

```json
{
    "tx_openaichatbot_chat": {
        "message": "Benutzernachricht",
        "threadId": "optional-thread-id"
    }
}
```

### Response Format

```json
{
    "success": true,
    "response": {
        "message": "AI Antwort",
        "threadId": "generierte-thread-id"
    }
}
```

## Entwicklung

### Build Process

```bash
# Install dependencies
composer install

# Build assets
npm install
npm run build

# Run tests
composer test
```

### Coding Standards

Die Extension folgt den TYPO3 Coding Guidelines. Überprüfen Sie Ihren Code mit:

```bash
composer check-style
composer fix-style
```

## Fehlerbehebung

### Bekannte Probleme

1. API-Verbindungsfehler
    - API-Key überprüfen
    - Netzwerkverbindung testen
    - Firewall-Einstellungen prüfen

2. Session-Probleme
    - Cache leeren
    - Session-Storage überprüfen
    - PHP-Session-Einstellungen verifizieren

## Support

- 📫 [GitHub Issues](https://github.com/mannimedia/openai-chatbot/issues)
- 💬 [TYPO3 Slack Channel](#)
- 📚 [Ausführliche Dokumentation](https://docs.typo3.org/p/mannimedia/openai-chatbot/main/en-us/)

## Beitragen

Beiträge sind willkommen! Bitte lesen Sie unsere [Contribution Guidelines](CONTRIBUTING.md).

1. Fork das Projekt
2. Erstellen Sie Ihren Feature Branch (`git checkout -b feature/AmazingFeature`)
3. Commit Ihre Änderungen (`git commit -m 'Add some AmazingFeature'`)
4. Push zum Branch (`git push origin feature/AmazingFeature`)
5. Öffnen Sie einen Pull Request

## Lizenz

Copyright © 2024 [Ihr Name/Firma]

Dieses Projekt ist unter der MIT-Lizenz lizenziert - siehe die [LICENSE.md](LICENSE.md) Datei für Details.

## Credits

- Entwickelt von [Ihr Name/Firma]
- Powered by [OpenAI](https://openai.com)
- Built for [TYPO3 CMS](https://typo3.org)

---

Made with ❤️ for TYPO3
