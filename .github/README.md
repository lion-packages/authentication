# 🦁 Lion-Authentication

<p align="center">
  <a href="https://dev.lion-packages.com/" target="_blank">
    <img 
        src="https://github.com/lion-packages/framework/assets/56183278/60871c9f-1c93-4481-8c1e-d70282b33254"
        width="450" 
        alt="Lion-Packages Logo"
    >
  </a>
</p>

<p align="center">
  <a href="https://packagist.org/packages/lion/authentication">
    <img src="https://poser.pugx.org/lion/authentication/v" alt="Latest Stable Version">
  </a>
  <a href="https://packagist.org/packages/lion/authentication">
    <img src="https://poser.pugx.org/lion/authentication/downloads" alt="Total Downloads">
  </a>
  <a href="https://github.com/lion-packages/authentication/blob/main/LICENSE">
    <img src="https://poser.pugx.org/lion/authentication/license" alt="License">
  </a>
  <a href="https://www.php.net/">
    <img src="https://poser.pugx.org/lion/authentication/require/php" alt="PHP Version Require">
  </a>
</p>

🚀 **Lion-Authentication** Library to authenticate users with the 2FA method.

---

## 📖 Features

✔️ Generate security codes.  
✔️ Generate QR codes.

---

## 📦 Installation

Install the authentication using **Composer**:

```bash
composer require lion/authentication pragmarx/google2fa-qrcode bacon/bacon-qr-code
```

## Usage Example

```php
use Lion\Authentication\Auth2FA;

$auth2fa = new Auth2FA();

$qr = $auth2fa->qr('company_name', 'company_email', 200); // default size of 400
```

## 📝 License

The <strong>authentication</strong> is open-sourced software licensed under the [MIT License](https://github.com/lion-packages/authentication/blob/main/LICENSE).
