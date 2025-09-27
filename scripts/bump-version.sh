#!/usr/bin/env bash
# Usage: ./scripts/bump-version.sh [patch|minor|major] [--auto-fix|--allow-dirty]
set -euo pipefail

RED='\033[0;31m'; GREEN='\033[0;32m'; YELLOW='\033[1;33m'; BLUE='\033[0;34m'; NC='\033[0m'
info()    { echo -e "${BLUE}[INFO]${NC} $*"; }
success() { echo -e "${GREEN}[SUCCESS]${NC} $*"; }
warn()    { echo -e "${YELLOW}[WARN]${NC} $*"; }
error()   { echo -e "${RED}[ERROR]${NC} $*"; }

BUMP_TYPE="${1:-patch}"
FLAG="${2:-}"
if [[ ! "$BUMP_TYPE" =~ ^(patch|minor|major)$ ]]; then
  error "Invalid bump type. Use: patch|minor|major"
  exit 1
fi

AUTO_FIX=false
ALLOW_DIRTY=false
[[ "$FLAG" == "--auto-fix" ]] && AUTO_FIX=true
[[ "$FLAG" == "--allow-dirty" ]] && ALLOW_DIRTY=true

CURRENT_BRANCH="$(git rev-parse --abbrev-ref HEAD)"
if [[ "$CURRENT_BRANCH" != "main" && "$CURRENT_BRANCH" != "master" ]]; then
  warn "Releasing from branch '$CURRENT_BRANCH'. Usually main/master."
fi

is_dirty() { [[ -n "$(git status --porcelain)" ]]; }

if is_dirty; then
  if $AUTO_FIX; then
    info "Workspace dirty → running Pint format..."
    composer format || true
    git add -A || true
    if is_dirty; then
      warn "Non-format changes detected → committing them too."
      git commit -m "chore: pre-release: auto-fix & tidy" || true
    else
      git commit -m "style: pint format" || true
    fi
  elif ! $ALLOW_DIRTY; then
    error "Working tree not clean. Use --auto-fix or commit/stash manually."
    exit 1
  fi
fi

info "Pulling latest with tags..."
git pull --rebase --tags

info "QA gate (lint + tests)..."
composer update -W --prefer-dist --no-progress
composer lint:test
composer test

HAS_VERSION_KEY="$(grep -E '^\s*\"version\"\s*:\s*\"' composer.json || true)"
if [[ -n "$HAS_VERSION_KEY" ]]; then
  CURRENT_VERSION="$(php -r '$c=json_decode(file_get_contents("composer.json"),true); echo $c["version"]??"";')"
  [[ -z "$CURRENT_VERSION" ]] && { error "composer.json version key empty."; exit 1; }
else
  CURRENT_VERSION="$(git tag --list 'v*' --sort=-v:refname | head -n1 | sed 's/^v//')"
  [[ -z "$CURRENT_VERSION" ]] && CURRENT_VERSION="0.1.0" && warn "No tags found. Starting from $CURRENT_VERSION"
fi

info "Current version: $CURRENT_VERSION"
IFS='.' read -r MAJOR MINOR PATCH <<< "$CURRENT_VERSION"
case "$BUMP_TYPE" in
  patch) PATCH=$((PATCH+1));;
  minor) MINOR=$((MINOR+1)); PATCH=0;;
  major) MAJOR=$((MAJOR+1)); MINOR=0; PATCH=0;;
esac
NEW_VERSION="${MAJOR}.${MINOR}.${PATCH}"
info "New version: $NEW_VERSION"

if [[ -n "$HAS_VERSION_KEY" ]]; then
  info "Updating composer.json version..."
  php -r '
    $f="composer.json";
    $c=json_decode(file_get_contents($f),true);
    $c["version"]=getenv("NEWV");
    file_put_contents($f,json_encode($c,JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES).PHP_EOL);
  ' NEWV="$NEW_VERSION"
  git add composer.json
  git commit -m "v${NEW_VERSION}: Version bump (${BUMP_TYPE})" || true
fi

TAG="v${NEW_VERSION}"
info "Creating tag ${TAG}..."
git tag -a "${TAG}" -m "Release ${TAG}"

info "Pushing branch & tags..."
git push origin "${CURRENT_BRANCH}" --tags

success "Released ${TAG} 🎉"