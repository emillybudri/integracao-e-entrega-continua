(function () {
    var cfg = window.PortalCRUD;
    if (!cfg) { return; }

    var $ = function (sel) { return document.querySelector(sel); };

    var editor = $('#editor');
    var form = $('#form-post');
    var confirmar = $('#confirmar');
    var toast = $('#toast');
    var erro = $('#form-erro');
    var salvar = $('#salvar');
    var idField = $('#post-id');
    var okBtn = $('#confirmar-ok');
    var alvoExclusao = null;
    var MAX_IMG = 5 * 1024 * 1024;

    function url(path, params) {
        var u = cfg.root + path;
        if (params) { u += (u.indexOf('?') > -1 ? '&' : '?') + params; }
        return u;
    }

    // Comunicação com a REST API do WordPress
    function api(path, options, params) {
        options = options || {};
        options.credentials = 'same-origin';
        options.headers = Object.assign({ 'X-WP-Nonce': cfg.nonce }, options.headers || {});
        return fetch(url(path, params), options).then(function (res) {
            return res.json().catch(function () { return {}; }).then(function (body) {
                if (!res.ok) {
                    throw new Error(body.message || 'Não foi possível concluir a ação (' + res.status + ').');
                }
                return body;
            });
        });
    }

    function json(method, data) {
        return { method: method, headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(data) };
    }

    function enviarImagem(file, titulo) {
        var nome = file.name.replace(/[^\w.\-]+/g, '-');
        return api('media', {
            method: 'POST',
            headers: {
                'Content-Type': file.type,
                'Content-Disposition': 'attachment; filename="' + nome + '"'
            },
            body: file
        }).then(function (media) {
            return api('media/' + media.id, json('POST', { alt_text: titulo })).then(function () { return media; });
        });
    }

    function mostrarErro(el, msg) {
        el.textContent = msg;
        el.hidden = !msg;
    }

    function avisar(msg) {
        try { sessionStorage.setItem('portal-aviso', msg); } catch (e) {}
    }

    function exibirAviso(msg) {
        toast.textContent = msg;
        toast.hidden = false;
        setTimeout(function () { toast.hidden = true; }, 3500);
    }

    function ocupado(estado) {
        salvar.disabled = estado;
        salvar.textContent = estado ? 'Salvando…' : 'Salvar';
    }

    function abrirEditor(post) {
        form.reset();
        mostrarErro(erro, '');
        ocupado(false);
        $('#editor-titulo').textContent = post ? 'Editar publicação' : 'Nova publicação';
        idField.value = post ? post.id : '';
        if (post) {
            $('#post-titulo').value = post.title.raw || '';
            $('#post-resumo').value = post.excerpt.raw || '';
            $('#post-conteudo').value = post.content.raw || '';
            $('#post-status').value = post.status === 'draft' ? 'draft' : 'publish';
        }
        $('#imagem-dica').textContent = post && post.featured_media
            ? 'A imagem atual será mantida, a menos que você escolha outra.'
            : 'Formatos aceitos: PNG, JPG, WebP e GIF, até 5 MB.';
        editor.showModal();
        $('#post-titulo').focus();
    }

    function depoisDeExcluir() {
        if (document.body.classList.contains('single')) {
            location.assign(cfg.home);
        } else {
            location.reload();
        }
    }

    $('#fab-novo').addEventListener('click', function () { abrirEditor(null); });

    document.addEventListener('click', function (e) {
        var edit = e.target.closest('[data-edit]');
        var del = e.target.closest('[data-delete]');
        var close = e.target.closest('[data-close]');

        if (edit) {
            edit.disabled = true;
            api('posts/' + edit.getAttribute('data-edit'), {}, 'context=edit')
                .then(abrirEditor)
                .catch(function (err) { exibirAviso(err.message); })
                .then(function () { edit.disabled = false; });
        }

        if (del) {
            alvoExclusao = del.getAttribute('data-delete');
            $('#confirmar-texto').textContent = '“' + del.getAttribute('data-title') + '” será movida para a lixeira e poderá ser restaurada pelo painel do WordPress.';
            mostrarErro($('#confirmar-erro'), '');
            okBtn.disabled = false;
            confirmar.showModal();
        }

        if (close) {
            var dlg = close.closest('dialog');
            if (dlg) { dlg.close(); }
        }
    });

    // Fecha o modal ao clicar no fundo escuro
    [editor, confirmar].forEach(function (dlg) {
        dlg.addEventListener('click', function (e) { if (e.target === dlg) { dlg.close(); } });
    });

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        mostrarErro(erro, '');

        var titulo = $('#post-titulo').value.trim();
        var arquivo = $('#post-imagem').files[0];
        var id = idField.value;

        if (!titulo) { mostrarErro(erro, 'Informe um título.'); return; }
        if (arquivo && arquivo.size > MAX_IMG) { mostrarErro(erro, 'A imagem deve ter no máximo 5 MB.'); return; }

        var dados = {
            title: titulo,
            excerpt: $('#post-resumo').value.trim(),
            content: $('#post-conteudo').value,
            status: $('#post-status').value
        };

        ocupado(true);

        var etapa = arquivo ? enviarImagem(arquivo, titulo) : Promise.resolve(null);
        etapa.then(function (media) {
            if (media) { dados.featured_media = media.id; }
            return api(id ? 'posts/' + id : 'posts', json('POST', dados));
        }).then(function () {
            avisar(id ? 'Publicação atualizada.' : 'Publicação criada.');
            if (id) { location.reload(); } else { location.assign(cfg.home); }
        }).catch(function (err) {
            mostrarErro(erro, err.message);
            ocupado(false);
        });
    });

    okBtn.addEventListener('click', function () {
        if (!alvoExclusao) { return; }
        okBtn.disabled = true;
        api('posts/' + alvoExclusao, { method: 'DELETE' }).then(function () {
            avisar('Publicação movida para a lixeira.');
            depoisDeExcluir();
        }).catch(function (err) {
            mostrarErro($('#confirmar-erro'), err.message);
            okBtn.disabled = false;
        });
    });

    // Exibe notificação caso haja mensagem salva no sessionStorage
    try {
        var msg = sessionStorage.getItem('portal-aviso');
        if (msg) {
            sessionStorage.removeItem('portal-aviso');
            exibirAviso(msg);
        }
    } catch (e) {}
})();

