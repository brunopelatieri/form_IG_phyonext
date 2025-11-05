<?php
require_once '../../config/config.php';
require_once '../SupabaseClient.php';

try {
    $supabase = new SupabaseClient();
    $result = $supabase->select('lead_unqualified', [], '*');
    echo "✅ Conexão OK!\n";
    print_r($result);
} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage();
}