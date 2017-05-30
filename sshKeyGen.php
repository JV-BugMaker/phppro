<?php
/* Create the private and public key */
$res = openssl_pkey_new();
$privKey = 'MIICXgIBAAKBgQC8tA2AwPPJHblnCicywXDDEIAq2wY4ROi8ABLOHHpSMDYTOwB8
C2NLxMPLD2kOnutm/oebhbXtf8aKTcU8N1GSTb8uTR/UulIeJPgpcQLftxwX3hba
eoOPlzK8Z/63y28Ffd24F7zs9L3tfSRDHwuAVsxuNI0+rU7YZWyzmYP23wIDAQAB
AoGAW4aNQhTUaYjEQ0j2aDTQ55vaPm8LXkF2DLGQbW38ml6N69fjTUcMu1RNjvED
iLbmAIeV6IX7Dp26A5zi/GjsY89qXQj9athOPib99jkeR0aicRgIKYIr8z9jvpWq
9ASyigxMjLrEVGXQ0o+p/Y8OEc3XfCF9IzVZG40l+yym7OECQQDl7cVt9cAlL14A
1qT/QEzThWTNd8zvFcdITRp9Vn71rU3hoApP8zLRDYtR64jY0ojeKe0KP6grTRA1
C8oldaYxAkEA0hmdWq/cNaS3SdAV6oD1N1k0Ti5WnMGqWqW0An/gP4UuKSRbqc3P
F/5OoHpIPjAiDSYmJtJew9GInJtQZ9JaDwJBAM5KENtCJJ14LP/VlH2KhCM2yCTs
ekp7oKtGuiB/7TKgxYJL41St3wbe/wOFrebSpYel2A1c5ZXL82GUbU5EitECQQDD
fboAi+nmsCEruUbrMJr6qTIWHN/SdBFCzzQzrzDFafKNrZrs4Od1d9dJUv6tfrPw
cDLHpK8wnWLz9UBXmk7ZAkEAzXxkxiscb0vdB6nBiN/LM1V0QwIcfa20p+HdD+eE
rLuD8lkoQsF09yVhaNPSEcVnzNWYFDmJNSkU6SA9/JNY7w==';
/* Extract the private key from $res to $privKey */
openssl_pkey_export($res, $privKey);

/* Extract the public key from $res to $pubKey */
$pubKey = openssl_pkey_get_details($res);
$pubKey = $pubKey["key"];

echo $privKey;

echo "\n\n\n";

echo $pubKey;
