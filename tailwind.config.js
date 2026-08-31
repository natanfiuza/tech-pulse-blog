/** @type {import('tailwindcss').Config} */
export default {
    darkMode: "class",
    content: [
        "./resources/**/*.blade.php",
        "./resources/js/**/*.{vue,js}",
    ],
    theme: {
        extend: {
            colors: {
                "primary-container": "rgba(43, 82, 238, 0.1)",
                "on-background": "#f6f6f8",
                "on-primary": "#ffffff",
                "on-primary-fixed": "#001258",
                "secondary": "#6272b4",
                "inverse-surface": "#f6f6f8",
                "primary": "#2b52ee",
                "surface-container-lowest": "#000821",
                "surface-container": "#001b5e",
                "on-tertiary-fixed-variant": "#812800",
                "tertiary-fixed": "#ffdbcf",
                "surface-container-low": "#001247",
                "error": "#ff5f56",
                "on-error": "#ffffff",
                "on-surface-variant": "#6272b4",
                "background": "#001247",
                "surface-bright": "#1a2a6c",
                "inverse-on-surface": "#001247",
                "secondary-fixed-dim": "#b9c3ff",
                "on-tertiary-fixed": "#380d00",
                "error-container": "#450a07",
                "outline": "#6272b4",
                "on-secondary-container": "#f6f6f8",
                "tertiary-fixed-dim": "#ffb59b",
                "primary-fixed": "#dee1ff",
                "inverse-primary": "#5c7cff",
                "on-tertiary": "#001247",
                "secondary-container": "rgba(98, 114, 180, 0.2)",
                "outline-variant": "rgba(98, 114, 180, 0.2)",
                "secondary-fixed": "#dee1ff",
                "surface-dim": "#000b2b",
                "surface": "#001247",
                "on-secondary-fixed-variant": "#344284",
                "on-surface": "#f6f6f8",
                "surface-container-high": "#0b253a",
                "primary-fixed-dim": "#b9c3ff",
                "surface-container-highest": "#1e3a5f",
                "on-error-container": "#ffb4ab",
                "on-secondary-fixed": "#001258",
                "surface-tint": "#2b52ee",
                "tertiary-container": "#011627",
                "on-tertiary-container": "#82aaff",
                "surface-variant": "#0b253a",
                "on-primary-container": "#2b52ee",
                "on-primary-fixed-variant": "#0033c2",
                "on-secondary": "#ffffff",
                "tertiary": "#82aaff"
            },
            borderRadius: {
                DEFAULT: "0.25rem",
                lg: "0.5rem",
                xl: "0.75rem",
                full: "9999px"
            },
            fontFamily: {
                headline: ["Inter", "sans-serif"],
                display: ["Inter", "sans-serif"],
                body: ["Inter", "sans-serif"],
                label: ["Inter", "sans-serif"],
                mono: ["JetBrains Mono", "monospace"]
            }
        }
    },
}
