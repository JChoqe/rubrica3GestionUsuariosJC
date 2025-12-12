<?php

// ===== Datos a cifrar =====
$texto = "Hola mundo";
$clave = "mi_clave_secreta_123456789012"; // AES-256 requiere 32 bytes

// ===== Método de cifrado =====
$metodo = "AES-256-CBC";

// ===== Generar un IV (vector de inicialización) =====
$iv_length = openssl_cipher_iv_length($metodo);
$iv = openssl_random_pseudo_bytes($iv_length);

// ===== Cifrar =====
$textoCifrado = openssl_encrypt($texto, $metodo, $clave, 0, $iv);

// Para poder almacenar/transmitir, combinamos el IV con el texto cifrado en base64
$mensajeSeguro = base64_encode($iv . $textoCifrado);
//echo "Mensaje cifrado (base64 + IV): $mensajeSeguro\n";

// ===== Descifrar =====
// Primero, decodificar base64 y separar IV y texto
$datosDecodificados = base64_decode($mensajeSeguro);
$iv_recuperado = substr($datosDecodificados, 0, $iv_length);
$textoCifradoRecuperado = substr($datosDecodificados, $iv_length);

// Descifrar
$textoOriginal = openssl_decrypt($textoCifradoRecuperado, $metodo, $clave, 0, $iv_recuperado);
//echo "Texto descifrado: $textoOriginal\n";


function cifrar($texto): string
{
    $metodo = "AES-256-CBC";
    $clave = "mi_clave_secreta_123456789012";
    $iv_length = openssl_cipher_iv_length($metodo);
    $iv = openssl_random_pseudo_bytes($iv_length); // Generar nuevo IV
    
    $textoCifrado = openssl_encrypt($texto, $metodo, $clave, 0, $iv);
    return base64_encode($iv . $textoCifrado);
}

function descifrar($texto): string
{
    global $metodo;
    global $clave;
    global $iv_length;

    // Primero, decodificar base64 y separar IV y texto
    $datosDecodificados = base64_decode($texto);
    $iv_recuperado = substr($datosDecodificados, 0, $iv_length);
    $textoCifradoRecuperado = substr($datosDecodificados, $iv_length);

    // Descifrar
    $textoOriginal = openssl_decrypt($textoCifradoRecuperado, $metodo, $clave, 0, $iv_recuperado);

    return $textoOriginal;
}
