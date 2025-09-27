#!/bin/sh
# Usage: ./scripts/bump-version.sh [patch|minor|major] [--auto-fix|--allow-dirty]
# POSIX sh compatible; no bashisms.

set -eu

blue()  { printf "\033[0;34m[INFO]\033[0m %s\n" "$1"; }
green() { printf "\033[0;32m[SUCCESS]\033[0m %s\n" "$1"; }
yellow(){ printf "\033[1;33m[WARN]\033[0m %s\n" "$1"; }
red()   { printf "\033[0;31m[ERROR]\033[0m %s\n" "$1"; }

BUMP_TYPE="${1:-patch}"
FLAG="${2:-}"

case "$BUMP_TYPE" in
  patch|minor|major) ;;
  *) red "Use: patch|minor|major"; exit 1;;
esac

AUTO_FIX=false
ALLOW_DIRTY=false
[ "$FLAG" = "--auto-fix" ] && AUTO_FIX=true
[ "$FLAG" = "--allow-dirty" ] && ALLOW_DIRTY=true

CURRENT_BRANCH="$(git rev-parse --abbrev-ref HEAD)"
if [ "$CURRENT_BRANCH" != "main" ] && [ "$CURRENT_BRANCH" != "master" ]; then
  yellow "Releasing from '$CURRENT_BRANCH' (usually main/master)"
fi

is_dirty() {
  [ -n "$(git status --porcelain)" ]
}

if is_dirty; then
  if [ "$AUTO_FIX" = true ]; then
    blue "Workspace dirty → running vendor/bin/pint..."
    if [ -x "./vendor/bin/pint" ]; then
      ./vendor/bin/pint || true
    else
      composer install --prefer-dist --no-progress
      ./vendor/bin/pint || true
    fi
    git add -A || true
    if is_dirty; then
      yellow "Non-format changes present → committing..."
      git commit -m "chore: pre-release: auto-fix & tidy" || true
    else
      git commit -m "style: pint format" || true
    fi
  elif [ "$ALLOW_DIRTY" != true ]; then
    red "Working tree not clean. Use --auto-fix or commit/stash."
    exit 1
  fi
fi

blue "Pulling latest with tags..."
git pull --rebase --tags

blue "QA gate (validate + lint + tests)..."
composer validate --strict
composer update -W --prefer-dist --no-progress
./vendor/bin/pint --test
./vendor/bin/pest

# Version from latest git tag (vX.Y.Z). If none, start 0.1.0
CURRENT_VERSION="$(git tag --list 'v*' --sort=-v:refname | head -n1 | sed 's/^v//')"
if [ -z "${CURRENT_VERSION}" ]; then
  CURRENT_VERSION="0.1.0"
  yellow "No tags found. Starting from ${CURRENT_VERSION}"
fi

blue "Current version: ${CURRENT_VERSION}"

MAJOR=$(printf "%s" "$CURRENT_VERSION" | cut -d. -f1)
MINOR=$(printf "%s" "$CURRENT_VERSION" | cut -d. -f2)
PATCH=$(printf "%s" "$CURRENT_VERSION" | cut -d. -f3)

case "$BUMP_TYPE" in
  patch) PATCH=$((PATCH + 1));;
  minor) MINOR=$((MINOR + 1)); PATCH=0;;
  major) MAJOR=$((MAJOR + 1)); MINOR=0; PATCH=0;;
esac

NEW_VERSION="${MAJOR}.${MINOR}.${PATCH}"
blue "New version: ${NEW_VERSION}"

TAG="v${NEW_VERSION}"
blue "Creating tag ${TAG}..."
git tag -a "${TAG}" -m "Release ${TAG}"

blue "Pushing branch & tags..."
git push origin "${CURRENT_BRANCH}" --tags

green "Released ${TAG} 🎉"