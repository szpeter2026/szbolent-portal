import type { Meta, StoryObj } from "@storybook/vue3-vite";
import LuckyWheel from "./LuckyWheel.vue";

const samplePrizes = [
  { name: "谢谢参与", num: 100, percent: 40, type: 0 as const },
  { name: "10 元券", num: 20, percent: 30, type: 1 as const },
  { name: "50 元券", num: 5, percent: 20, type: 1 as const },
  { name: "神秘大奖", num: 1, percent: 10, type: 1 as const },
];

const meta: Meta<typeof LuckyWheel> = {
  title: "Components/LuckyWheel",
  component: LuckyWheel,
  tags: ["autodocs"],
  args: {
    activityId: 1,
    userId: 1,
    prizes: samplePrizes,
  },
  parameters: {
    docs: {
      description: {
        component:
          "依赖 legacy activity API；Storybook 中抽奖次数为 0（API 不可用），转盘绘制与布局仍可验收。",
      },
    },
  },
};

export default meta;
type Story = StoryObj<typeof LuckyWheel>;

export const Default: Story = {};

export const SixPrizes: Story = {
  args: {
    prizes: [
      ...samplePrizes,
      { name: "再来一次", num: 10, percent: 5, type: 0 as const },
      { name: "纪念徽章", num: 3, percent: 5, type: 1 as const },
    ],
  },
};
