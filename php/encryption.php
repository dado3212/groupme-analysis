<?php
  // Encryption adapted from 'http://stackoverflow.com/a/30166085/3951475'
  // Requires key length of 32

  function encrypt($message, $key) {
    $nonce = random_bytes(SODIUM_CRYPTO_SECRETBOX_NONCEBYTES);

    return base64_encode($nonce . 
      sodium_crypto_secretbox(
        $message,
        $nonce,
        $key
      )
    );
  }

  function decrypt($encrypted, $key) {   
    $decoded = base64_decode($encrypted);
    $nonce = mb_substr($decoded, 0, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES, "8bit");
    $ciphertext = mb_substr($decoded, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES, null, "8bit");

    return sodium_crypto_secretbox_open(
      $ciphertext,
      $nonce,
      $key
    );
  }
?>