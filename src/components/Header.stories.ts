import type { Meta, StoryObj } from "@storybook/vue3-vite";
import Header from "./Header.vue";

const meta: Meta<typeof Header> = {
  title: "Components/Header",
  component: Header,
  tags: ["autodocs"],
};

export default meta;
type Story = StoryObj<typeof Header>;

export const Default: Story = {};

export const MobileMenuOpen: Story = {
  play: async ({ canvasElement }) => {
    const toggle = canvasElement.querySelector(".mobile-menu-toggle") as HTMLButtonElement | null;
    toggle?.click();
  },
};
