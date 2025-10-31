<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Phyonext - IA Tech</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- CSS -->
    <link href="/assets/css/style.css" rel="stylesheet">  
</head>
<body>
    <div class="form-container">
        <!-- Formulário -->
        <div id="formContent">
            <div class="form-header">
                <div class="ai-icon">
                    <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12 2L2 7v10c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V7l-10-5zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm-1-13h2v6h-2zm0 8h2v2h-2z"/>
                    </svg>
                </div>
                <h1 class="form-title">Transforme seu negócio com IA</h1>
                <p class="form-subtitle">Preencha os dados e descubra como a inteligência artificial pode revolucionar sua empresa</p>
            </div>
            
            <form id="contactForm">
                <!-- Nome -->
                <div class="mb-4">
                    <label for="nome" class="form-label">Nome completo *</label>
                    <input type="text" class="form-control" id="nome" name="nome" required>
                </div>
                
                <!-- WhatsApp -->
                <div class="mb-4">
                    <label for="whatsapp" class="form-label">WhatsApp *</label>
                    <input type="text" class="form-control" id="whatsapp" name="whatsapp" placeholder="(00) 00000-0000" required>
                </div>
                
                <!-- Email -->
                <div class="mb-4">
                    <label for="email" class="form-label">E-mail *</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>
                
                <!-- Faturamento -->
                <div class="mb-4">
                    <label class="form-label">Faturamento anual *</label>
                    <div class="radio-group">
                        <div class="radio-option">
                            <input type="radio" id="fat1" name="faturamento" value="Até R$100k/ano" required>
                            <label for="fat1">Até R$100k/ano</label>
                        </div>
                        <div class="radio-option">
                            <input type="radio" id="fat2" name="faturamento" value="R$100K a R$500k/ano">
                            <label for="fat2">R$100K a R$500k/ano</label>
                        </div>
                        <div class="radio-option">
                            <input type="radio" id="fat3" name="faturamento" value="R$500K a R$1M/ano">
                            <label for="fat3">R$500K a R$1M/ano</label>
                        </div>
                        <div class="radio-option">
                            <input type="radio" id="fat4" name="faturamento" value="R$1M a R$5M/ano">
                            <label for="fat4">R$1M a R$5M/ano</label>
                        </div>
                        <div class="radio-option">
                            <input type="radio" id="fat5" name="faturamento" value="+R$10M/ano">
                            <label for="fat5">+R$10M/ano</label>
                        </div>
                    </div>
                </div>
                
                <button type="submit" class="btn-submit" id="submitBtn">
                    Enviar dados
                </button>
            </form>
        </div>
        
        <!-- Mensagem de agradecimento -->
        <div class="thank-you-message" id="thankYouMessage">
            <div class="thank-you-icon">
                <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z"/>
                </svg>
            </div>
            <h2 class="thank-you-title">Obrigado!</h2>
            <p class="thank-you-text">Seus dados foram enviados com sucesso. Em breve entraremos em contato para mostrar como a IA pode transformar seu negócio.</p>
        </div>
    </div>
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    
    <!-- jQuery Mask Plugin -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        $(document).ready(function() {
            // Captura parâmetro 'u' da URL
            const urlParams = new URLSearchParams(window.location.search);
            const controleU = urlParams.get('u') || '';
            
            // Máscara para WhatsApp
            $('#whatsapp').mask('(00) 00000-0000');
            
            // Objeto para armazenar dados já enviados
            let dadosEnviados = {
                nome: false,
                whatsapp: false,
                email: false,
                faturamento: false
            };
            
            // Função para exibir alertas customizados
            function showAlert(message, type = 'error') {
                // Remove alertas anteriores
                $('.custom-alert').remove();
                
                const icons = {
                    error: '<svg class="alert-icon" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>',
                    success: '<svg class="alert-icon" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>',
                    info: '<svg class="alert-icon" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/></svg>'
                };
                
                const alert = $(`
                    <div class="custom-alert alert-${type}">
                        ${icons[type]}
                        <div class="alert-message">${message}</div>
                    </div>
                `);
                
                $('body').append(alert);
                
                setTimeout(() => {
                    alert.fadeOut(400, function() {
                        $(this).remove();
                    });
                }, 4000);
            }
            
            // Função para validar email
            function validarEmail(email) {
                const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                return regex.test(email);
            }
            
            // Função para validar WhatsApp (celular BR)
            function validarWhatsApp(whatsapp) {
                const numero = whatsapp.replace(/\D/g, '');
                // Verifica se tem 11 dígitos e se o 3º dígito é 9 (celular)
                return numero.length === 11 && numero.charAt(2) === '9';
            }
            
            // Função para enviar dados via AJAX
            function enviarDado(campo, valor) {
                if (dadosEnviados[campo]) return; // Não reenvia se já foi enviado
                
                $.ajax({
                    url: 'https://seu-endpoint-aqui.com/api/salvar', // Substitua pela sua URL
                    method: 'POST',
                    data: {
                        campo: campo,
                        valor: valor,
                        controle: controleU,
                        timestamp: new Date().toISOString()
                    },
                    success: function(response) {
                        dadosEnviados[campo] = true;
                        console.log(`${campo} enviado com sucesso:`, response);
                    },
                    error: function(xhr, status, error) {
                        console.error(`Erro ao enviar ${campo}:`, error);
                        // Não mostra erro ao usuário para não prejudicar a experiência
                    }
                });
            }
            
            // Validação e envio do Nome
            $('#nome').on('blur', function() {
                const valor = $(this).val().trim();
                
                if (valor === '') {
                    $(this).removeClass('is-valid').addClass('is-invalid');
                    showAlert('Por favor, preencha seu nome completo', 'error');
                } else if (valor.length < 3) {
                    $(this).removeClass('is-valid').addClass('is-invalid');
                    showAlert('Nome deve ter pelo menos 3 caracteres', 'error');
                } else {
                    $(this).removeClass('is-invalid').addClass('is-valid');
                    enviarDado('nome', valor);
                }
            });
            
            // Validação e envio do WhatsApp
            $('#whatsapp').on('blur', function() {
                const valor = $(this).val();
                
                if (valor === '') {
                    $(this).removeClass('is-valid').addClass('is-invalid');
                    showAlert('Por favor, preencha seu WhatsApp', 'error');
                } else if (!validarWhatsApp(valor)) {
                    $(this).removeClass('is-valid').addClass('is-invalid');
                    showAlert('WhatsApp inválido. Use um número de celular brasileiro válido', 'error');
                } else {
                    $(this).removeClass('is-invalid').addClass('is-valid');
                    enviarDado('whatsapp', valor);
                }
            });
            
            // Validação e envio do Email
            $('#email').on('blur', function() {
                const valor = $(this).val().trim();
                
                if (valor === '') {
                    $(this).removeClass('is-valid').addClass('is-invalid');
                    showAlert('Por favor, preencha seu e-mail', 'error');
                } else if (!validarEmail(valor)) {
                    $(this).removeClass('is-valid').addClass('is-invalid');
                    showAlert('E-mail inválido. Use um formato válido (exemplo@email.com)', 'error');
                } else {
                    $(this).removeClass('is-invalid').addClass('is-valid');
                    enviarDado('email', valor);
                }
            });
            
            // Envio do Faturamento
            $('input[name="faturamento"]').on('change', function() {
                const valor = $(this).val();
                enviarDado('faturamento', valor);
            });
            
            // Submit do formulário
            $('#contactForm').on('submit', function(e) {
                e.preventDefault();
                
                // Validações finais
                let isValid = true;
                
                const nome = $('#nome').val().trim();
                const whatsapp = $('#whatsapp').val();
                const email = $('#email').val().trim();
                const faturamento = $('input[name="faturamento"]:checked').val();
                
                if (nome === '' || nome.length < 3) {
                    $('#nome').addClass('is-invalid');
                    isValid = false;
                }
                
                if (!validarWhatsApp(whatsapp)) {
                    $('#whatsapp').addClass('is-invalid');
                    isValid = false;
                }
                
                if (!validarEmail(email)) {
                    $('#email').addClass('is-invalid');
                    isValid = false;
                }
                
                if (!faturamento) {
                    showAlert('Por favor, selecione o faturamento anual', 'error');
                    isValid = false;
                }
                
                if (!isValid) {
                    showAlert('Por favor, preencha todos os campos corretamente', 'error');
                    return;
                }
                
                // Desabilita botão
                $('#submitBtn').prop('disabled', true).text('Enviando...');
                
                // Envia dados finais (caso algum não tenha sido enviado)
                $.ajax({
                    url: 'https://seu-endpoint-aqui.com/api/finalizar', // Substitua pela sua URL
                    method: 'POST',
                    data: {
                        nome: nome,
                        whatsapp: whatsapp,
                        email: email,
                        faturamento: faturamento,
                        controle: controleU,
                        timestamp: new Date().toISOString()
                    },
                    success: function(response) {
                        console.log('Formulário enviado com sucesso:', response);
                        
                        // Esconde formulário
                        $('#formContent').fadeOut(400, function() {
                            // Reseta formulário
                            $('#contactForm')[0].reset();
                            $('.form-control').removeClass('is-valid is-invalid');
                            
                            // Reseta dados enviados
                            dadosEnviados = {
                                nome: false,
                                whatsapp: false,
                                email: false,
                                faturamento: false
                            };
                            
                            // Mostra mensagem de agradecimento
                            $('#thankYouMessage').addClass('active');
                            
                            // Reabilita botão
                            $('#submitBtn').prop('disabled', false).text('Enviar dados');
                        });
                    },
                    error: function(xhr, status, error) {
                        console.error('Erro ao enviar formulário:', error);
                        showAlert('Erro ao enviar formulário. Tente novamente.', 'error');
                        $('#submitBtn').prop('disabled', false).text('Enviar dados');
                    }
                });
            });
        });
    </script>
</body>
</html>