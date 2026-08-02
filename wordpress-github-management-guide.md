# WordPress 独立站的 GitHub 科学管理规范

> 适用项目：Kechoo WordPress 独立站  
> 适用对象：Codex、Claude Code、GitHub Copilot、其他代码 Agent、开发人员与运维人员  
> 文档目标：明确 WordPress 项目中代码、数据库、媒体资源、SEO 内容、部署与回滚的边界，确保 Agent 可以安全、可追踪地修改项目，而不会破坏线上业务。

---

## 1. 核心原则

WordPress 独立站不能像普通静态网站一样把所有内容都放进 GitHub。

整个项目应拆分为四类资产：

| 资产类型 | 主要存储位置 | 是否进入 GitHub |
|---|---|---|
| 主题、插件、模板、脚本 | GitHub | 是 |
| 页面、文章、商品、订单、配置 | WordPress 数据库 | 否 |
| 产品图片、PDF、视频、图纸 | WordPress uploads 或对象存储 | 否 |
| 项目规则、SEO 模板、内容模型 | GitHub | 是 |

最重要的边界：

> GitHub 管代码，WordPress 数据库管业务内容，媒体系统管图片和文件，备份系统负责恢复。

---

## 2. 推荐总体架构

```text
本地开发环境
    ↓ Git push
GitHub 私有仓库
    ↓ Pull Request / CI
测试环境 Staging
    ↓ 人工确认
正式环境 Production
```

建议环境：

```text
Local:       kechoo.local
Staging:     staging.kechoo.com
Production:  kechoo.com
```

生产环境只负责运行经过审核的代码。

任何 Agent 都不应直接登录生产服务器修改文件。

---

## 3. 推荐技术方案

### WordPress 运行环境

```text
Cloudflare
Nginx
PHP-FPM
MariaDB
WordPress
WooCommerce
GeneratePress
Gutenberg / GenerateBlocks
```

### GitHub 管理范围

建议使用一个私有仓库：

```text
kechoo-wp/
├── .github/
│   └── workflows/
├── wp-content/
│   ├── themes/
│   │   └── kechoo/
│   └── plugins/
│       └── kechoo-core/
├── scripts/
├── docs/
├── content/
├── tests/
├── AGENTS.md
├── README.md
├── .gitignore
├── composer.json
├── composer.lock
├── package.json
└── package-lock.json
```

---

## 4. 主题与插件的职责分离

### 4.1 主题负责外观

推荐：

```text
KECHOO custom theme
└── kechoo
```

`kechoo` 负责：

- Header
- Footer
- 页面布局
- 产品卡片
- WooCommerce 页面样式
- 首页模块
- 响应式样式
- 模板结构
- 视觉组件

主题目录示例：

```text
wp-content/themes/kechoo/
├── style.css
├── functions.php
├── theme.json
├── assets/
│   ├── css/
│   ├── js/
│   └── images/
├── inc/
├── template-parts/
└── woocommerce/
```

### 4.2 自定义插件负责业务能力

建立：

```text
wp-content/plugins/kechoo-core/
```

`kechoo-core` 负责：

- 产品自定义字段
- 产品规格模型
- 机器型号匹配
- 应用分类
- 自定义 Taxonomy
- 自定义 Post Type
- SEO Schema 扩展
- 询盘按钮逻辑
- API
- 数据库迁移
- 后台字段
- 定时任务
- 权限控制

目录示例：

```text
wp-content/plugins/kechoo-core/
├── kechoo-core.php
├── includes/
│   ├── class-product-fields.php
│   ├── class-machine-compatibility.php
│   ├── class-schema.php
│   ├── class-inquiry.php
│   └── class-migrations.php
├── admin/
├── public/
└── tests/
```

核心原则：

> 主题可以替换，业务数据不能跟着主题消失。

不要把所有功能堆进 `functions.php`。

---

## 5. GitHub 应管理什么

应进入 GitHub：

```text
自定义主题
自定义插件
部署脚本
测试脚本
CI 配置
数据库迁移代码
SEO 模板
内容模型
Agent 规则
项目文档
```

不应进入 GitHub：

```text
wp-config.php
.env
数据库导出
客户信息
用户信息
订单数据
uploads
缓存
备份
日志
支付密钥
SMTP 密码
Cloudflare Token
SSH 私钥
商业插件安装包
```

---

## 6. 推荐 `.gitignore`

```gitignore
# WordPress core
/wp-admin/
/wp-includes/
/wp-*.php
/index.php
/license.txt
/readme.html
/xmlrpc.php

# Config and secrets
wp-config.php
.env
.env.*
!.env.example

# Runtime files
/wp-content/uploads/
/wp-content/cache/
/wp-content/upgrade/
/wp-content/backups/
/wp-content/ai1wm-backups/
/wp-content/debug.log

# Third-party plugins
/wp-content/plugins/*
!/wp-content/plugins/kechoo-core/

# Third-party themes
/wp-content/themes/*
!/wp-content/themes/kechoo/

# Dependencies
node_modules/
vendor/

# Keep lock files
!composer.lock
!package-lock.json

# IDE and OS
.DS_Store
Thumbs.db
.idea/
.vscode/

# Build outputs
dist/
*.log
*.zip
```

注意：

`--delete`、`.gitignore` 和部署路径必须严格限制在自定义主题和自定义插件目录，避免误删 uploads 或第三方插件。

---

## 7. 分支策略

推荐：

```text
main
develop
feature/*
fix/*
hotfix/*
```

职责：

| 分支 | 用途 |
|---|---|
| main | 生产环境代码 |
| develop | 测试环境代码 |
| feature/* | 新功能 |
| fix/* | 普通 Bug 修复 |
| hotfix/* | 紧急生产修复 |

标准流程：

```text
创建 feature 分支
→ Agent 修改
→ 本地测试
→ 提交 Commit
→ 创建 Pull Request
→ 自动检查
→ 部署 Staging
→ 人工验证
→ 合并 main
→ 部署 Production
```

示例：

```bash
git checkout -b feature/product-spec-table
git add .
git commit -m "feat: add product specification table"
git push -u origin feature/product-spec-table
```

禁止：

```text
Agent 直接向 main 推送
Agent 直接修改生产文件
Agent 自动覆盖生产数据库
```

---

## 8. Agent 工作规范

仓库根目录必须包含：

```text
AGENTS.md
```

建议内容：

```markdown
# Kechoo WordPress Agent Rules

## Project architecture

- `wp-content/themes/kechoo` contains presentation code.
- `wp-content/plugins/kechoo-core` contains business logic.
- Never modify WordPress core.
- Never modify WooCommerce core.
- Never modify GeneratePress core.
- Never modify third-party plugins.
- Never commit secrets, database dumps, uploads, backups, or logs.

## WordPress rules

- Use WordPress hooks and filters.
- Escape all output.
- Sanitize all user input.
- Use nonces for state-changing requests.
- Use prepared SQL statements.
- All strings must be translatable.
- Do not add plugin dependencies without approval.

## WooCommerce rules

- Use hooks before template overrides.
- Use template overrides only when necessary.
- Preserve HPOS compatibility.
- Avoid large variation combinations.
- Do not alter checkout behavior without explicit approval.

## Performance rules

- Avoid database queries inside loops.
- Cache expensive queries.
- Load assets only where needed.
- Do not add Elementor or another page builder.
- Do not store large data in autoloaded options.
- Do not add frontend libraries unless necessary.

## Workflow

1. Read relevant files before editing.
2. Describe the intended change.
3. Make the smallest possible patch.
4. Run syntax and lint checks.
5. Report modified files.
6. Report database impact.
7. Report rollback steps.
```

---

## 9. Agent 任务应该如何描述

不推荐：

```text
全面优化网站
```

推荐：

```text
请为 WooCommerce 单个商品页增加产品规格模块。

要求：
1. 只允许修改 kechoo 和 kechoo-core。
2. 数据字段逻辑放在 kechoo-core。
3. 展示模板放在 kechoo。
4. 优先使用 WooCommerce hooks。
5. 不修改 WooCommerce 核心文件。
6. 所有输出必须 escaping。
7. 移动端使用单列布局。
8. 不增加第三方依赖。
9. 完成后运行 PHP 语法检查。
10. 报告修改文件、数据库影响和回滚方法。
```

Agent 每次提交必须说明：

- 修改了哪些文件
- 修改原因
- 是否涉及数据库
- 是否增加依赖
- 是否影响 WooCommerce
- 如何测试
- 如何回滚
- 存在哪些风险

---

## 10. 本地开发环境

### 简单方案

```text
LocalWP
Git
Codex / Claude Code
浏览器
```

适合 Windows 用户。

流程：

```text
Git clone
→ 将主题和插件放入 LocalWP
→ Agent 修改仓库
→ 本地测试
→ 提交 PR
```

### 自动化方案

使用 `wp-env`。

示例 `.wp-env.json`：

```json
{
  "core": null,
  "plugins": [
    "./wp-content/plugins/kechoo-core"
  ],
  "themes": [
    "./wp-content/themes/kechoo"
  ],
  "port": 8888,
  "config": {
    "WP_DEBUG": true,
    "SCRIPT_DEBUG": true
  }
}
```

运行：

```bash
npm install
npx wp-env start
```

生产 VPS 不运行 Docker 开发环境。

---

## 11. 测试环境

推荐：

```text
staging.kechoo.com
```

测试环境必须：

- noindex
- 禁止真实支付
- 使用支付 Sandbox
- 禁止向真实客户发邮件
- 使用测试 SMTP 或邮件捕获
- 不长期保存真实客户隐私数据
- 不直接与生产订单数据同步

1GB VPS 不适合长期同时运行两套完整 WordPress。

可采用：

```text
本地测试
→ 临时 Staging
→ 验证
→ 关闭 Staging
```

---

## 12. GitHub Actions 部署

推荐：

```text
Push develop
→ 自动测试
→ 部署 Staging

Merge main
→ 人工批准
→ 部署 Production
```

GitHub Secrets：

```text
DEPLOY_HOST
DEPLOY_USER
DEPLOY_SSH_KEY
```

不要使用 root 账户部署。

创建独立用户：

```bash
adduser deploy
```

仅授予写入以下目录的权限：

```text
wp-content/themes/kechoo
wp-content/plugins/kechoo-core
```

---

## 13. GitHub Actions 示例

```yaml
name: Deploy Production

on:
  push:
    branches:
      - main

concurrency:
  group: kechoo-production
  cancel-in-progress: false

jobs:
  test:
    runs-on: ubuntu-latest

    steps:
      - name: Checkout
        uses: actions/checkout@v4

      - name: Check PHP syntax
        run: |
          find wp-content/themes/kechoo \
               wp-content/plugins/kechoo-core \
               -name "*.php" -print0 |
          xargs -0 -n1 php -l

  deploy:
    needs: test
    runs-on: ubuntu-latest
    environment: production

    steps:
      - name: Checkout
        uses: actions/checkout@v4

      - name: Configure SSH
        run: |
          mkdir -p ~/.ssh
          echo "${{ secrets.DEPLOY_SSH_KEY }}" > ~/.ssh/id_ed25519
          chmod 600 ~/.ssh/id_ed25519
          ssh-keyscan -H "${{ secrets.DEPLOY_HOST }}" >> ~/.ssh/known_hosts

      - name: Deploy theme
        run: |
          rsync -az --delete \
            wp-content/themes/kechoo/ \
            "${{ secrets.DEPLOY_USER }}@${{ secrets.DEPLOY_HOST }}:/var/www/kechoo.com/wp-content/themes/kechoo/"

      - name: Deploy custom plugin
        run: |
          rsync -az --delete \
            wp-content/plugins/kechoo-core/ \
            "${{ secrets.DEPLOY_USER }}@${{ secrets.DEPLOY_HOST }}:/var/www/kechoo.com/wp-content/plugins/kechoo-core/"
```

---

## 14. 数据库管理原则

生产数据库是唯一业务真源。

```text
Production Database = 唯一业务真源
```

数据库保存：

- 页面
- 文章
- 商品
- 商品属性
- 商品变体
- SEO 元数据
- 用户
- 订单
- 库存
- 询盘
- 菜单
- 插件设置
- Gutenberg 内容
- 定时任务

禁止：

```text
用本地数据库覆盖生产数据库
```

正确方向：

```text
生产数据库
→ 导出
→ 脱敏
→ 导入本地或 Staging
```

---

## 15. 数据库同步

从生产同步到本地时必须：

### 脱敏

删除或替换：

- 用户邮箱
- 用户姓名
- 地址
- 电话
- 订单信息
- 询盘内容
- API Key
- SMTP 密码
- 支付密钥

### URL 替换

使用 WP-CLI：

```bash
wp search-replace \
  'https://kechoo.com' \
  'http://kechoo.local' \
  --all-tables \
  --skip-columns=guid
```

不要使用普通文本替换序列化数据。

### 禁止真实邮件

本地和 Staging：

```php
define('WP_ENVIRONMENT_TYPE', 'staging');
```

并关闭真实邮件和真实支付。

---

## 16. 数据库备份

### 每日

```bash
mysqldump \
  --single-transaction \
  --quick \
  --lock-tables=false \
  database_name > kechoo-db.sql

gzip kechoo-db.sql
```

### 每周

完整备份：

```text
数据库
uploads
自定义主题
自定义插件
wp-config.php
Nginx 配置
```

### 保存周期

| 类型 | 保留 |
|---|---|
| 每日数据库 | 7–14 天 |
| 每周完整备份 | 4–8 周 |
| 每月归档 | 6–12 个月 |

备份必须同步到远程：

```text
Cloudflare R2
Backblaze B2
AWS S3
Google Drive
另一台 VPS
```

不能只保存在当前 VPS。

---

## 17. 数据库恢复测试

至少每月一次：

```text
下载备份
→ 创建临时数据库
→ 导入 SQL
→ 恢复 uploads
→ 替换域名
→ 打开测试站
→ 检查首页、商品、后台、登录
```

只有可以恢复的备份才算有效备份。

---

## 18. 数据库迁移

Agent 新增数据库结构时，不允许直接手工执行不可追踪 SQL。

在 `kechoo-core` 中建立版本化迁移：

```php
define('KECHOO_CORE_DB_VERSION', '1.2.0');
```

示例：

```php
$current_version = get_option('kechoo_core_db_version');

if (version_compare($current_version, '1.2.0', '<')) {
    kechoo_run_migration_120();
    update_option('kechoo_core_db_version', '1.2.0');
}
```

迁移要求：

- 可重复执行
- 不删除现有数据
- 执行前备份
- 有日志
- 支持失败中止
- 不在每次页面访问时重复检查复杂结构

---

## 19. 数据库维护

建议：

```php
define('WP_POST_REVISIONS', 10);
define('AUTOSAVE_INTERVAL', 120);
```

不要完全关闭修订版本。

定期检查 autoload：

```sql
SELECT
    option_name,
    LENGTH(option_value) AS size
FROM wp_options
WHERE autoload = 'yes'
ORDER BY size DESC
LIMIT 20;
```

规则：

> 大型数据禁止存入 autoload option。

可定期清理：

- 过期 transients
- Spam comments
- 自动草稿
- 过多 revisions
- 过期 sessions
- Action Scheduler 历史任务
- 已卸载插件残留

清理前必须备份。

---

## 20. 图片资源管理

图片不进入 GitHub。

原因：

- Git 不适合大量二进制文件
- 仓库会快速膨胀
- clone 和部署变慢
- 图片每次修改都会产生完整历史
- Agent 不需要下载全部媒体素材

### 三层图片资产

#### 原始素材库

保存：

- 相机原图
- 产品源图
- PSD
- AI
- 视频
- EXIF 证据
- 包装源文件
- 工厂拍摄素材

存储：

```text
Google Drive
OneDrive
NAS
本地硬盘
```

目录建议：

```text
Kechoo-Media/
├── Products/
│   ├── Portable-Blades/
│   ├── Meat-Blades/
│   ├── Metal-Blades/
│   └── Wood-Blades/
├── Factory/
├── Applications/
├── Packaging/
├── Certificates/
├── OEM/
└── Source-Files/
```

#### 网站发布资源

经过：

- 裁剪
- 压缩
- 转 WebP
- 重命名
- SEO Alt 规划

再上传 WordPress。

#### 客户上传资源

例如：

- 图纸
- 机器铭牌
- CAD 文件
- 产品照片

必须与公开 uploads 分离，放到私有存储。

---

## 21. 图片命名规范

禁止：

```text
IMG_8282.jpg
微信图片_20260723.jpg
主图最终版2.jpg
```

推荐：

```text
m42-portable-band-saw-blade-835mm-14tpi.webp
meat-saw-blade-welded-joint-detail.webp
carbide-band-saw-blade-hardwood-cutting.webp
kechoo-band-saw-blade-factory-welding.webp
```

命名规则：

```text
产品类型 + 核心属性 + 场景或细节
```

---

## 22. 图片尺寸规范

| 用途 | 推荐尺寸 |
|---|---|
| 产品主图 | 1200×1200 |
| 产品缩略图 | 600×600 |
| 分类封面 | 1200×800 |
| 页面横幅 | 1920×900 或更小 |
| 文章正文图 | 1200–1600 宽 |
| Logo | SVG |
| 图标 | SVG |
| 规格示意图 | SVG 或 WebP |

文件体积建议：

| 类型 | 建议体积 |
|---|---|
| 产品主图 | 150–350KB |
| 详情图 | 100–300KB |
| Banner | 200–500KB |
| SVG | 尽量低于 50KB |

不要直接上传 5–15MB 手机原图。

---

## 23. WordPress 图片尺寸控制

WordPress 和 WooCommerce 会自动生成多个缩略图。

检查：

```bash
wp media image-size
```

建议保留：

- thumbnail
- medium
- large
- WooCommerce thumbnail
- WooCommerce single

删除不使用的自定义图片尺寸前，必须确认主题和 WooCommerce 不依赖它们。

---

## 24. 图片存储策略

### 初期

```text
wp-content/uploads
+ Cloudflare CDN
```

这是最简单方案。

### 后期

媒体达到数 GB 后，可迁移：

```text
Cloudflare R2
Backblaze B2
AWS S3
```

不要在项目早期为几十张图片引入复杂对象存储。

---

## 25. 客户上传图纸

客户上传文件必须：

- 限制类型
- 限制大小
- 随机文件名
- 禁止执行脚本
- 不进入公开媒体库
- 下载需权限
- 定期清理
- 设置保存期限

可允许：

```text
pdf
jpg
jpeg
png
webp
dxf
dwg
step
stp
```

CAD 文件优先进入私有对象存储。

---

## 26. SEO 内容管理

SEO 资产包括：

1. 产品页
2. 产品分类页
3. 尺寸页
4. 应用页
5. 机器匹配页
6. 技术文章
7. FAQ
8. 对比页
9. 选型指南

推荐内容模型：

```text
Product       → WooCommerce Product
Application   → Custom Post Type / Taxonomy
Machine       → Custom Post Type / Taxonomy
Size          → Taxonomy / Landing Page
Guide         → Posts
```

---

## 27. SEO 内容结构

### Products

```text
M42 Portable Band Saw Blade
Meat Cutting Band Saw Blade
Carbide-Tipped Band Saw Blade
```

### Product Categories

```text
Portable Band Saw Blades
Metal Cutting Band Saw Blades
Meat Saw Blades
Wood Cutting Band Saw Blades
```

### Sizes

```text
687mm Band Saw Blade
733mm Band Saw Blade
835mm Band Saw Blade
93-1/2 Inch Band Saw Blade
1650mm Meat Saw Blade
```

### Applications

```text
Band Saw Blades for Cutting Steel Pipe
Band Saw Blades for Frozen Meat
Band Saw Blades for Hardwood
Band Saw Blades for Anchor Cable
```

### Machines

```text
Band Saw Blade for DeWalt DCS374
Band Saw Blade for Milwaukee Portable Band Saw
Band Saw Blade for Generic Meat Saw Model
```

### Guides

```text
How to Measure Band Saw Blade Length
How to Choose Band Saw Blade TPI
M42 vs Carbon Steel Band Saw Blades
Why Band Saw Blades Break
```

---

## 28. SEO 系统与 SEO 内容的边界

进入 GitHub：

- URL 规则
- Schema 逻辑
- Breadcrumb
- 页面模板
- 默认标题模板
- Canonical 规则
- noindex 规则
- Sitemap 排除规则
- 内容字段定义
- 内链组件

保存在 WordPress 数据库：

- 标题
- H1
- 正文
- Meta Description
- FAQ
- 产品规格
- 内链
- 图片 Alt
- 分类介绍
- 页面状态

核心原则：

> GitHub 管 SEO 系统，数据库管 SEO 内容。

---

## 29. SEO 草稿与 GitHub

重要长文可以使用 Markdown 管理：

```text
content/
├── briefs/
├── drafts/
├── published/
└── data/
```

示例：

```text
content/drafts/how-to-choose-bandsaw-blade-tpi.md
```

Front Matter：

```yaml
---
title: "How to Choose Band Saw Blade TPI"
slug: "how-to-choose-bandsaw-blade-tpi"
primary_keyword: "band saw blade tpi"
status: "draft"
category: "Band Saw Blade Guides"
related_products:
  - "m42-portable-band-saw-blade"
---
```

推荐模式：

```text
普通商品内容 → WordPress
普通页面内容 → WordPress
重要 SEO 长文草稿 → Markdown + GitHub
正式发布版本 → WordPress 数据库
```

发布后应明确：

```text
WordPress = 线上正式版本
GitHub Markdown = 内容源稿或历史稿
```

---

## 30. SEO 内容发布流程

```text
关键词研究
→ 内容 Brief
→ Agent 草稿
→ 人工审核
→ WordPress Draft
→ 页面预览
→ SEO 检查
→ 发布
→ Search Console 监控
```

Agent 可以：

- 生成草稿
- 生成 Meta Description
- 建议内链
- 检查标题结构
- 生成 FAQ
- 检查重复内容
- 生成 Schema 数据
- 找出缺少 Alt 的图片

Agent 不应：

- 自动批量发布
- 自动建立几千个低质量页面
- 自动修改已有 URL
- 删除已有页面
- 自动改 canonical
- 生成虚假机器兼容信息
- 生成未经验证的产品参数

---

## 31. SEO 内容台账

建议维护：

| URL | 类型 | 主关键词 | 状态 | 搜索意图 | 关联产品 | 最后更新 |
|---|---|---|---|---|---|---|
| /portable-band-saw-blades/ | 分类 | portable band saw blades | 已发布 | 商业 | Portable Blades | 2026-07 |
| /835mm-band-saw-blade/ | 尺寸 | 835mm band saw blade | 草稿 | 交易 | 835mm Blade | — |
| /how-to-choose-tpi/ | Guide | band saw blade tpi | 已发布 | 信息 | M42 Blades | 2026-07 |

避免：

- 关键词互相竞争
- 重复页面
- 无价值批量页面
- 页面长期不更新
- 内链断裂

---

## 32. URL 规则

建议提前固定：

```text
/products/m42-portable-band-saw-blade/
/product-category/portable-band-saw-blades/
/applications/metal-cutting/
/machines/dewalt-dcs374/
/sizes/835mm-band-saw-blade/
/guides/how-to-choose-band-saw-blade-tpi/
```

上线后不要频繁变更 URL。

如必须变更：

- 建立 301
- 更新内链
- 更新 Sitemap
- 检查 canonical
- 检查 Search Console

---

## 33. 内容关联

每个产品页至少关联：

```text
产品分类
长度
TPI
应用
机器类型
技术指南
相关产品
```

示例：

```text
M42 Portable Band Saw Blade
├── Category: Portable Band Saw Blades
├── Length: 687mm / 733mm / 835mm
├── Application: Metal Cutting
├── Machine: Portable Band Saw
├── Guide: How to Choose TPI
└── Guide: How to Measure Blade Length
```

---

## 34. 回滚策略

### 代码回滚

```bash
git revert <commit>
```

重新部署即可。

### 发布目录

推荐：

```text
/var/www/kechoo.com/
├── current/
├── releases/
│   ├── 20260722-220001/
│   ├── 20260722-230015/
│   └── 20260723-010210/
└── shared/
    ├── wp-config.php
    └── uploads/
```

### 简化代码备份

```bash
tar -czf /var/backups/kechoo-code-$(date +%F-%H%M).tar.gz \
  wp-content/themes/kechoo \
  wp-content/plugins/kechoo-core
```

数据库结构变化前必须备份数据库。

---

## 35. 安全要求

Agent 不允许：

- 读取或输出生产密钥
- 提交 `.env`
- 提交 `wp-config.php`
- 提交数据库
- 提交客户文件
- 提交支付信息
- 使用 root 部署
- 修改服务器防火墙
- 修改生产数据库而无备份
- 自动删除生产数据
- 绕过 Pull Request

所有部署密钥必须放 GitHub Secrets。

---

## 36. 推荐最终架构

```text
GitHub 私有仓库
├── kechoo
├── kechoo-core
├── AGENTS.md
├── SEO 模板
├── 内容草稿
├── 数据库迁移
├── 自动测试
└── 自动部署

本地 Windows
├── LocalWP
├── Codex / Claude Code
├── Git
└── 浏览器测试

生产 VPS
├── WordPress Core
├── WooCommerce
├── GeneratePress
├── kechoo
├── kechoo-core
├── uploads
├── MariaDB
└── 自动备份
```

---

## 37. 最重要的管理纪律

1. 不让 Agent 修改 WordPress 核心。
2. 不让 Agent 修改 WooCommerce 核心。
3. 不让 Agent 修改 GeneratePress 核心。
4. 业务逻辑放 `kechoo-core`。
5. 外观和模板放 `kechoo`。
6. 所有代码改动走分支和 Pull Request。
7. GitHub 只管理代码，不管理数据库和 uploads。
8. 生产数据库是唯一业务真源。
9. 不用本地数据库覆盖生产数据库。
10. 数据库每日备份且远程保存。
11. 原始图片不上传 WordPress。
12. 客户图纸不进入公开媒体库。
13. SEO 规则代码化，SEO 内容数据库化。
14. Agent 可以写草稿，但不直接批量发布。
15. 所有数据库结构变化走迁移脚本。
16. 所有部署必须可回滚。
17. 所有密钥只放 Secrets。
18. 正式发布前必须经过 Staging 或本地验证。
19. 任何高风险改动必须先备份。
20. 任何 Agent 修改都必须报告测试和回滚方式。

---

## 38. Agent 执行前检查清单

在开始任何任务前，Agent 必须确认：

```text
[ ] 已读取 AGENTS.md
[ ] 已确认允许修改的目录
[ ] 未修改第三方代码
[ ] 未接触生产密钥
[ ] 已确认是否涉及数据库
[ ] 已确认是否涉及 WooCommerce
[ ] 已确认是否涉及 SEO URL
[ ] 已确认是否影响缓存
[ ] 已确认测试方式
[ ] 已确认回滚方式
```

---

## 39. Agent 提交后检查清单

```text
[ ] 已列出修改文件
[ ] 已说明修改原因
[ ] 已运行 PHP 语法检查
[ ] 已运行前端构建或 lint
[ ] 已测试移动端
[ ] 已测试 WooCommerce 页面
[ ] 已说明数据库影响
[ ] 已说明性能影响
[ ] 已说明安全风险
[ ] 已说明回滚步骤
```

---

## 40. 最终目标

这套管理方式的目标不是把 WordPress 变成纯代码项目，而是建立清晰边界：

```text
GitHub
→ 管站点能力

WordPress 数据库
→ 管业务内容和状态

媒体系统
→ 管图片和文件

备份系统
→ 管灾难恢复

Agent
→ 在规则内安全修改
```

最终应做到：

> 所有代码改动可追踪，所有业务数据有备份，所有图片有来源，所有 SEO 内容有结构，所有 Agent 修改可测试、可审核、可回滚。
