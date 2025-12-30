# demo

```txt
/ (Root)
├── index.php                <-- The Router
├── assets/                  <-- (Empty or just images, we used CDN for CSS/JS)
└── src/
    ├── layout.php           <-- Main Theme Wrapper
    ├── views/
    │   ├── home.php         <-- Landing Page
    │   └── ctf-hub.php      <-- Mission Control
    └── tools/
        ├── base64.php       <-- Base64 Tool
        ├── steg.php         <-- Steganography Tool
        ├── invert.php       <-- Image Editor
        ├── pcap.php         <-- Network Analyzer
        ├── crypto/
        │   └── caesar.php   <-- Caesar Cipher Challenge
        └── ctf/
            ├── base64.php
            ├── metadata.php
            ├── password.php
            ├── redirect.php
            ├── ports.php
            ├── xss.php
            └── md5.php
```