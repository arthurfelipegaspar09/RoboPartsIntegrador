# Guia dos alunos

## Primeiro uso

1. Um integrante cria o fork do repositório para o grupo.
2. Os demais integrantes clonam esse fork.
3. Cada integrante copia `.env.example` para `.env`.
4. O grupo executa `docker compose up -d --build`.
5. O desenvolvimento começa no arquivo `app/index.php`.

## Regras importantes

- Não altere senhas diretamente em `compose.yaml`.
- Não envie `.env`, senhas ou dados pessoais ao Git.
- Defina as senhas antes da primeira execução. Alterar o `.env` depois não modifica a senha armazenada no volume existente.
- Mantenha a estrutura inicial do banco em `database/schema.sql`.
- Faça commits pequenos e com mensagens que expliquem a mudança.
- Antes de enviar código, confira `docker compose ps` e os logs.

## Início de cada página

Comece toda página PHP carregando o arquivo `init.php`:

```php
<?php

require_once __DIR__ . '/init.php';
```

Ele realiza três tarefas:

1. inicia a sessão com `session_start()`;
2. cria a conexão PDO na variável `$pdo`;
3. carrega as funções disponíveis em `banco/crud.php`.

Para páginas dentro de uma subpasta, ajuste o caminho. Por exemplo:

```php
require_once dirname(__DIR__) . '/init.php';
```

## Sessão

Depois de carregar `init.php`, a variável `$_SESSION` pode ser usada normalmente:

```php
$_SESSION['usuario_id'] = 10;
$usuarioId = $_SESSION['usuario_id'] ?? null;
```

Não execute `session_start()` novamente nas páginas.

## Banco de dados e CRUD

O projeto usa MySQL 8.4. O driver `pdo_mysql` já está habilitado, e a variável `$pdo` contém a conexão. As funções do CRUD recebem essa conexão como primeiro argumento.

### Inserir

```php
$id = create($pdo, 'exemplos', [
    'titulo' => 'Primeiro registro',
]);
```

### Listar e buscar

```php
$todos = readAll($pdo, 'exemplos');
$registro = read($pdo, 'exemplos', 'id = ?', [$id]);
```

### Atualizar

```php
update(
    $pdo,
    'exemplos',
    ['titulo' => 'Título atualizado'],
    'id = ?',
    [$id],
);
```

### Excluir

```php
delete($pdo, 'exemplos', 'id = ?', [$id]);
```

Use `?` para os valores dos filtros e envie os valores no último argumento. Não coloque conteúdo recebido de formulário diretamente no SQL. Os nomes de tabelas e colunas devem ser definidos pelo código, nunca pelo usuário.

As funções recusam nomes de tabela ou coluna inválidos, inserções sem campos e alterações ou exclusões sem condição. Isso ajuda a evitar SQL inválido e mudanças acidentais em todos os registros.

## Onde ficam as configurações

- `app/init.php`: sessão e carregamento dos recursos comuns;
- `app/banco/database.php`: conexão PDO;
- `app/banco/crud.php`: funções de CRUD;
- `.env`: usuário, senha e nome do banco local;
- `database/schema.sql`: criação inicial das tabelas.
