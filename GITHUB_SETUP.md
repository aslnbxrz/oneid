# GitHub Repository Setup Guide

Bu fayl GitHub repoga qo'yish uchun qadamlar va fayllar ro'yxatini o'z ichiga oladi.

## 📁 Repository Structure

```
oneid/
├── .github/
│   ├── workflows/
│   │   └── tests.yml              # GitHub Actions CI/CD
│   ├── ISSUE_TEMPLATE/
│   │   ├── bug_report.md          # Bug report template
│   │   └── feature_request.md     # Feature request template
│   └── pull_request_template.md   # PR template
├── docs/
│   └── ONEID_SYSTEM.md            # OneID system documentation
├── scripts/
│   ├── bump-version.sh            # Version bumping script
│   ├── quick-release.sh           # Quick release script
│   └── README.md                  # Scripts documentation
├── src/
│   ├── Commands/
│   │   └── ValidateOneIDConfigCommand.php
│   ├── Data/
│   │   ├── OneIDAuthResult.php
│   │   └── OneIDLogoutResult.php
│   ├── Facades/
│   │   └── OneID.php
│   ├── Http/
│   │   ├── Controllers/
│   │   │   └── OneIDController.php
│   │   └── Integrations/
│   │       └── OneID/
│   │           ├── OneIDConnector.php
│   │           └── Requests/
│   │               ├── OneIDGetTokenRequest.php
│   │               ├── OneIDHandleRequest.php
│   │               └── OneIDLogoutRequest.php
│   ├── Services/
│   │   └── OneIDValidator.php
│   ├── OneIDManager.php
│   ├── OneIDService.php
│   └── OneIdServiceProvider.php
├── tests/
│   ├── Unit/
│   │   └── OneIDServiceTest.php
│   ├── Pest.php
│   └── phpunit.xml
├── config/
│   └── oneid.php                  # Configuration file
├── routes/
│   └── oneid.php                  # Route definitions
├── .gitignore                     # Git ignore rules
├── composer.json                  # Composer configuration
├── composer.lock                  # Composer lock file
├── Makefile                       # Development commands
├── LICENSE                        # MIT License
├── README.md                      # Main documentation
├── CONTRIBUTING.md                # Contribution guidelines
├── SECURITY.md                    # Security policy
└── GITHUB_SETUP.md                # This file
```

## 🚀 GitHub Repository Setup Steps

### 1. Create GitHub Repository

1. GitHub'da yangi repository yarating:
   - Repository name: `oneid`
   - Description: `Professional OneID integration package for Laravel - Uzbekistan's unified identification system`
   - Visibility: Public
   - Initialize with README: No (bizda allaqachon bor)

### 2. Initialize Git Repository

```bash
cd /Users/aslnbxrz/Desktop/Composer\ Packages/OneID/
git init
git add .
git commit -m "Initial commit: OneID Laravel Package v1.0.0"
```

### 3. Add Remote and Push

```bash
git remote add origin https://github.com/aslnbxrz/oneid.git
git branch -M main
git push -u origin main
```

### 4. Repository Settings

GitHub repository settings'da quyidagilarni sozlang:

#### General Settings:
- Repository name: `oneid`
- Description: `Professional OneID integration package for Laravel - Uzbekistan's unified identification system`
- Website: `https://packagist.org/packages/aslnbxrz/oneid`
- Topics: `laravel`, `oneid`, `uzbekistan`, `authentication`, `oauth`, `sso`, `egov`, `identity`

#### Features:
- ✅ Issues
- ✅ Projects
- ✅ Wiki
- ✅ Discussions

#### Branches:
- Default branch: `main`
- Branch protection rules: Enable for main branch

### 5. Create Release

1. GitHub'da "Releases" bo'limiga o'ting
2. "Create a new release" tugmasini bosing
3. Quyidagi ma'lumotlarni kiriting:
   - Tag version: `v1.0.0`
   - Release title: `OneID Laravel Package v1.0.0`
   - Description: Release notes

### 6. Packagist.org Setup

1. [Packagist.org](https://packagist.org)'ga kiring
2. "Submit" tugmasini bosing
3. Repository URL: `https://github.com/aslnbxrz/oneid`
4. "Check" tugmasini bosing
5. Repository ma'lumotlarini tekshiring
6. "Submit" tugmasini bosing

### 7. GitHub Actions Setup

GitHub Actions avtomatik ravishda ishlaydi, lekin quyidagilarni tekshiring:

1. Repository'da "Actions" bo'limiga o'ting
2. Workflow fayllar ko'rinishini tekshiring
3. Testlarni ishga tushiring

## 📋 Pre-Release Checklist

### Code Quality
- [ ] Barcha testlar o'tadi (`composer test`)
- [ ] Code style tekshiruvlari (`composer lint`)
- [ ] Linter xatolari yo'q
- [ ] Namespace'lar to'g'ri

### Documentation
- [ ] README.md to'liq va aniq
- [ ] Installation instructions
- [ ] Usage examples
- [ ] Configuration guide
- [ ] API documentation

### Configuration
- [ ] composer.json to'g'ri
- [ ] Version 1.0.0
- [ ] Dependencies to'g'ri
- [ ] Scripts ishlaydi

### Security
- [ ] .gitignore to'g'ri
- [ ] Sensitive ma'lumotlar yo'q
- [ ] SECURITY.md mavjud
- [ ] Security best practices

### Testing
- [ ] Unit testlar mavjud
- [ ] Test coverage yaxshi
- [ ] CI/CD sozlanmagan

## 🎯 Post-Release Tasks

### 1. Documentation
- [ ] GitHub Pages sozlash (agar kerak bo'lsa)
- [ ] Wiki'da qo'shimcha ma'lumotlar
- [ ] FAQ qo'shish

### 2. Community
- [ ] GitHub Discussions yoqish
- [ ] Issue templates ishlaydi
- [ ] PR templates ishlaydi

### 3. Monitoring
- [ ] Packagist.org'da repository ko'rinishini tekshirish
- [ ] Download statistikalarini kuzatish
- [ ] Issue va PR'larni tekshirish

## 🔧 Development Commands

Repository'ni klon qilgandan keyin:

```bash
# Dependencies o'rnatish
composer install

# Testlarni ishga tushirish
composer test

# Code style tekshirish
composer lint:test

# Code style tuzatish
composer lint

# Version bumping
make bump-patch
make bump-minor
make bump-major

# Quick release
make release
```

## 📞 Support

Agar muammolar bo'lsa:

1. GitHub Issues yarating
2. Email: [bexruz.aslonov1@gmail.com](mailto:bexruz.aslonov1@gmail.com)
3. Documentation'ni tekshiring

---

**OneID Laravel Package v1.0.0 GitHub repoga tayyor! 🚀**
