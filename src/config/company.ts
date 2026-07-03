/**
 * szbolent.cn 门户品牌配置
 * 继承自 SurfaceZervi/archive-gitee/szbenyx/bolent/web-public
 */

export const companyInfo = {
  name: 'SZBolent',
  fullName: 'SZBolent Poetry Portal',
  chineseName: '诗词门户',
  tagline: '以诗会友，以 AI 读诗',
  slogan: 'szbolent.cn — 智能化诗词与个人品牌门户',

  description:
    'SZBolent 是面向诗词爱好者与个人品牌展示的智能化门户：精选古典诗词、博客文章与 AI 辅助赏析，与 JobFirst 求职主线刻意分离。',

  contact: {
    email: 'hello@szbolent.cn',
    support: 'support@szbolent.cn',
    workTime: '周一至周五 9:00–18:00',
  },

  address: {
    main: {
      city: '中国',
      full: '线上门户 szbolent.cn',
      postcode: '',
    },
  },

  social: {
    wechat: {
      name: 'SZBolent',
      qrcode: '/images/qrcode/wechat.jpg',
    },
    github: {
      name: 'szbolent',
      url: 'https://github.com/szbolent',
    },
  },

  stats: {
    foundedYear: 2026,
    poemCorpus: '78k+',
    aiModules: 'poetry_rag',
  },

  values: [
    {
      icon: '📜',
      title: '诗词为本',
      description: '古典语料与当代阅读体验结合',
    },
    {
      icon: '🤖',
      title: 'AI 增强',
      description: '智能推荐、赏析与检索，不替代人文判断',
    },
    {
      icon: '🌐',
      title: '开放门户',
      description: 'Headless CMS + 现代前端，内容可运营',
    },
    {
      icon: '🔗',
      title: '溯源清晰',
      description: '工程与 SurfaceZervi bolent-content 线对齐',
    },
  ],

  certifications: [] as string[],
  partners: [] as Array<{ name: string; logo: string }>,
}

export const seoConfig = {
  defaultTitle: 'SZBolent — 智能化诗词门户',
  titleTemplate: '%s | szbolent.cn',
  defaultDescription:
    'szbolent.cn：古典诗词鉴赏、个人品牌展示与 AI 辅助读诗。Headless WordPress 内容 + Vue 门户 + Tatha 诗词 RAG。',
  keywords: [
    'szbolent',
    '诗词',
    '古典文学',
    'AI读诗',
    '个人品牌',
    'headless WordPress',
  ],
  siteUrl: 'https://szbolent.cn',
  ogImage: '/images/og-image.jpg',
}

export default {
  company: companyInfo,
  seo: seoConfig,
}
