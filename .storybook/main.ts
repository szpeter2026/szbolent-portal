import type { StorybookConfig } from "@storybook/vue3-vite";

const config: StorybookConfig = {
  stories: ["../src/**/*.stories.@(js|jsx|mjs|ts|tsx)"],
  addons: ["@storybook/addon-a11y", "@storybook/addon-docs"],
  framework: "@storybook/vue3-vite",
  async viteFinal(config) {
    config.resolve ??= {};
    config.resolve.dedupe = [...(config.resolve.dedupe ?? []), "react", "react-dom"];
    config.optimizeDeps ??= {};
    config.optimizeDeps.include = [
      ...(config.optimizeDeps.include ?? []),
      "react",
      "react-dom",
      "react/jsx-runtime",
    ];
    return config;
  },
};

export default config;
