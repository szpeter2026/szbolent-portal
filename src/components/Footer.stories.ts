import type { Meta, StoryObj } from "@storybook/vue3-vite";
import Footer from "./Footer.vue";

const meta: Meta<typeof Footer> = {
  title: "Components/Footer",
  component: Footer,
  tags: ["autodocs"],
};

export default meta;
type Story = StoryObj<typeof Footer>;

export const Default: Story = {};
