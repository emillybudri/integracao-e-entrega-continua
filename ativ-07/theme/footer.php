</main>

<footer class="site-footer">
    <div class="wide">
        <span>Atividade 07 · Integração e Entrega Contínua</span>
        <a href="https://github.com/emillybudri/integracao-e-entrega-continua" target="_blank" rel="noopener">GitHub</a>
    </div>
</footer>

<?php if ( portal_cicd_pode_editar() ) : ?>

<button type="button" class="fab" id="fab-novo" aria-label="Nova publicação" title="Nova publicação">
    <svg width="22" height="22" viewBox="0 0 22 22" aria-hidden="true"><path d="M11 3v16M3 11h16" stroke="currentColor" stroke-width="2.25" stroke-linecap="round" fill="none"/></svg>
</button>

<dialog id="editor" class="dialog" aria-labelledby="editor-titulo">
    <form id="form-post">
        <header class="dialog-head">
            <h2 id="editor-titulo">Nova publicação</h2>
            <button type="button" class="dialog-x" data-close aria-label="Fechar">×</button>
        </header>
        <div class="dialog-body">
            <input type="hidden" id="post-id">
            <label for="post-titulo">Título</label>
            <input type="text" id="post-titulo" required maxlength="200" autocomplete="off">

            <label for="post-resumo">Resumo <span class="opt">opcional</span></label>
            <textarea id="post-resumo" rows="2" maxlength="300"></textarea>

            <label for="post-conteudo">Conteúdo</label>
            <textarea id="post-conteudo" rows="9" required></textarea>
            <p class="hint">Separe parágrafos com uma linha em branco. HTML básico é aceito.</p>

            <div class="row">
                <div>
                    <label for="post-status">Estado</label>
                    <select id="post-status">
                        <option value="publish">Publicado</option>
                        <option value="draft">Rascunho</option>
                    </select>
                </div>
                <div>
                    <label for="post-imagem">Imagem de destaque <span class="opt">opcional</span></label>
                    <input type="file" id="post-imagem" accept="image/png,image/jpeg,image/webp,image/gif">
                </div>
            </div>
            <p class="hint" id="imagem-dica"></p>
            <p class="form-error" id="form-erro" role="alert" hidden></p>
        </div>
        <footer class="dialog-foot">
            <button type="button" class="btn btn-ghost" data-close>Cancelar</button>
            <button type="submit" class="btn" id="salvar">Salvar</button>
        </footer>
    </form>
</dialog>

<dialog id="confirmar" class="dialog dialog-sm" aria-labelledby="confirmar-titulo">
    <div class="dialog-body">
        <h2 id="confirmar-titulo">Excluir publicação?</h2>
        <p class="hint" id="confirmar-texto"></p>
        <p class="form-error" id="confirmar-erro" role="alert" hidden></p>
    </div>
    <footer class="dialog-foot">
        <button type="button" class="btn btn-ghost" data-close>Cancelar</button>
        <button type="button" class="btn" id="confirmar-ok">Excluir</button>
    </footer>
</dialog>

<div class="toast" id="toast" role="status" aria-live="polite" hidden></div>

<?php endif; ?>

<?php wp_footer(); ?>
</body>
</html>
