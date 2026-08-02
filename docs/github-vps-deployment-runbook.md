# GitHub to aaPanel VPS Deployment Runbook

This runbook records the standard KECHOO deployment flow for moving the site to a new VPS or rebuilding the current one.

## Deployment Model

GitHub is the source of truth for code. WordPress is the source of truth for live content and state.

```text
Local repository
  -> git commit and push
GitHub private repository
  -> VPS pulls with a read-only deploy key
VPS deploy checkout
  -> rsync only the custom theme and custom plugin
WordPress web root
  -> runs KECHOO theme, KECHOO Core, WooCommerce, uploads, and database content
```

Only these repository paths are deployed into WordPress:

```text
wp-content/themes/kechoo/
wp-content/plugins/kechoo-core/
```

Never deploy these repository paths into the public web root:

```text
.git/
AGENTS.md
README.md
DEPLOYMENT.md
docs/
data-templates/
test-site/
node_modules/
package.json
package-lock.json
.wordpress-playground/
```

## Current Staging Convention

During the first launch buildout, `kechoo.com` may be used as a temporary staging/pre-production site.

Required staging safeguards:

- WordPress `Settings -> Reading -> Discourage search engines from indexing this site` stays enabled.
- Do not submit the sitemap to Google Search Console.
- Do not enable real payments.
- Do not import real customer or order data.
- Do not enable aggressive Cloudflare full-page caching.
- Keep WooCommerce coming soon disabled only when the team needs to visually test the KECHOO frontend.

Before production launch:

- Confirm all public pages, RFQ, product catalog, SSL, email, and mobile views.
- Disable WordPress noindex only when the site is ready to be indexed.
- Submit `https://kechoo.com/wp-sitemap.xml` after launch.

## Current VPS Notes

The first VPS deployment was performed against:

```text
Host: 149.104.22.140
Web root: /www/wwwroot/kechoo.com
GitHub branch: codex/wordpress-deploy-prep
Deploy checkout used during first deployment: /tmp/kechoo-web-deploy
GitHub deploy key path: /root/.ssh/kechoo_github_deploy
```

The current `/tmp/kechoo-web-deploy` checkout is only a cache. It can be deleted by the operating system and should not be treated as durable infrastructure.

Before this server becomes a long-lived production host, move the deploy checkout to:

```text
/opt/kechoo/deploy/kechoo-web
```

The WordPress runtime files are already in the durable web root:

```text
/www/wwwroot/kechoo.com/wp-content/themes/kechoo
/www/wwwroot/kechoo.com/wp-content/plugins/kechoo-core
```

Important current-state safeguards:

- WordPress noindex is enabled while the site is being validated.
- WooCommerce coming soon was disabled so the KECHOO frontend can be tested.
- KECHOO Core and the KECHOO theme are active.
- RFQ entries are stored as private `kechoo_rfq` posts with metadata.
- Root-password access was used during initial setup; replace shared passwords and move to SSH key access before production.

## New VPS Prerequisites

aaPanel stack:

- Nginx
- MariaDB 10.6+ or MySQL 8.0+
- PHP 8.3
- WordPress
- WooCommerce

Required PHP modules:

```text
mysqli
pdo_mysql
curl
mbstring
xml
zip
gd or imagick
intl
fileinfo
exif
opcache
```

Required system tools:

```bash
apt-get update
apt-get install -y git rsync curl unzip tar
```

Recommended server layout:

```text
/www/wwwroot/kechoo.com                 WordPress web root
/opt/kechoo/deploy/kechoo-web           persistent GitHub deploy checkout
/root/.ssh/kechoo_github_deploy         deploy-key private key, root-only
```

For quick first-time deployments, `/tmp/kechoo-web-deploy` is acceptable as a cache, but it may be removed by the OS. Use `/opt/kechoo/deploy/kechoo-web` for a stable setup.

## GitHub Deploy Key Setup

Generate a read-only deploy key on the VPS:

```bash
mkdir -p /root/.ssh
chmod 700 /root/.ssh
ssh-keygen -t ed25519 -C "kechoo-vps-deploy" -f /root/.ssh/kechoo_github_deploy -N ""
chmod 600 /root/.ssh/kechoo_github_deploy
chmod 644 /root/.ssh/kechoo_github_deploy.pub
cat /root/.ssh/kechoo_github_deploy.pub
```

Add the public key to GitHub:

```text
GitHub repository -> Settings -> Deploy keys -> Add deploy key
Title: kechoo-vps-deploy
Allow write access: unchecked
```

Verify access from the VPS:

```bash
GIT_SSH_COMMAND='ssh -i /root/.ssh/kechoo_github_deploy -o StrictHostKeyChecking=accept-new' \
git ls-remote git@github.com:dequan/kechoo-web.git refs/heads/codex/wordpress-deploy-prep
```

## Local SSH Key for VPS Login

This is different from the GitHub deploy key above.

```text
GitHub deploy key:
  VPS -> GitHub private repository
  Used only to pull code from GitHub.

Local login key:
  Your Windows machine -> VPS
  Used to log in without typing the root password.
```

If the Windows machine already has an SSH key:

```text
C:\Users\jojo\.ssh\id_ed25519
C:\Users\jojo\.ssh\id_ed25519.pub
```

show the public key in PowerShell:

```powershell
Get-Content $env:USERPROFILE\.ssh\id_ed25519.pub
```

Copy the full single-line public key. It should start with:

```text
ssh-ed25519
```

Add it to the VPS root account:

```bash
mkdir -p /root/.ssh
chmod 700 /root/.ssh
echo 'paste-the-full-public-key-here' >> /root/.ssh/authorized_keys
chmod 600 /root/.ssh/authorized_keys
```

Then test from Windows:

```powershell
ssh root@149.104.22.140
```

If it logs in without asking for the root password, the key is working.

For routine production deployment, prefer a limited `deploy` user instead of root:

```bash
adduser deploy
mkdir -p /home/deploy/.ssh
chmod 700 /home/deploy/.ssh
echo 'paste-the-full-public-key-here' >> /home/deploy/.ssh/authorized_keys
chmod 600 /home/deploy/.ssh/authorized_keys
chown -R deploy:deploy /home/deploy/.ssh
```

Then test:

```powershell
ssh deploy@149.104.22.140
```

Do not copy or paste the private key file `id_ed25519` into the VPS, GitHub, chat, or project files. Only the `.pub` public key is shared.

## First Deploy or Rebuild

Set variables:

```bash
REPO_DIR=/opt/kechoo/deploy/kechoo-web
BRANCH=codex/wordpress-deploy-prep
SITE_ROOT=/www/wwwroot/kechoo.com
export GIT_SSH_COMMAND='ssh -i /root/.ssh/kechoo_github_deploy -o StrictHostKeyChecking=accept-new'
```

Clone the deploy checkout:

```bash
mkdir -p "$(dirname "$REPO_DIR")"
git clone --branch "$BRANCH" --single-branch git@github.com:dequan/kechoo-web.git "$REPO_DIR"
```

Run PHP syntax checks before syncing:

```bash
find "$REPO_DIR/wp-content/themes/kechoo" \
     "$REPO_DIR/wp-content/plugins/kechoo-core" \
     -name "*.php" -print0 |
xargs -0 -n1 php -l
```

Sync only KECHOO code:

```bash
mkdir -p "$SITE_ROOT/wp-content/themes/kechoo" "$SITE_ROOT/wp-content/plugins/kechoo-core"

rsync -az --delete \
  "$REPO_DIR/wp-content/themes/kechoo/" \
  "$SITE_ROOT/wp-content/themes/kechoo/"

rsync -az --delete \
  "$REPO_DIR/wp-content/plugins/kechoo-core/" \
  "$SITE_ROOT/wp-content/plugins/kechoo-core/"

chown -R www:www "$SITE_ROOT/wp-content/themes/kechoo" "$SITE_ROOT/wp-content/plugins/kechoo-core"
find "$SITE_ROOT/wp-content/themes/kechoo" "$SITE_ROOT/wp-content/plugins/kechoo-core" -type d -exec chmod 755 {} +
find "$SITE_ROOT/wp-content/themes/kechoo" "$SITE_ROOT/wp-content/plugins/kechoo-core" -type f -exec chmod 644 {} +
```

Activate in WordPress:

```text
WordPress admin -> Plugins -> Activate KECHOO Core
WordPress admin -> Appearance -> Themes -> Activate KECHOO
WordPress admin -> Settings -> Permalinks -> Post name -> Save
```

If using SSH, back up the database before theme/plugin activation, then activate through WordPress APIs.

## Routine Code Update

Commit and push from local:

```bash
git status --short
git add <intended files>
git commit -m "short change description"
git push
```

Update the VPS deploy checkout:

```bash
REPO_DIR=/opt/kechoo/deploy/kechoo-web
BRANCH=codex/wordpress-deploy-prep
SITE_ROOT=/www/wwwroot/kechoo.com
export GIT_SSH_COMMAND='ssh -i /root/.ssh/kechoo_github_deploy -o StrictHostKeyChecking=accept-new'

git -C "$REPO_DIR" fetch origin "$BRANCH"
git -C "$REPO_DIR" checkout "$BRANCH"
git -C "$REPO_DIR" reset --hard "origin/$BRANCH"

find "$REPO_DIR/wp-content/themes/kechoo" \
     "$REPO_DIR/wp-content/plugins/kechoo-core" \
     -name "*.php" -print0 |
xargs -0 -n1 php -l

rsync -az --delete "$REPO_DIR/wp-content/themes/kechoo/" "$SITE_ROOT/wp-content/themes/kechoo/"
rsync -az --delete "$REPO_DIR/wp-content/plugins/kechoo-core/" "$SITE_ROOT/wp-content/plugins/kechoo-core/"
chown -R www:www "$SITE_ROOT/wp-content/themes/kechoo" "$SITE_ROOT/wp-content/plugins/kechoo-core"
```

## WordPress Content Updates

Code deployment does not automatically overwrite existing WordPress pages, products, menus, RFQs, settings, or uploads.

When repository default content changes, such as seeded page copy in `class-kechoo-site-setup.php`, existing WordPress pages may need a separate content update through:

- WordPress admin editor, or
- a small one-time WordPress API script after taking a database backup.

Always report whether a change affected:

- files only,
- WordPress database content,
- WooCommerce settings,
- SEO URLs,
- cache behavior.

## Verification Checklist

After each deployment:

```bash
/www/server/nginx/sbin/nginx -t

curl -L -o /dev/null -s -w "%{http_code}\n" http://127.0.0.1/ -H "Host: kechoo.com"
curl -L -o /dev/null -s -w "%{http_code}\n" http://127.0.0.1/request-a-quote/ -H "Host: kechoo.com"
curl -L -o /dev/null -s -w "%{http_code}\n" http://127.0.0.1/about/ -H "Host: kechoo.com"
```

WordPress checks:

- `KECHOO` theme active.
- `KECHOO Core` plugin active.
- Permalinks use `/%postname%/`.
- `blog_public=0` while staging.
- RFQ form submits.
- RFQ details appear in WordPress admin.
- No public test prices or Add to cart buttons appear in public-lite mode.

## Rollback

Code rollback:

```bash
cd /path/to/local/repo
git revert <bad_commit>
git push
```

Then run the routine VPS update.

Emergency file rollback:

```bash
git -C "$REPO_DIR" reset --hard <known_good_commit>
rsync -az --delete "$REPO_DIR/wp-content/themes/kechoo/" "$SITE_ROOT/wp-content/themes/kechoo/"
rsync -az --delete "$REPO_DIR/wp-content/plugins/kechoo-core/" "$SITE_ROOT/wp-content/plugins/kechoo-core/"
```

Database rollback:

- Restore only from a backup taken before the database-affecting operation.
- Do not use a local database to overwrite production.
- Prefer targeted fixes over full database restore when only page copy or settings changed.

## Migration to a New VPS

1. Install aaPanel stack and PHP modules.
2. Create `kechoo.com` site and database.
3. Install WordPress and WooCommerce.
4. Keep WordPress noindex enabled until final launch.
5. Generate a new VPS deploy key and add it to GitHub Deploy keys.
6. Clone the deploy checkout into `/opt/kechoo/deploy/kechoo-web`.
7. Sync only `wp-content/themes/kechoo` and `wp-content/plugins/kechoo-core`.
8. Activate KECHOO Core and KECHOO theme.
9. Save permalinks.
10. Restore or recreate WordPress content:
    - use production database backup for real migration,
    - use plugin setup/default pages for a fresh staging build.
11. Restore `uploads` only from trusted backups or object storage.
12. Configure SSL, SMTP, backups, and Cloudflare.
13. Run the verification checklist.
14. Disable noindex only after final production review.

## Security Notes

- Replace any password that has been shared in chat or logs.
- Prefer SSH key login over root password login.
- Create a limited `deploy` user for routine deployment when possible.
- Keep the GitHub deploy key read-only.
- Do not expose MariaDB/MySQL to the public internet.
- Store production secrets in aaPanel, WordPress config, GitHub Secrets, or a password manager, never in Git.
