#!/bin/sh
# Build wp-admin-installable ZIPs of the theme and plugin from the committed
# tree (HEAD), so whoever deploys only needs wp-admin "Upload Theme/Plugin":
#   release/blackroll-theme.zip        → Appearance › Themes › Add New › Upload
#   release/blackroll-core-plugin.zip  → Plugins › Add New › Upload
# Run after committing (dist/ included): `npm run package`, then commit release/.
set -e
cd "$(dirname "$0")/.."
mkdir -p release
git archive --format=zip --prefix=blackroll/ HEAD:themes/blackroll -o release/blackroll-theme.zip
git archive --format=zip --prefix=blackroll-core/ HEAD:plugins/blackroll-core -o release/blackroll-core-plugin.zip
git rev-parse --short HEAD > release/BUILD-COMMIT.txt
ls -la release
