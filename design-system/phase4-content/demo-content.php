<?php
/**
 * Bolent 示例内容数据
 * 对应外包需求：示例职位 8 个 + 示例案例 6 个 + 首页文案
 *
 * 使用方法：
 * 1. 将此文件放到子主题 acf-fields/ 目录
 * 2. 在 WordPress 后台 → 工具 → 导入 → 运行
 * 或在 functions.php 的 after_switch_theme 钩子中自动执行
 */

if (!defined('ABSPATH')) exit;

/**
 * 8 个示例职位
 */
function bolent_import_demo_jobs() {
    $jobs = array(
        array(
            'title' => '高级前端工程师',
            'department' => '技术部',
            'location' => '深圳',
            'type' => '全职',
            'salary_min' => 25, 'salary_max' => 40,
            'experience' => '5年以上', 'education' => '本科及以上',
            'requirements' => "精通 Vue 3 / React\n熟悉 TypeScript\n有大型项目架构经验\n良好的代码规范意识",
            'status' => 'open',
        ),
        array(
            'title' => '后端开发工程师',
            'department' => '技术部',
            'location' => '深圳',
            'type' => '全职',
            'salary_min' => 20, 'salary_max' => 35,
            'experience' => '3-5年', 'education' => '本科及以上',
            'requirements' => "精通 Python / Go / Node.js\n熟悉 PostgreSQL / Redis\n有微服务架构经验\n了解 Docker / K8s",
            'status' => 'open',
        ),
        array(
            'title' => 'AI 算法工程师',
            'department' => 'AI 实验室',
            'location' => '远程',
            'type' => '全职',
            'salary_min' => 30, 'salary_max' => 50,
            'experience' => '3年以上', 'education' => '硕士及以上',
            'requirements' => "熟悉 LLM / RAG 架构\n有 NLP 项目经验\n了解 ChromaDB / 向量数据库\nPython 工程能力扎实",
            'status' => 'open',
        ),
        array(
            'title' => 'UI/UX 设计师',
            'department' => '设计部',
            'location' => '深圳',
            'type' => '全职',
            'salary_min' => 18, 'salary_max' => 30,
            'experience' => '3-5年', 'education' => '本科及以上',
            'requirements' => "精通 Figma\n有企业级产品设计经验\n理解设计体系\n良好的沟通能力",
            'status' => 'open',
        ),
        array(
            'title' => '测试自动化工程师',
            'department' => '质量保障部',
            'location' => '深圳',
            'type' => '全职',
            'salary_min' => 15, 'salary_max' => 25,
            'experience' => '3-5年', 'education' => '本科及以上',
            'requirements' => "熟悉 Cypress / Playwright\n有 CI/CD 集成经验\n了解性能测试\n良好的质量意识",
            'status' => 'open',
        ),
        array(
            'title' => 'DevOps 工程师',
            'department' => '运维部',
            'location' => '远程',
            'type' => '全职',
            'salary_min' => 22, 'salary_max' => 38,
            'experience' => '3-5年', 'education' => '本科及以上',
            'requirements' => "精通 Docker / Kubernetes\n熟悉云平台 (阿里云/腾讯云)\n有 CI/CD 流水线经验\n了解监控体系",
            'status' => 'open',
        ),
        array(
            'title' => '产品经理',
            'department' => '产品部',
            'location' => '深圳',
            'type' => '全职',
            'salary_min' => 20, 'salary_max' => 35,
            'experience' => '5年以上', 'education' => '本科及以上',
            'requirements' => "有 B 端产品经验\n熟悉敏捷开发流程\n数据驱动决策能力\n跨部门协调能力",
            'status' => 'open',
        ),
        array(
            'title' => '诗词内容编辑',
            'department' => '内容部',
            'location' => '远程',
            'type' => '兼职',
            'salary_min' => 8, 'salary_max' => 15,
            'experience' => '不限', 'education' => '本科及以上',
            'requirements' => "古典文学 / 中文相关专业\n热爱诗词文化\n有内容编辑经验\n了解新媒体运营",
            'status' => 'open',
        ),
    );

    foreach ($jobs as $job) {
        $dept = $job['department']; unset($job['department']);
        $loc  = $job['location'];   unset($job['location']);
        $type = $job['type'];        unset($job['type']);

        $post_id = wp_insert_post(array(
            'post_type'    => 'bolent_job',
            'post_title'   => $job['title'],
            'post_status'  => 'publish',
            'post_content' => $job['title'] . ' — 详细职位描述。',
        ));

        if ($post_id && !is_wp_error($post_id)) {
            update_post_meta($post_id, '_job_salary_min', $job['salary_min']);
            update_post_meta($post_id, '_job_salary_max', $job['salary_max']);
            update_post_meta($post_id, '_job_experience', $job['experience']);
            update_post_meta($post_id, '_job_education', $job['education']);
            update_post_meta($post_id, '_job_requirements', $job['requirements']);
            update_post_meta($post_id, '_job_status', $job['status']);

            wp_set_object_terms($post_id, $dept, 'job_department');
            wp_set_object_terms($post_id, $loc, 'job_location');
            wp_set_object_terms($post_id, $type, 'job_type');
        }
    }
}

/**
 * 6 个示例案例
 */
function bolent_import_demo_cases() {
    $cases = array(
        array(
            'title' => '某金融企业数字化平台重构',
            'client' => '某头部金融科技公司',
            'industry' => '金融',
            'tags' => 'Web开发,微服务,数据可视化',
            'metrics' => array('效率提升 40%', '交付周期缩短 30%', '系统稳定性 99.9%'),
            'result' => '通过微服务架构重构和 CI/CD 流水线搭建，实现从月度发布到每日发布的跨越。',
        ),
        array(
            'title' => '智慧教育平台全栈开发',
            'client' => '某省级教育机构',
            'industry' => '教育',
            'tags' => 'Vue3,Node.js,实时通信',
            'metrics' => array('用户增长 200%', '日活提升 150%', '响应延迟降低 60%'),
            'result' => '构建在线课堂、作业管理、数据看板一体化平台，覆盖 50 万师生用户。',
        ),
        array(
            'title' => 'AI 诗词解析引擎',
            'client' => 'Bolent 自研',
            'industry' => '文化',
            'tags' => 'LLM,RAG,NLP,ChromaDB',
            'metrics' => array('诗词解析 3000+', '准确率 92%', '响应时间 <2s'),
            'result' => '基于 RAG 架构构建古典诗词智能解析引擎，实现意境可视化与文化数据传承。',
        ),
        array(
            'title' => '零售连锁数字化升级',
            'client' => '某全国连锁零售品牌',
            'industry' => '零售',
            'tags' => '数据分析,Dashboard,自动化',
            'metrics' => array('库存周转提升 35%', '人工成本降低 25%', '销售额增长 18%'),
            'result' => '搭建门店数据采集与实时分析平台，实现 500 家门店的统一数字化管理。',
        ),
        array(
            'title' '医疗数据治理与合规',
            'client' => '某三甲医院',
            'industry' => '医疗',
            'tags' => '数据治理,安全审计,合规',
            'metrics' => array('数据质量提升 50%', '合规率 100%', '查询效率提升 3 倍'),
            'result' => '建立医疗数据治理体系，完成等保三级认证，数据安全与合规双达标。',
        ),
        array(
            'title' '制造企业 MES 系统定制',
            'client' => '某大型制造企业',
            'industry' => '制造',
            'tags' => 'MES,物联网,DevOps',
            'metrics' => array('产能提升 22%', '设备故障率降低 40%', '交付准时率 98%'),
            'result' => '定制开发 MES 生产执行系统，集成 IoT 设备监控，实现生产全流程数字化。',
        ),
    );

    foreach ($cases as $case) {
        $post_id = wp_insert_post(array(
            'post_type'    => 'bolent_case',
            'post_title'   => $case['title'],
            'post_status'  => 'publish',
            'post_content' => $case['result'],
            'post_excerpt' => $case['result'],
        ));

        if ($post_id && !is_wp_error($post_id)) {
            update_post_meta($post_id, '_case_client', $case['client']);
            update_post_meta($post_id, '_case_tags', $case['tags']);
            update_post_meta($post_id, '_case_metric_1', $case['metrics'][0]);
            update_post_meta($post_id, '_case_metric_2', $case['metrics'][1]);
            update_post_meta($post_id, '_case_metric_3', $case['metrics'][2]);
            update_post_meta($post_id, '_case_result', $case['result']);
            wp_set_object_terms($post_id, $case['industry'], 'case_industry');
        }
    }
}

/**
 * 6 个示例服务
 */
function bolent_import_demo_services() {
    $services = array(
        array('icon' => '◆', 'title' => '数字化 & 数据', 'desc' => '实时、准确、可靠的数据采集与分析体系', 'features' => '数据采集与清洗,实时分析看板,数据治理咨询,BI 可视化', 'featured' => false),
        array('icon' => '▲', 'title' => '软件开发', 'desc' => '从 Web 到移动端，为企业打造核心数字化产品', 'features' => 'Web 应用开发,移动端开发,微服务架构,DevOps 流水线', 'featured' => false),
        array('icon' => '●', 'title' => '自动化 & QA', 'desc' => '测试自动化工具与质量保障体系', 'features' => '自动化测试框架,性能压测,CI/CD 集成,质量度量', 'featured' => false),
        array('icon' => '■', 'title' => 'IT 管理', 'desc' => '企业级 IT 管理与基础设施运维', 'features' => '网络监控,云基础设施,安全审计,运维自动化', 'featured' => false),
        array('icon' => '✦', 'title' => 'AI 读诗', 'desc' => '以人工智能解读古典诗词，科技在诗意中落地', 'features' => '诗词智能解析,意境可视化,古文 NLP 引擎,文化数据传承', 'featured' => true),
        array('icon' => '◈', 'title' => 'IT 外包', 'desc' => '不只是成本套利，更是优质人才与高质量交付', 'features' => '驻场开发,项目外包,技术顾问,团队搭建', 'featured' => false),
    );

    foreach ($services as $svc) {
        $post_id = wp_insert_post(array(
            'post_type'    => 'bolent_service',
            'post_title'   => $svc['title'],
            'post_status'  => 'publish',
            'post_content' => $svc['desc'],
            'post_excerpt' => $svc['desc'],
            'menu_order'   => $svc['featured'] ? 5 : 0,
        ));

        if ($post_id && !is_wp_error($post_id)) {
            update_post_meta($post_id, '_service_icon', $svc['icon']);
            update_post_meta($post_id, '_service_short_desc', $svc['desc']);
            update_post_meta($post_id, '_service_features', $svc['features']);
            update_post_meta($post_id, '_service_is_featured', $svc['featured'] ? '1' : '0');
        }
    }
}

/**
 * 一键导入所有示例内容
 */
function bolent_import_all_demo() {
    if (get_option('bolent_demo_imported') === 'yes') return;

    bolent_import_demo_services();
    bolent_import_demo_jobs();
    bolent_import_demo_cases();

    update_option('bolent_demo_imported', 'yes');
}
// 在 functions.php 的 after_switch_theme 钩子中调用 bolent_import_all_demo()
