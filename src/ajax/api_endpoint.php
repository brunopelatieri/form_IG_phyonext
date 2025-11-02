<?php
/**
 * @author Bruno Pelatieri Goulart
 * @version 1.0.0
 * @date 2025-11-01
 * @license MIT
 * @package ApiEndpoint
 * 
 * API Endpoint para formulário de leads
 * 
 * Recebe requisições AJAX e insere dados no Supabase
 * - Campos individuais -> lead_unqualified
 * - Submit completo -> lead_qualified
 */

require_once '../../config/config.php';
require_once '../SupabaseClient.php';

// Força resposta JSON
header('Content-Type: application/json; charset=utf-8');

/**
 * Função para enviar resposta JSON
 */
function sendResponse($success, $message, $data = null, $httpCode = 200) {
    http_response_code($httpCode);
    echo json_encode([
        'success' => $success,
        'message' => $message,
        'data' => $data,
        'timestamp' => date('Y-m-d H:i:s')
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

/**
 * Função para registrar log
 */
function logRequest($data) {
    if (ENABLE_LOGS) {
        $logDir = dirname(LOG_FILE);
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }
        
        $timestamp = date('Y-m-d H:i:s');
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $logMessage = "[{$timestamp}] IP: {$ip} - " . json_encode($data, JSON_UNESCAPED_UNICODE) . "\n";
        file_put_contents(LOG_FILE, $logMessage, FILE_APPEND);
    }
}

// Verifica se é POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendResponse(false, 'Método não permitido. Use POST.', null, 405);
}

// Verifica Content-Type
$contentType = $_SERVER['CONTENT_TYPE'] ?? '';
if (strpos($contentType, 'application/json') === false && 
    strpos($contentType, 'application/x-www-form-urlencoded') === false) {
    sendResponse(false, 'Content-Type inválido.', null, 400);
}

try {
    // Recebe dados
    $rawData = file_get_contents('php://input');
    
    // Tenta decodificar JSON primeiro
    $postData = json_decode($rawData, true);
    
    // Se não for JSON, tenta como form-data
    if (json_last_error() !== JSON_ERROR_NONE) {
        parse_str($rawData, $postData);
    }
    
    // Se ainda estiver vazio, usa $_POST
    if (empty($postData)) {
        $postData = $_POST;
    }
    
    // Log da requisição
    logRequest(['method' => 'POST', 'data' => $postData]);
    
    // Sanitiza dados
    $postData = SupabaseClient::sanitize($postData);
    
    // Verifica se tem o parâmetro de controle
    $controleU = $postData['controle'] ?? $postData['u'] ?? null;
    
    if (empty($controleU)) {
        sendResponse(false, 'Parâmetro de controle (u) é obrigatório.', null, 400);
    }
    
    // Inicializa cliente Supabase
    $supabase = new SupabaseClient();
    
    // Identifica o tipo de requisição
    $tipo = $postData['tipo'] ?? 'campo';
    
    // ==========================================
    // REQUISIÇÃO DE CAMPO INDIVIDUAL
    // ==========================================
    if ($tipo === 'campo' || isset($postData['campo'])) {
        $campo = $postData['campo'] ?? null;
        $valor = $postData['valor'] ?? null;
        
        if (empty($campo) || $valor === null || $valor === '') {
            sendResponse(false, 'Campo e valor são obrigatórios.', null, 400);
        }
        
        // Validações específicas por campo
        switch ($campo) {
            case 'nome':
                if (strlen($valor) < 3) {
                    sendResponse(false, 'Nome deve ter pelo menos 3 caracteres.', null, 400);
                }
                break;
                
            case 'whatsapp':
                if (!SupabaseClient::validateWhatsApp($valor)) {
                    sendResponse(false, 'WhatsApp inválido.', null, 400);
                }
                break;
                
            case 'email':
                if (!SupabaseClient::validateEmail($valor)) {
                    sendResponse(false, 'E-mail inválido.', null, 400);
                }
                break;
                
            case 'site':
                // Site é opcional, não valida se vazio
                if (!empty($valor) && !filter_var($valor, FILTER_VALIDATE_URL)) {
                    sendResponse(false, 'URL inválida.', null, 400);
                }
                break;
                
            case 'faturamento':
                // Valida se é uma das opções válidas
                $opcoesValidas = [
                    'Até R$100k/ano',
                    'R$100K a R$500k/ano',
                    'R$500K a R$1M/ano',
                    'R$1M a R$5M/ano',
                    '+R$10M/ano'
                ];
                
                if (!in_array($valor, $opcoesValidas)) {
                    sendResponse(false, 'Opção de faturamento inválida.', null, 400);
                }
                break;
        }
        
        // Verifica se já existe registro para este controleU
        $existente = $supabase->select('lead_unqualified', ['controle_u' => $controleU]);
        
        if (!empty($existente['data'])) {
            // Atualiza registro existente
            $updateData = [
                $campo => $valor,
                'updated_at' => date('Y-m-d H:i:s')
            ];
            
            $result = $supabase->update('lead_unqualified', $updateData, ['controle_u' => $controleU]);
            
            sendResponse(true, "Campo '{$campo}' atualizado com sucesso.", [
                'campo' => $campo,
                'controle_u' => $controleU,
                'acao' => 'update'
            ]);
        } else {
            // Cria novo registro
            $insertData = [
                'controle_u' => $controleU,
                $campo => $valor,
                'ip' => $_SERVER['REMOTE_ADDR'] ?? null,
                'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];
            
            $result = $supabase->insert('lead_unqualified', $insertData);
            
            sendResponse(true, "Campo '{$campo}' salvo com sucesso.", [
                'campo' => $campo,
                'controle_u' => $controleU,
                'acao' => 'insert'
            ]);
        }
    }
    
    // ==========================================
    // REQUISIÇÃO DE SUBMIT COMPLETO
    // ==========================================
    else {
        // Valida campos obrigatórios
        $camposObrigatorios = ['nome', 'whatsapp', 'email', 'faturamento'];
        $camposFaltando = [];
        
        foreach ($camposObrigatorios as $campoObrigatorio) {
            if (empty($postData[$campoObrigatorio])) {
                $camposFaltando[] = $campoObrigatorio;
            }
        }
        
        if (!empty($camposFaltando)) {
            sendResponse(false, 'Campos obrigatórios faltando: ' . implode(', ', $camposFaltando), null, 400);
        }
        
        // Validações
        if (strlen($postData['nome']) < 3) {
            sendResponse(false, 'Nome deve ter pelo menos 3 caracteres.', null, 400);
        }
        
        if (!SupabaseClient::validateWhatsApp($postData['whatsapp'])) {
            sendResponse(false, 'WhatsApp inválido.', null, 400);
        }
        
        if (!SupabaseClient::validateEmail($postData['email'])) {
            sendResponse(false, 'E-mail inválido.', null, 400);
        }
        
        // Valida site se fornecido
        if (!empty($postData['site']) && !filter_var($postData['site'], FILTER_VALIDATE_URL)) {
            sendResponse(false, 'URL inválida.', null, 400);
        }
        
        // Prepara dados para inserção
        $leadQualified = [
            'controle_u' => $controleU,
            'nome' => $postData['nome'],
            'whatsapp' => $postData['whatsapp'],
            'email' => $postData['email'],
            'site' => $postData['site'] ?? null,
            'faturamento' => $postData['faturamento'],
            'ip' => $_SERVER['REMOTE_ADDR'] ?? null,
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];
        
        // Insere na tabela lead_qualified
        $result = $supabase->insert('lead_qualified', $leadQualified);

        // Delete da tabela lead_unqualified
        $supabase->delete('lead_unqualified', ['controle_u' => $controleU]);
        
        sendResponse(true, 'Lead qualificado salvo com sucesso!', [
            'controle_u' => $controleU,
            'email' => $postData['email']
        ], 201);
    }
    
} catch (Exception $e) {
    // Log do erro
    logRequest(['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
    
    // Mensagem amigável em produção
    $errorMessage = ENVIRONMENT === 'development' 
        ? $e->getMessage() 
        : 'Erro ao processar requisição. Tente novamente.';
    
    sendResponse(false, $errorMessage, null, 500);
}
