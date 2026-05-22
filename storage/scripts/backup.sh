#!/usr/bin/env bash
#
# Phase 13: daily backup script for cPanel cron.
#
# Dumps the MySQL/MariaDB database and tarballs storage/, naming files by
# date. Keeps the last 14 dailies; older ones are pruned. Optional rclone
# upload to a remote (S3, Google Drive) if BACKUP_REMOTE is set.
#
# cPanel cron entry (set via cPanel UI → Cron Jobs):
#   30 3 * * * /home/<USER>/ERD-deploy/storage/scripts/backup.sh >> /home/<USER>/ERD-deploy/storage/logs/backup.log 2>&1
#
# Env vars used (read from .env in the app root):
#   DB_DATABASE, DB_USERNAME, DB_PASSWORD, DB_HOST, DB_PORT
#   BACKUP_DIR      — default storage/backups
#   BACKUP_KEEP     — default 14 (days)
#   BACKUP_REMOTE   — optional rclone remote:path (e.g. s3:adly-backups/erd)

set -euo pipefail

APP_ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/../.." && pwd)"
cd "$APP_ROOT"

# Parse .env (basic; only the keys we care about)
if [[ -f .env ]]; then
    export $(grep -E '^(DB_DATABASE|DB_USERNAME|DB_PASSWORD|DB_HOST|DB_PORT|BACKUP_DIR|BACKUP_KEEP|BACKUP_REMOTE)=' .env | sed 's/#.*//' | xargs -d '\n' -I{} echo {})
fi

: "${DB_DATABASE:?DB_DATABASE not set in .env}"
: "${DB_USERNAME:?DB_USERNAME not set in .env}"
: "${DB_PASSWORD:?DB_PASSWORD not set in .env}"
DB_HOST="${DB_HOST:-127.0.0.1}"
DB_PORT="${DB_PORT:-3306}"

BACKUP_DIR="${BACKUP_DIR:-storage/backups}"
BACKUP_KEEP="${BACKUP_KEEP:-14}"
TIMESTAMP="$(date +%Y-%m-%d_%H%M%S)"

mkdir -p "$BACKUP_DIR"

DB_FILE="$BACKUP_DIR/db_${DB_DATABASE}_${TIMESTAMP}.sql.gz"
STORAGE_FILE="$BACKUP_DIR/storage_${TIMESTAMP}.tar.gz"

echo "[backup $TIMESTAMP] mysqldump $DB_DATABASE → $DB_FILE"
mysqldump \
    --host="$DB_HOST" \
    --port="$DB_PORT" \
    --user="$DB_USERNAME" \
    --password="$DB_PASSWORD" \
    --single-transaction \
    --routines \
    --triggers \
    --no-tablespaces \
    --add-drop-table \
    "$DB_DATABASE" \
    | gzip > "$DB_FILE"

echo "[backup $TIMESTAMP] tar storage/ → $STORAGE_FILE"
tar -czf "$STORAGE_FILE" \
    --exclude='storage/backups' \
    --exclude='storage/logs/*.log' \
    --exclude='storage/framework/cache/*' \
    --exclude='storage/framework/sessions/*' \
    --exclude='storage/framework/views/*' \
    storage/

# Prune anything older than BACKUP_KEEP days.
find "$BACKUP_DIR" -maxdepth 1 -type f \( -name 'db_*.sql.gz' -o -name 'storage_*.tar.gz' \) -mtime "+$BACKUP_KEEP" -delete

# Optional off-site upload via rclone (if installed + configured).
if [[ -n "${BACKUP_REMOTE:-}" ]] && command -v rclone >/dev/null 2>&1; then
    echo "[backup $TIMESTAMP] rclone copy → $BACKUP_REMOTE"
    rclone copy "$DB_FILE" "$BACKUP_REMOTE/" --quiet
    rclone copy "$STORAGE_FILE" "$BACKUP_REMOTE/" --quiet
fi

echo "[backup $TIMESTAMP] done"
