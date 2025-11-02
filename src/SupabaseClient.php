<?php
/**
 * @author Bruno Pelatieri Goulart
 * @version 1.0.0
 * @date 2025-11-01
 * @license MIT
 * @package SupabaseClient
 * 
 * Classe SupabaseClient
 * 
 * Cliente PHP para interação com API REST do Supabase
 * Implementa operações CRUD e gerenciamento de erros
 */

class SupabaseClient {
    private $url;
    private $key;
    private $headers;
    
    /**
     * Construtor
     */
    public function __construct() {
        $this->url = rtrim(SUPABASE_URL, '/');
        $this->key = SUPABASE_KEY;
        
        $this->headers = [
            'apikey: ' . $this->key,
            'Authorization: Bearer ' . $this->key,
            'Content-Type: application/json',
            'Prefer: return=representation'
        ];
    }
    
    /**
     * Insere dados em uma tabela
     * 
     * @param string $table Nome da tabela
     * @param array $data Dados a serem inseridos
     * @return array Resposta da API
     */
    public function insert($table, $data) {
        $endpoint = "{$this->url}/rest/v1/{$table}";
        
        // Validação básica
        if (empty($data)) {
            throw new Exception('Dados vazios não podem ser inseridos');
        }
        
        return $this->request('POST', $endpoint, $data);
    }
    
    /**
     * Atualiza dados em uma tabela
     * 
     * @param string $table Nome da tabela
     * @param array $data Dados a serem atualizados
     * @param array $filters Filtros para a atualização
     * @return array Resposta da API
     */
    public function update($table, $data, $filters) {
        $endpoint = "{$this->url}/rest/v1/{$table}";
        
        // Adiciona filtros à URL
        if (!empty($filters)) {
            $queryParams = [];
            foreach ($filters as $key => $value) {
                $queryParams[] = "{$key}=eq.{$value}";
            }
            $endpoint .= '?' . implode('&', $queryParams);
        }
        
        return $this->request('PATCH', $endpoint, $data);
    }

    /**
     * Atualiza dados em uma tabela
     * 
     * @param string $table Nome da tabela
     * @param array $data Dados a serem atualizados
     * @param array $filters Filtros para a atualização
     * @return array Resposta da API
     */
    public function delete($table, $filters) {
        $endpoint = "{$this->url}/rest/v1/{$table}";
        
        // Adiciona filtros à URL
        if (!empty($filters)) {
            $queryParams = [];
            foreach ($filters as $key => $value) {
                $queryParams[] = "{$key}=eq.{$value}";
            }
            $endpoint .= '?' . implode('&', $queryParams);
        }
        
        return $this->request('DELETE', $endpoint);
    }
    
    /**
     * Busca dados em uma tabela
     * 
     * @param string $table Nome da tabela
     * @param array $filters Filtros opcionais
     * @param string $select Colunas a selecionar (padrão: *)
     * @return array Resposta da API
     */
    public function select($table, $filters = [], $select = '*') {
        $endpoint = "{$this->url}/rest/v1/{$table}?select={$select}";
        
        // Adiciona filtros
        if (!empty($filters)) {
            foreach ($filters as $key => $value) {
                $endpoint .= "&{$key}=eq.{$value}";
            }
        }
        
        return $this->request('GET', $endpoint);
    }
    
    /**
     * Faz requisição HTTP para a API do Supabase
     * 
     * @param string $method Método HTTP
     * @param string $url URL da requisição
     * @param array $data Dados (opcional)
     * @return array Resposta da API
     */
    private function request($method, $url, $data = null) {
        $ch = curl_init();
        
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        
        if ($data !== null && in_array($method, ['POST', 'PATCH', 'PUT'])) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }
        
        // Timeout
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        
        curl_close($ch);
        
        // Tratamento de erros
        if ($error) {
            $this->logError("CURL Error: {$error}");
            throw new Exception("Erro de conexão: {$error}");
        }
        
        $responseData = json_decode($response, true);
        
        // Verifica código HTTP
        if ($httpCode < 200 || $httpCode >= 300) {
            $errorMsg = $responseData['message'] ?? $responseData['error'] ?? 'Erro desconhecido';
            $this->logError("HTTP {$httpCode}: {$errorMsg}");
            throw new Exception("Erro na API (HTTP {$httpCode}): {$errorMsg}");
        }
        
        return [
            'success' => true,
            'data' => $responseData,
            'http_code' => $httpCode
        ];
    }
    
    /**
     * Registra erros em log
     * 
     * @param string $message Mensagem de erro
     */
    private function logError($message) {
        if (ENABLE_LOGS) {
            $logDir = dirname(LOG_FILE);
            if (!is_dir($logDir)) {
                mkdir($logDir, 0755, true);
            }
            
            $timestamp = date('Y-m-d H:i:s');
            $logMessage = "[{$timestamp}] {$message}\n";
            file_put_contents(LOG_FILE, $logMessage, FILE_APPEND);
        }
    }
    
    /**
     * Sanitiza dados de entrada
     * 
     * @param mixed $data Dados a serem sanitizados
     * @return mixed Dados sanitizados
     */
    public static function sanitize($data) {
        if (is_array($data)) {
            return array_map([self::class, 'sanitize'], $data);
        }
        
        if (is_string($data)) {
            return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
        }
        
        return $data;
    }
    
    /**
     * Valida formato de email
     * 
     * @param string $email Email a ser validado
     * @return bool
     */
    public static function validateEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
    
    /**
     * Valida formato de WhatsApp brasileiro
     * 
     * @param string $whatsapp WhatsApp a ser validado
     * @return bool
     */
    public static function validateWhatsApp($whatsapp) {
        $numero = preg_replace('/\D/', '', $whatsapp);
        return strlen($numero) === 11 && $numero[2] === '9';
    }
}
