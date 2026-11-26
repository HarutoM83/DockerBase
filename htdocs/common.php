<?php
// common.php

// エラーを画面に表示する(開発用)
ini_set('display_errors', 1);
error_reporting(E_ALL);

// セッションを開始
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// データベース接続設定を読み込む
require_once 'db_config.php';

/**
 * データベース接続（PDO）を取得する
 * @return PDO
 */
function getDb(): PDO
{
    global $host, $database, $username, $password;
    $dsn = "mysql:host={$host};dbname={$database};charset=utf8mb4";
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    return $pdo;
}