import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import vue from "@vitejs/plugin-vue";

export default defineConfig({
    plugins: [
        laravel({
            input: "resources/js/app.js",
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
    ],
    server: {
        hmr: {
            host: process.env.CODESPACE_NAME
                ? process.env.CODESPACE_NAME +
                  "-5173." +
                  process.env.GITHUB_CODESPACES_PORT_FORWARDING_DOMAIN
                : null,
            clientPort: process.env.CODESPACE_NAME ? 443 : null,
            protocol: process.env.CODESPACE_NAME ? "wss" : null,
        },
    },
});
