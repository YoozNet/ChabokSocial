<h1 align="center"/></h1>
<br/>
<p align="center">
    <a href="#">
        <img src="https://img.shields.io/github/license/YoozNet/ChabokSocial?style=flat-square" />
    </a>
    <a href="https://t.me/ChabokSocial" target="_blank">
        <img src="https://img.shields.io/badge/telegram-group-blue?style=flat-square&logo=telegram" />
    </a>
    <a href="#">
        <img src="https://img.shields.io/github/stars/YoozNet/ChabokSocial?style=social" />
    </a>
</p>

<p align="center">
 <a href="./README.md">
 English
 </a>
 /
 <a href="./README-fa.md">
 فارسی
 </a>
</p>

<p align="center">
  <a href="https://github.com/YoozNet/ChabokSocial/" target="_blank" rel="noopener noreferrer" >
    <img src="https://github.com/YoozNet/ChabokSocial/raw/master/image/dashboard.png" alt="ChaBok screenshots" width="600" height="auto">
  </a>
</p>

# Overview

ChabokSocial is a real-time messaging app built on WebSockets, with a robust Laravel backend and a dynamic React frontend. The UI is styled using Tailwind CSS for a sleek, responsive experience.

# Why using ChabokSocial?
For security enthusiasts, ChabokSocial delivers end-to-end AES-256 encrypted chats with mandatory TOTP 2FA.  
Experience lightning-fast real-time messaging over WebSocket+Redis with sub-100 ms delivery.

# Features

- **Real-time messaging**  
  WebSocket-powered pub/sub over Redis for sub-100 ms delivery and true bi-directional chat.

- **End-to-end encryption**  
  • Per-conversation 32 B AES-256-CBC keys (random IV, Base64)  
  • Master keys derived via Argon2id (`sodium_crypto_pwhash`) from user password + salt  
  • Secure AES-encrypted storage of all keys and payloads

- **Mandatory Two-Factor Authentication**  
  Enforced TOTP login flow; every session requires a 6-digit time-based code.

- **Backup codes**  
  Ten one-time recovery codes, hashed in DB, for account access when TOTP device is unavailable.

- **User presence & status**  
  Real-time online/offline tracking with `last_seen`, broadcast via Laravel Echo.

- **Message operations**  
  • Send, receive, reply, edit (within 72 h), delete  
  • Conversation “clear” to purge history  
  • Pagination and cache of first-page chats

- **Attachments & avatars**  
  • Gz-compressed + AES-encrypted attachments in private storage  
  • AES-decrypted avatars served with strict security headers

- **Profile management**  
  • Secure avatar upload/encryption  
  • 2FA-protected profile updates  
  • Cache-optimized read/write

- **JSON-only, CSRF-safe API**  
  All endpoints accept/reply only JSON (400 on others), mitigating XSRF risk.

- **Distributed Redis caching**  
  • Master keys, chat pages, backup codes, profile data  
  • Automatic invalidation on update

- **Robust validation & error handling**  
  Granular field rules, standardized 4xx/5xx JSON responses, centralized exception guard.

- **Secure key lifecycle**  
  • Random `random_bytes` generation for salts & IVs  
  • Argon2id KDF for GPU-resistance  
  • Cache master keys for up to 40 days

- **Laravel + React + Tailwind CSS stack**  
  • Laravel API with sanctum or passport  
  • React SPA, RTL-compatible, “Vazir” font  
  • Responsive, mobile-first UI with fluid animations

- **Modular, extensible architecture**  
  Clean separation of concerns, easy to customize 2FA, encryption parameters, and caching layers.

## Installation

Follow the installation guide here:  
[https://github.com/YoozNet/ChabokSocial/install.md](./install.md)

---

<p align="center">
  <a href="https://t.me/ChabokSocial" target="_blank">
    <img src="https://img.shields.io/badge/Telegram–Support%20Group-blue?style=flat-square&logo=telegram" alt="Telegram Support" />
  </a>
</p>

Join our Telegram support group for any questions!

Have a feature request or found a bug? Check our [Open Issues](https://github.com/YoozNet/ChabokSocial/issues).  

# Donation

If you found ChabokSocial useful and would like to support its development, you can make a donation in one of the following crypto networks:

- TRON network (TRC20): `TWupWw6TEsJrjfqSEf1smJbrTy3ELRVxom`
- Ethereum network: `0xf5bea36E77e540581455424d3706106e90CAa6ee`
- Bitcoin network: `bc1qa3s40ydtsnlenaw275ehw97czq0fwpadaj0sl9`
- Dogecoin network: `DGT4FqaXRkPC2or5pqbRReXAsqiQq1XH8P`
- TON network: `UQCepi0jPfATD-AHGHtiRY0Iz5Z5-r-KOZ0ED59BCEso6Tts`
- Litecoin network: `ltc1q38ffljlyx9hnrjezuhz0rkclgvpnf7sfj6w3fr`

Thank you for your support!

---

**Special thanks to our developers:**  
YoozNet team and all community contributors for making ChabokSocial possible!  