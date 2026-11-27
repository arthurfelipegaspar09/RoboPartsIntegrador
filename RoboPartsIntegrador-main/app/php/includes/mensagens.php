<?php
// Mensagens enviadas pelo formulário de contato.
// Ficam em data/mensagens.json (separado de loja.json) e são lidas pelo admin em php/mensagens.php.

const ARQUIVO_MENSAGENS = RAIZ . '/data/mensagens.json';

function mensagens_ler(): array
{
    if (!is_file(ARQUIVO_MENSAGENS)) {
        return [];
    }
    $json = json_decode((string) file_get_contents(ARQUIVO_MENSAGENS), true);
    return is_array($json) ? $json : [];
}

/** Lê, altera e grava o arquivo com trava, para dois envios simultâneos não se atropelarem. */
function mensagens_alterar(callable $alterar): bool
{
    $dir = dirname(ARQUIVO_MENSAGENS);
    if (!is_dir($dir) && !mkdir($dir, 0755, true)) {
        return false;
    }

    $f = fopen(ARQUIVO_MENSAGENS, 'c+');
    if (!$f) {
        return false;
    }

    $ok = false;
    if (flock($f, LOCK_EX)) {
        $atual = json_decode((string) stream_get_contents($f), true);
        $novo  = $alterar(is_array($atual) ? $atual : []);

        rewind($f);
        ftruncate($f, 0);
        $ok = fwrite($f, json_encode(array_values($novo), JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT)) !== false;
        fflush($f);
        flock($f, LOCK_UN);
    }
    fclose($f);

    return $ok;
}

function mensagem_salvar(array $dados): bool
{
    return mensagens_alterar(function (array $lista) use ($dados) {
        $lista[] = [
            'id'       => bin2hex(random_bytes(6)),
            'data'     => date('Y-m-d H:i:s'),
            'nome'     => $dados['nome'],
            'email'    => $dados['email'],
            'assunto'  => $dados['assunto'],
            'mensagem' => $dados['mensagem'],
        ];
        return $lista;
    });
}

function mensagem_excluir(string $id): bool
{
    return mensagens_alterar(fn(array $lista) => array_filter($lista, fn($m) => ($m['id'] ?? '') !== $id));
}

/** Assuntos aceitos no formulário (chave gravada => texto exibido). */
function assuntos_contato(): array
{
    return [
        'duvida'   => 'Dúvida sobre um produto',
        'orcamento' => 'Orçamento para empresa',
        'pedido'   => 'Suporte com pedido ou conta',
        'parceria' => 'Parceria ou fornecimento',
        'outro'    => 'Outro assunto',
    ];
}
