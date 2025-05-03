# Contributing

Thank you for considering contributing to the iSend SMS Laravel package! This document outlines the guidelines for contributing to this project.

## Code of Conduct

This project adheres to a standard code of conduct. By participating, you are expected to uphold this code. Please report unacceptable behavior to [soliman.benhalim@gmail.com](mailto:soliman.benhalim@gmail.com).

## Pull Requests

1. Fork the repository
2. Create a branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add some amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## Development Setup

1. Clone your fork of the repository
2. Install dependencies:
   ```bash
   composer install
   ```
3. Run tests to ensure everything is working:
   ```bash
   composer test
   ```

## Coding Standards

This package follows the PSR-12 coding standard and the PSR-4 autoloading standard.

You can check the code style with:
```bash
composer format
```

And analyze the code with:
```bash
composer analyse
```

## Testing

The package includes a test suite that you can run with:
```bash
composer test
```

Please make sure your code passes all tests, and add new tests for new features.

## Documentation

If your changes include functionality that should be documented, please update the README.md file.

## Version Control

- Follow [Semantic Versioning](https://semver.org/)
- Use descriptive commit messages
- Reference issues and pull requests in commit messages when applicable

Thank you for your contributions!