/**
 * Bolent 门户品牌配置 v2.0
 * 对齐 Phase 1 品牌规范：数智企业门户定位
 */

export const companyInfo = {
  name: 'Bolent',
  fullName: 'Bolent Digital Intelligence',
  chineseName: '数智企业门户',
  tagline: '以工程精度交付价值，以人文视角连接未来',
  slogan: 'Bolent — 融合科技与人文的数智企业',

  description:
    'Bolent 是一家融合现代科技与文化底蕴的数智企业。我们提供软件开发、数字化、自动化、IT 管理与 IT 外包等全方位服务，并以 AI 读诗为特色板块，让技术在诗意中落地。',

  contact: {
    email: 'hello@szbolent.cn',
    support: 'support@szbolent.cn',
    workTime: '周一至周五 9:00–18:00',
  },

  address: {
    main: {
      city: '中国',
      full: 'szbolent.cn',
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
  },

  values: [
    {
      icon: '◆',
      title: '领域专业',
      description: '深厚的行业经验与专业技能积累',
    },
    {
      icon: '▲',
      title: '卓越品质',
      description: '工程级精度，交付高质量解决方案',
    },
    {
      icon: '●',
      title: '技术前沿',
      description: '采用最新技术与行业最佳实践',
    },
    {
      icon: '✦',
      title: '人文视角',
      description: '以文化底蕴驱动有温度的产品体验',
    },
  ],

  certifications: [] as string[],
  partners: [] as Array<{ name: string; logo: string }>,
}

export const seoConfig = {
  defaultTitle: 'Bolent — 数智企业门户',
  titleTemplate: '%s | Bolent',
  defaultDescription:
    'Bolent 是融合现代科技与文化底蕴的数智企业，提供软件开发、数字化、IT管理及AI读诗等全方位服务。',
  keywords: [
    'Bolent',
    '数智企业',
    '软件开发',
    'AI读诗',
    '数字化转型',
    'IT服务',
    '企业门户',
  ],
  siteUrl: 'https://szbolent.cn',
  ogImage: '/images/og-image.jpg',
}

export default {
  company: companyInfo,
  seo: seoConfig,
}
