# Midnight Pulse Design System

### 1. Overview & Creative North Star
**Creative North Star: "The Synthetic Nocturne"**
Midnight Pulse is a high-end technical editorial system designed for the modern developer. It moves away from the "utility-first" clutter by embracing deep, oceanic navy tones and vibrant electric blue accents. The system leverages high-contrast typography and syntax-inspired highlights to create a focused, immersive learning environment. It breaks the traditional grid through the use of asymmetrical card layouts and oversized hero typography that bleeds into the background space.

### 2. Colors
The palette is built on a foundation of "Midnight Deep" (`#000b2b`) and "Background Dark" (`#001247`). 
- **The "No-Line" Rule:** Sectioning is achieved through tonal shifts (e.g., transitioning from `surface` to `surface_container_low`) or 1px borders using `outline_variant` at 20% opacity. Never use solid, high-contrast borders for structural layout.
- **Surface Hierarchy & Nesting:** Use `surface_container_high` (#0b253a) specifically for interactive code environments or inset sections. Content cards should reside on `surface_container_low` with a subtle hover transition to a higher tonal state.
- **The "Glass & Gradient" Rule:** Navigation and persistent headers must use the `glass-header` class: a backdrop blur of 12px combined with a semi-transparent `background-dark` (alpha 0.8).
- **Signature Textures:** Hero elements should utilize a subtle glow effect (box-shadow with primary color at low opacity) rather than heavy textures.

### 3. Typography
Midnight Pulse utilizes a strictly curated scale based on **Inter** for all UI elements and **JetBrains Mono** for technical data.

**Typography Scale:**
- **Display/Hero:** 3.75rem (60px) / 4rem (64px) - Font-weight 900 (Black). Used for primary hooks.
- **Headline:** 2.25rem (36px) - Font-weight 800. Used for section titles.
- **Title/Card:** 1.25rem (20px) or 1.5rem (24px).
- **Body:** 1.125rem (18px) for editorial content; 0.875rem (14px) for standard interface text.
- **Label/Small:** 0.75rem (12px) or 10px (for "Pro" tags and metadata).

The typographic rhythm relies on extreme weight variance—pairing Ultra-Black headlines with Medium-weight body text to create an authoritative editorial feel.

### 4. Elevation & Depth
Depth is not communicated through "physical" shadows but through light emission and stacking.

- **The Layering Principle:** Depth is established by placing cards (`midnight-deep/30`) on top of the base (`background-dark`). 
- **Ambient Shadows:** Use `shadow-2xl` for floating elements (like code blocks). For buttons, use "Glow Elevation": `0 0 20px rgba(43, 82, 238, 0.4)` upon hover to simulate light emission.
- **Glassmorphism:** All overlays must include `backdrop-filter: blur(12px)` to maintain the sense of a singular, deep spatial environment.

### 5. Components
- **Buttons:** Primary buttons are solid `primary` (#2b52ee) with rounded-lg corners. They should not have borders but may use a glow effect on hover.
- **Cards:** Editorial cards feature an `aspect-video` image container. The card body uses a subtle 5% `periwinkle` border that intensifies to 30% `primary` on hover.
- **Tags/Chips:** Small, high-contrast tags (e.g., 10px font size) with 20% opacity backgrounds. Use `emerald-500` for News and `primary` for technical levels.
- **Input Fields:** Styled as "Midnight Insets"—`midnight-deep` background with `periwinkle/30` borders. Focus states should transition the border to `primary`.
- **Code Blocks:** Use the `#011627` (Night Owl-inspired) background with a custom syntax highlighting palette: `#c792ea` (Keywords), `#82aaff` (Functions), and `#c3e88d` (Strings).

### 6. Do's and Don'ts
**Do:**
- Use `text-periwinkle` at 60-70% opacity for secondary body text to maintain hierarchy.
- Use wide spacing (`spacing: 3`) to allow technical content to breathe.
- Apply `animate-pulse` to small status indicators to add life to the static screen.

**Don't:**
- Use pure black (#000000) or pure white (#FFFFFF) for backgrounds or text; use the system's themed neutrals to keep the "Midnight" vibe.
- Use standard Material Design shadows; stick to tonal shifts and blurs.
- Mix serif fonts into this system; it is strictly neo-grotesque and monospaced.