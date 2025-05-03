# Security Policy

## Supported Versions

| Version | Supported          |
| ------- | ------------------ |
| 1.x.x   | :white_check_mark: |

## Reporting a Vulnerability

If you discover a security vulnerability within this package, please send an e-mail to Suliman Benhalim at [soliman.benhalim@gmail.com](mailto:soliman.benhalim@gmail.com). All security vulnerabilities will be promptly addressed.

### Process

1. Your report will be acknowledged within 48 hours.
2. We will confirm the vulnerability and determine its impact.
3. We will release a patch as soon as possible, depending on complexity.

Please do not report security vulnerabilities through public GitHub issues.

## Security Best Practices

When using this package:

1. Always keep your API token secure and never expose it in client-side code.
2. Use environment variables for storing sensitive configuration.
3. Implement proper access controls for SMS sending functionality.
4. Log SMS sending activity for audit purposes.
5. Consider rate limiting SMS sending operations to prevent abuse.

## Third-Party Services

This package interacts with iSend SMS API. Please be aware that:

1. Data sent via the API (phone numbers, message content) will pass through iSend's servers.
2. iSend has its own privacy policy and security measures.
3. You are responsible for ensuring your usage of the API complies with relevant data protection regulations.

Thank you for helping keep this package secure!