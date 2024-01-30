import { defineConfig, squooshImageService } from "astro/config";
import { SITE } from "./src/config.mts";
import partytown from "@astrojs/partytown";
import sitemap from "@astrojs/sitemap";
import tailwind from "@astrojs/tailwind";
import compress from "astro-compress";
import svelte from "@astrojs/svelte";
import icon from "astro-icon";

// https://astro.build/config
export default defineConfig({
  site: SITE.origin,
  base: SITE.basePathname,
  output: "static",
  image: {
    service: squooshImageService(),
  },
  integrations: [
    partytown(),
    sitemap(),
    icon(),
    tailwind(),
    compress({
      CSS: true,
      HTML: {
        // removeAttributeQuotes: false,
      },
      Image: false,
      JavaScript: true,
      SVG: false,
      Logger: 1,
    }),
    svelte(),
  ],
});
