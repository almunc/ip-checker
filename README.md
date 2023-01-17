# ip-checker
PHP library to check if a client is using a VPN/Proxy or Tor exit node. It's also possible to determine users countries or block specific countries.

### Usage
Check the examples folder.

### Additional considerations
Blocking VPN/Proxy and Tor exit nodes will help fight of the cheapest form of botting. There are plenty of sites that offer residential proxies which will bypass these restrictions. Additional tools to increase security would be checking the user agent, requiering JavaScript (aka a browser), captchas, enterprise security solutions like Cloudflare Enterprise and fingerprinting and disallowing too many similar fingerprints within a short period of time.
