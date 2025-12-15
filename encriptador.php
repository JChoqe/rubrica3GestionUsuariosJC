<?php

// Parámetros de cifrado
$metodo = "AES-256-CBC";
$clave  = "mi_clave_secreta_123456789012"; // 32 bytes para AES-256

// Longitud del IV según el método
$iv_length = openssl_cipher_iv_length($metodo);

/**
 * Cifra un texto y devuelve IV + texto cifrado en base64
 */
function cifrar($texto): string
{
    global $metodo, $clave, $iv_length;

    // Genera un IV nuevo por cada cifrado
    $iv = openssl_random_pseudo_bytes($iv_length);

    $textoCifrado  = openssl_encrypt($texto, $metodo, $clave, 0, $iv);
    $mensajeSeguro = base64_encode($iv . $textoCifrado);

    return $mensajeSeguro;
}

/**
 * Descifra un texto en base64 que contiene IV + texto cifrado
 */
function descifrar($texto): string
{
    global $metodo, $clave, $iv_length;

    // Decodifica y separa IV y texto cifrado
    $datosDecodificados      = base64_decode($texto);
    $iv_recuperado           = substr($datosDecodificados, 0, $iv_length);
    $textoCifradoRecuperado  = substr($datosDecodificados, $iv_length);

    return openssl_decrypt(
        $textoCifradoRecuperado,
        $metodo,
        $clave,
        0,
        $iv_recuperado
    );
}
