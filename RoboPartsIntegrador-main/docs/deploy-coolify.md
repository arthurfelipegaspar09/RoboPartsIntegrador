# Deploy no Coolify

Repita este procedimento para cada fork. Cada cadastro gera containers, rede e volume de banco independentes.

O banco utilizado pelo projeto é MySQL 8.4.

## Criar a aplicação

1. No projeto da turma, escolha **New Resource** e **Application**.
2. Conecte o fork do grupo e selecione a branch `main`.
3. Escolha **Docker Compose** como build pack.
4. Informe `compose.yaml` como arquivo Compose.
5. Mantenha o modo Compose normal do Coolify; não use Raw Compose.
6. Configure o domínio do serviço `app`.

## Variáveis obrigatórias

Defina valores diferentes e fortes em cada aplicação:

```dotenv
APP_ENV=production
DB_HOST=db
DB_PORT=3306
DB_DATABASE=integrador
DB_USERNAME=integrador
DB_PASSWORD=senha-exclusiva-do-grupo
DB_ROOT_PASSWORD=outra-senha-exclusiva
```

O Coolify detecta as referências do `compose.yaml` e apresenta essas variáveis no painel. Não copie o `.env` local para o servidor.

## Após o primeiro deploy

1. Confirme que `app` está em execução e que `db` está saudável.
2. Abra o domínio e confira a mensagem `Banco de dados: Conectado`.
3. Ative o deploy automático da branch `main`, se desejado.
4. Configure backups para o volume `db_data`.

Não exponha o serviço `db` por domínio ou porta pública.
