import type { Preview } from "@storybook/vue3-vite";
import { setup } from "@storybook/vue3-vite";
import { createRouter, createMemoryHistory } from "vue-router";
import "../src/assets/styles/main.scss";

const stubRoute = { template: "<div />" };

const router = createRouter({
  history: createMemoryHistory(),
  routes: [
    { path: "/", component: stubRoute },
    { path: "/about", component: stubRoute },
    { path: "/blog", component: stubRoute },
    { path: "/case-study", component: stubRoute },
    { path: "/careers", component: stubRoute },
    { path: "/contact", component: stubRoute },
    { path: "/services/:slug", component: stubRoute },
  ],
});

setup((app) => {
  app.use(router);
});

const preview: Preview = {
  parameters: {
    layout: "fullscreen",
    controls: {
      matchers: {
        color: /(background|color)$/i,
        date: /Date$/i,
      },
    },
    a11y: {
      test: "todo",
    },
  },
};

export default preview;
