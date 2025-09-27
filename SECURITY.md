# Security Policy

## Supported Versions

We release patches for security vulnerabilities in the following versions:

| Version | Supported          |
| ------- | ------------------ |
| 1.0.x   | :white_check_mark: |

## Reporting a Vulnerability

If you discover a security vulnerability within this package, please send an email to [bexruz.aslonov1@gmail.com](mailto:bexruz.aslonov1@gmail.com). All security vulnerabilities will be promptly addressed.

Please do not create public GitHub issues for security vulnerabilities.

### What to Include

When reporting a security vulnerability, please include:

- Description of the vulnerability
- Steps to reproduce the issue
- Potential impact
- Suggested fix (if any)
- Your contact information

### Response Timeline

- We will acknowledge receipt of your report within 48 hours
- We will provide regular updates on our progress
- We will notify you when the issue has been resolved

### Responsible Disclosure

We follow responsible disclosure practices:

1. **Do not** disclose the vulnerability publicly until we have had a chance to address it
2. **Do not** use the vulnerability for malicious purposes
3. Give us reasonable time to fix the issue before public disclosure

## Security Best Practices

### For Users

- Always use HTTPS in production
- Keep your OneID credentials secure
- Regularly update the package to the latest version
- Use environment variables for sensitive configuration
- Implement proper error handling and logging
- Validate all user inputs

### For Developers

- Never commit sensitive data to version control
- Use proper authentication and authorization
- Implement rate limiting
- Use secure HTTP client configurations
- Follow OWASP security guidelines
- Regular security audits

## Security Features

This package includes several security features:

- **CSRF Protection**: Built-in CSRF token validation
- **Input Validation**: Comprehensive input validation
- **Secure Logging**: Sensitive data protection in logs
- **Rate Limiting**: Configurable rate limiting
- **SSL Verification**: SSL certificate verification
- **Token Validation**: Access token validation

## Updates

We recommend:

- Regularly updating to the latest version
- Monitoring security advisories
- Implementing security patches promptly
- Following Laravel security best practices

## Contact

For security-related questions or concerns, please contact:

- Email: [bexruz.aslonov1@gmail.com](mailto:bexruz.aslonov1@gmail.com)
- GitHub Issues: Use private reporting for security issues

Thank you for helping keep this project secure!
