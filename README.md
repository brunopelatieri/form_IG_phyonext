# 🚀 Sistema de Captura de Leads com PHP e Supabase

Sistema completo de formulário responsivo com captura progressiva de leads e integração com Supabase.
Ideal para ser usado em automação com Instagram que envia o link do formulário para o Direct Message.
Esse com tema azul tecnologia Phyonext.

Demo: https://phyonex.com/material/form.html

## 📋 Índice

1. [Arquitetura do Sistema](#arquitetura-do-sistema)
2. [Requisitos](#requisitos)
3. [Instalação](#instalação)
4. [Configuração do Supabase](#configuração-do-supabase)
5. [Configuração do PHP](#configuração-do-php)
6. [Estrutura de Arquivos](#estrutura-de-arquivos)
7. [Funcionamento](#funcionamento)
8. [Segurança](#segurança)
9. [Troubleshooting](#troubleshooting)

---

## 🏗️ Arquitetura do Sistema

```
┌─────────────┐       ┌──────────────┐       ┌─────────────────┐
│  Formulário │──────▶│   api.php    │──────▶│    Supabase     │
│    HTML     │ AJAX  │ (Backend PHP)│  REST │   PostgreSQL    │
└─────────────┘       └──────────────┘       └─────────────────┘
                              │
                              ▼
                      ┌──────────────┐
                      │   Tabelas:   │
                      │ - unqualified│
                      │ - qualified  │
                      └──────────────┘
```

### Fluxo de Dados:

1. **Campo Individual**: A cada blur → INSERT/UPDATE em `lead_unqualified`
2. **Submit Final**: Ao enviar → INSERT em `lead_qualified` e DELETE em `lead_unqualified`

---

## 💻 Requisitos

### Servidor:
- PHP 7.4 ou superior
- Extensões PHP: `curl`, `json`, `mbstring`
- Apache ou Nginx
- SSL/HTTPS (recomendado para produção)

### Supabase:
- Conta no Supabase (gratuita ou paga)
- Projeto criado
- API Key e URL do projeto

---

## 📦 Instalação

### 1. Estrutura de Diretórios

Crie a seguinte estrutura no seu servidor:

```
/seu-projeto/
├── assets/                         # Diretório dos arquivos estáticos (CSS, JS)
│   └── css/
│       └── style.css               # CSS
│   └── js/
│       └── script.js               # Js javascript jquery
├── config/                         # Diretório de configurações
│   └── config.php                  # Configurações
│   └── logs/                       # Diretório de logs (criar com permissões)
│       └── api.log                 # logs (criar com permissões)
├── SQL_DLL/                        # Diretório SQL DDL
│   └── DDL_supabase_tables.sql     # Arquivo SQL DDL
├── src/                            # Diretório APP
│   └── SupabaseClient.php          # Classe de conexão
├── form.html                       # Formulário HTML
└── .htaccess                       # Configurações Apache (opcional)
```

### 2. Configurar Permissões

```bash
# Criar diretório de logs
mkdir logs
chmod 755 logs

# Dar permissão de escrita (se necessário)
chmod 666 logs/api.log
```

### 3. Arquivo .htaccess (Apache)

Crie um arquivo `.htaccess` na raiz do projeto:

```apache
# Proteção de arquivos sensíveis
<Files "config.php">
    Order allow,deny
    Deny from all
</Files>

<Files "SupabaseClient.php">
    Order allow,deny
    Deny from all
</Files>

# Habilitar CORS (se necessário)
Header set Access-Control-Allow-Origin "*"
Header set Access-Control-Allow-Methods "GET, POST, OPTIONS"
Header set Access-Control-Allow-Headers "Content-Type, Authorization"

# Bloquear acesso direto aos logs
<Files "*.log">
    Order allow,deny
    Deny from all
</Files>
```

---

## 🔧 Configuração do Supabase

### Passo 1: Criar Projeto

1. Acesse [https://supabase.com](https://supabase.com)
2. Crie uma conta (se não tiver)
3. Clique em "New Project"
4. Preencha os dados e aguarde a criação

### Passo 2: Obter Credenciais

1. No Dashboard, vá em **Settings** → **API**
2. Copie:
   - **URL**: `https://xxxxx.supabase.co`
   - **anon/public key**: `eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...`

### Passo 3: Criar Tabelas

1. Vá em **SQL Editor**
2. Cole o conteúdo do arquivo `DDL_supabase_tables`
3. Clique em **Run** para executar
4. Verifique se as tabelas foram criadas em **Table Editor**

### Passo 4: Configurar RLS (Row Level Security)

**Opção 1: Desabilitar RLS (mais simples, menos seguro)**

```sql
ALTER TABLE lead_unqualified DISABLE ROW LEVEL SECURITY;
ALTER TABLE lead_qualified DISABLE ROW LEVEL SECURITY;
```

**Opção 2: Configurar RLS (recomendado)**

Já está no arquivo `DDL_supabase_tables`. Descomente as políticas necessárias.

---

## ⚙️ Configuração do PHP

### Arquivo: `config/config.php`

Abra o arquivo e configure:

```php
// Suas credenciais do Supabase
define('SUPABASE_URL', 'https://seu-projeto.supabase.co');
define('SUPABASE_KEY', 'sua-anon-key-aqui');

// Domínios permitidos (CORS)
define('ALLOWED_ORIGINS', [
    'https://seudominio.com.br',
    'https://www.seudominio.com.br',
    'http://localhost' // Apenas para desenvolvimento
]);

// Ambiente (production ou development)
define('ENVIRONMENT', 'production');
```

### Testar Conexão

Crie um arquivo `test.php` temporário:

```php
<?php
require_once 'config.php';
require_once 'SupabaseClient.php';

try {
    $supabase = new SupabaseClient();
    $result = $supabase->select('lead_unqualified', [], '*');
    echo "✅ Conexão OK!\n";
    print_r($result);
} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage();
}
?>
```

Execute: `php test.php`

---

## 📁 Estrutura de Arquivos

### `assets/css/style.css`
Estilos CSS

### `assets/js/script.js`
Script javascript Jquery

### `form.html`
Formulário HTML com Bootstrap e jQuery. Totalmente responsivo e otimizado para mobile.

### `config/config.php`
Configurações globais, credenciais do Supabase, CORS e segurança.

### `src/ajax/api_endpoint.php`
Endpoint principal que recebe requisições AJAX:
- **POST campo individual** → `lead_unqualified`
- **POST submit completo** → `lead_qualified`

### `src/SupabaseClient.php`
Classe PHP para comunicação com Supabase via REST API.

**Métodos principais:**
- `insert($table, $data)` - Inserir dados
- `update($table, $data, $filters)` - Atualizar dados
- `delete($table, $filters)` - Deleta dados
- `select($table, $filters, $select)` - Buscar dados

---

## 🔄 Funcionamento

### 1. Captura de Campo Individual

**Quando o usuário preenche um campo:**

```javascript
// Frontend (jQuery)
$('#nome').on('blur', function() {
    // Valida
    // Envia via AJAX
    enviarDado('nome', valor);
});
```

```php
// Backend (src/ajax/api_endpoint.php)
// Recebe: campo, valor, controle_u
// Verifica se existe registro
// INSERT ou UPDATE em lead_unqualified
```

### 2. Submit Final

**Quando o usuário clica em "Enviar dados":**

```javascript
// Frontend
$('#contactForm').on('submit', function() {
    // Valida todos os campos
    // Envia dados completos via AJAX
});
```

```php
// Backend (src/ajax/api_endpoint.php)
// Recebe: todos os campos + controle_u
// Valida dados completos
// INSERT em lead_qualified
// DELETE em lead_unqualified
```

### 3. Parâmetro de Controle

O sistema usa o parâmetro `u` da URL para controlar os leads:

```
https://seusite.com/formulario.html?u=campanha-facebook-123
```

Este valor (`campanha-facebook-123`) é salvo em ambas as tabelas no campo `controle_u`.

---

## 🔒 Segurança

### Implementações de Segurança:

✅ **Sanitização de Dados**: Todos os inputs são sanitizados  
✅ **Validação Server-Side**: PHP valida todos os campos  
✅ **CORS Configurável**: Apenas domínios permitidos  
✅ **Headers de Segurança**: X-Frame-Options, X-XSS-Protection  
✅ **HTTPS Recomendado**: Para tráfego criptografado  
✅ **Logs de Requisições**: Para auditoria  
✅ **Proteção de Arquivos**: .htaccess bloqueia acesso direto  

### Recomendações Adicionais:

1. **Use HTTPS** em produção
2. **Configure RLS** no Supabase
3. **Limite rate limiting** no servidor
4. **Monitore logs** regularmente
5. **Mantenha credenciais seguras** (nunca comite config.php)

---

## 🐛 Troubleshooting

### Problema: "CORS Error"

**Solução:**
```php
// Em config.php, adicione seu domínio
define('ALLOWED_ORIGINS', [
    'https://seudominio.com.br'
]);
```

### Problema: "Connection refused"

**Verificar:**
1. SUPABASE_URL está correto?
2. SUPABASE_KEY está correta?
3. Firewall está bloqueando?
4. cURL está instalado? (`php -m | grep curl`)

### Problema: "Erro 401 Unauthorized"

**Solução:**
- Verifique se a API Key está correta
- Confirme se RLS está configurado corretamente
- Use a `anon` key, não a `service_role` key

### Problema: "Tabelas não encontradas"

**Solução:**
```sql
-- Execute novamente o SQL de criação
-- Verifique se está no projeto correto do Supabase
```

### Problema: "Dados não salvam"

**Debug:**
```php
// Em config.php, ative debug
define('ENVIRONMENT', 'development');

// Verifique logs
tail -f config/logs/api.log
```

---

## 📊 Consultas Úteis

### Ver últimos leads qualificados:
```sql
SELECT * FROM lead_qualified 
ORDER BY created_at DESC 
LIMIT 10;
```

### Taxa de conversão:
```sql
SELECT * FROM vw_conversao_leads;
```

### Leads por faturamento:
```sql
SELECT faturamento, COUNT(*) as total
FROM lead_qualified
GROUP BY faturamento
ORDER BY total DESC;
```

---

## 📝 Checklist de Deploy

- [ ] PHP 7.4+ instalado
- [ ] Extensões PHP habilitadas (curl, json)
- [ ] Projeto Supabase criado
- [ ] Tabelas criadas no Supabase
- [ ] Credenciais configuradas em `config/config.php`
- [ ] CORS configurado
- [ ] Diretório `config/logs/` criado com permissões
- [ ] .htaccess configurado (se Apache)
- [ ] HTTPS habilitado (produção)
- [ ] Teste de conexão realizado
- [ ] Formulário testado em mobile
- [ ] Logs monitorados

---

## 🆘 Suporte

Se precisar de ajuda:

1. **Verifique os logs**: `config/logs/api.log`
2. **Use modo debug**: `ENVIRONMENT = 'development'`
3. **Teste a API**: Use Postman ou cURL
4. **Documentação Supabase**: [https://supabase.com/docs](https://supabase.com/docs)

---

## 📄 Licença

@author Bruno Pelatieri Goulart
@version 1.0.0

Sistema desenvolvido para captura de leads com IA e tecnologia.

**Desenvolvido com ❤️ usando PHP e Supabase**

Este projeto está licenciado sob os termos da licença **[MIT License](LICENSE)**.

&copy; 2025 Bruno Pelatieri Goulart. Todos os direitos reservados.