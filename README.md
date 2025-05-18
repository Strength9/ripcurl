# RipCurl – WordPress Block Theme

RipCurl is a modern block-based WordPress theme built with SCSS and full Site Editor support.

---

## 📦 Requirements

- Node.js (v16+ recommended)
- npm
- Sass (installed as a dev dependency via npm)

---

## 📁 Folder Structure

```
ripcurl/
├── css/                   # Output directory (optional, unused in final setup)
├── scss/                  # Source SCSS files
│   ├── style.scss         # Main front-end styles (compiled to style.css)
│   ├── editor.scss        # Block editor styles (compiled to editor-style.css)
│   ├── _variables.scss    # Colour, font and layout variables
│   ├── _layout.scss       # Layout-specific rules
│   └── _components.scss   # Buttons, typography etc.
├── style.css              # Compiled CSS + WordPress metadata block (must be in root)
├── editor-style.css       # Editor stylesheet (auto-loaded by WP)
├── functions.php          # Enqueues styles, theme support
├── theme.json             # Theme config for block editor
├── templates/             # HTML block templates
└── templates/parts/       # Header, footer, sidebar etc.
```

---

## 🛠 Development Commands

Run these in the theme root folder (where `package.json` lives):

### 📥 Install Dependencies

```bash
npm install
```

### 🔨 One-Time Build

```bash
npm run build
```

This compiles:

- `scss/style.scss → style.css`
- `scss/editor.scss → editor-style.css`

### 🔁 Live Watch (recommended while developing)

```bash
npm run watch
```

This will watch both SCSS files and recompile on save.

---

## ✏️ Theme Setup Notes

- `style.css` **must** remain in the root of the theme for WordPress to recognise it.
- The `editor-style.css` is automatically enqueued via `functions.php`.
- Custom templates (like `home.html`, `page.html`, `404.html`) live in `templates/`.

---

## ✅ Future Ideas

- CLI scaffold (`npx create-ripcurl`)
- WooCommerce template overrides
- Global pattern library
- Block styles and variations

---

### 👨‍💻 Made by Dave Pratt – Web Developer
