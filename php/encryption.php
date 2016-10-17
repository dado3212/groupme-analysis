<?php
  // Encryption adapted from 'http://stackoverflow.com/a/30166085/3951475'
  // Requires key length of 32

  function encrypt($message, $key) {
    $nonce = \Sodium\randombytes_buf(\Sodium\CRYPTO_SECRETBOX_NONCEBYTES);

    return base64_encode($nonce . 
      \Sodium\crypto_secretbox(
        $message,
        $nonce,
        $key
      )
    );
  }

  function decrypt($encrypted, $key) {   
    $decoded = base64_decode($encrypted);
    $nonce = mb_substr($decoded, 0, \Sodium\CRYPTO_SECRETBOX_NONCEBYTES, "8bit");
    $ciphertext = mb_substr($decoded, \Sodium\CRYPTO_SECRETBOX_NONCEBYTES, null, "8bit");

    return \Sodium\crypto_secretbox_open(
      $ciphertext,
      $nonce,
      $key
    );
  }
?>