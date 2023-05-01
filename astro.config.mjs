import { defineConfig } from "astro/config";
import { SITE } from "./src/config.mjs";
import partytown from "@astrojs/partytown";
import sitemap from "@astrojs/sitemap";
import tailwind from "@astrojs/tailwind";
import image from "@astrojs/image";
import compress from "astro-compress";
import svelte from "@astrojs/svelte";

// https://astro.build/config
export default defineConfig({
  site: SITE.origin,
  base: SITE.basePathname,
  output: "static",
  integrations: [
    partytown(),
    sitemap(),
    tailwind(),
    image(),
    compress({
      css: true,
      html: {
        removeAttributeQuotes: false,
      },
      img: false,
      js: true,
      svg: false,
      logger: 1,
    }),
    svelte(),
  ],
});
