# Contributing to OneID Laravel Package

Thank you for your interest in contributing to the OneID Laravel package! This document provides guidelines for contributing to the project.

## Code of Conduct

This project adheres to a code of conduct. By participating, you are expected to uphold this code. Please report unacceptable behavior to [bexruz.aslonov1@gmail.com](mailto:bexruz.aslonov1@gmail.com).

## How to Contribute

### Reporting Bugs

Before creating bug reports, please check existing issues to avoid duplicates. When you create a bug report, please include as many details as possible:

- Use a clear and descriptive title
- Describe the exact steps to reproduce the problem
- Provide specific examples to demonstrate the steps
- Describe the behavior you observed and what behavior you expected
- Include your environment details (PHP version, Laravel version, etc.)

### Suggesting Enhancements

Enhancement suggestions are tracked as GitHub issues. When creating an enhancement suggestion, please include:

- Use a clear and descriptive title
- Provide a step-by-step description of the suggested enhancement
- Provide specific examples to demonstrate the steps
- Describe the current behavior and explain which behavior you expected
- Explain why this enhancement would be useful

### Pull Requests

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/amazing-feature`)
3. Make your changes
4. Add tests for your changes
5. Ensure all tests pass (`composer test`)
6. Ensure code style is correct (`composer lint`)
7. Commit your changes (`git commit -m 'Add some amazing feature'`)
8. Push to the branch (`git push origin feature/amazing-feature`)
9. Open a Pull Request

## Development Setup

### Prerequisites

- PHP 8.2 or higher
- Composer
- Git

### Installation

1. Clone the repository:
```bash
git clone https://github.com/aslnbxrz/oneid.git
cd oneid
```

2. Install dependencies:
```bash
composer install
```

3. Copy the test environment file:
```bash
cp .env.example .env.testing
```

### Running Tests

```bash
# Run all tests
composer test

# Run tests with coverage
composer test:coverage

# Run specific test suites
composer test:unit
composer test:feature
```

### Code Style

This project uses Laravel Pint for code style enforcement:

```bash
# Check code style
composer lint:test

# Fix code style issues
composer lint
```

## Coding Standards

### PHP

- Follow PSR-12 coding standard
- Use type hints where possible
- Write descriptive variable and method names
- Add docblocks for all public methods
- Keep methods small and focused

### Laravel

- Follow Laravel conventions
- Use Laravel's built-in features where appropriate
- Follow the repository pattern for data access
- Use form requests for validation
- Use resources for API responses

### Testing

- Write tests for all new features
- Aim for high test coverage
- Use descriptive test names
- Test both happy path and edge cases
- Mock external dependencies

## Commit Messages

Use clear and descriptive commit messages:

```
feat: add OneID logout functionality
fix: resolve token validation issue
docs: update installation instructions
test: add unit tests for validation service
```

## Pull Request Guidelines

### Before Submitting

- [ ] Ensure all tests pass
- [ ] Run code style checks
- [ ] Update documentation if needed
- [ ] Add tests for new functionality
- [ ] Update CHANGELOG.md if applicable

### Pull Request Description

Please include:

- Description of changes
- Type of change (bug fix, feature, etc.)
- Related issues
- Testing performed
- Screenshots (if applicable)

## Release Process

Releases are managed through GitHub releases. The process involves:

1. Update version numbers
2. Update CHANGELOG.md
3. Create a release tag
4. Generate release notes

## Questions?

If you have questions about contributing, please:

- Check existing issues and discussions
- Create a new issue with the "question" label
- Contact the maintainer: [bexruz.aslonov1@gmail.com](mailto:bexruz.aslonov1@gmail.com)

## Thank You

Thank you for contributing to the OneID Laravel package! Your contributions help make this project better for everyone.
