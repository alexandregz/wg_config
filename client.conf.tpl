[Interface]
Address = $_VPN_IP
PrivateKey = $_PRIVATE_KEY

[Peer]
PublicKey = $_SERVER_PUBLIC_KEY
AllowedIPs = 192.168.66.0/24, 192.168.69.0/24
Endpoint = $_SERVER_LISTEN
