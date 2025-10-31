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
        site: false,
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
    
    // Função para validar URL
    function validarURL(url) {
        if (url.trim() === '') return true; // Campo opcional
        
        // Adiciona https:// se não tiver protocolo
        if (!url.match(/^https?:\/\//i)) {
            url = 'https://' + url;
            $('#site').val(url);
        }
        
        // Regex para validar URL
        const regex = /^https?:\/\/(www\.)?[-a-zA-Z0-9@:%._\+~#=]{1,256}\.[a-zA-Z0-9()]{1,6}\b([-a-zA-Z0-9()@:%_\+.~#?&//=]*)$/;
        return regex.test(url);
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
    
    // Validação e envio do Site (opcional)
    $('#site').on('blur', function() {
        const valor = $(this).val().trim();
        
        if (valor === '') {
            $(this).removeClass('is-valid is-invalid');
            return; // Campo opcional, não valida se estiver vazio
        }
        
        if (!validarURL(valor)) {
            $(this).removeClass('is-valid').addClass('is-invalid');
            showAlert('URL inválida. Use um formato válido (exemplo: https://seusite.com.br)', 'error');
        } else {
            $(this).removeClass('is-invalid').addClass('is-valid');
            enviarDado('site', $(this).val()); // Pega o valor atualizado com https://
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
        const site = $('#site').val().trim();
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
        
        if (site !== '' && !validarURL(site)) {
            $('#site').addClass('is-invalid');
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
                site: site,
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
                        site: false,
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