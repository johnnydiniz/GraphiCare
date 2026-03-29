# GraphiCare

Sistema de gestão para empresas gráficas, desenvolvido com Laravel e containerizado com Docker.

## Sobre o Projeto

O GraphiCare é um ERP voltado para gráficas, cobrindo o ciclo completo de operações: desde o cadastro de clientes e fornecedores, passando por orçamentos e ordens de serviço, até o controle financeiro e de estoque.

### Funcionalidades

- **Cadastros** - Pessoas, clientes, funcionários, fornecedores, tipos de contato
- **Serviços** - Serviços, componentes de serviço, equipamentos operacionais, matérias-primas
- **Orçamentos** - Criação, impressão (cliente/admin), geração de ordens de serviço
- **Ordens de Serviço** - Criação, acompanhamento de status, início/finalização, impressão
- **Ordens de Compra** - Compras de matérias-primas, recebimento de materiais
- **Financeiro** - Faturas (contas a receber), boletos (contas a pagar), fluxo de caixa
- **Estoque** - Controle de entradas, saídas, perdas e quebras, alertas de estoque mínimo
- **Relatórios** - Ordens de serviço, financeiro, estoque
- **Dashboard** - KPIs, resumo financeiro, vencimentos próximos, estoque crítico
- **Notificações** - Alertas de vencimentos e estoque crítico no header
- **Perfil** - Edição de dados pessoais e alteração de senha

## Requisitos

- [Docker](https://www.docker.com/) e Docker Compose

## Instalação

1. Clone o repositório:

```bash
git clone <url-do-repositorio> GraphiCare
cd GraphiCare
```

2. Copie o arquivo de ambiente:

```bash
cp .env.example .env
```

3. Suba os containers:

```bash
docker compose up -d --build
```

4. Instale as dependências e prepare a aplicação:

```bash
docker compose exec app composer install
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate
docker compose exec app php artisan db:seed
```

5. Gere os caches para melhor performance:

```bash
docker compose exec app php artisan config:cache
docker compose exec app php artisan route:cache
docker compose exec app php artisan view:cache
```

6. Acesse a aplicação em `http://localhost`.

## Serviços Docker

| Serviço    | Porta | Descrição                  |
|------------|-------|----------------------------|
| Nginx      | 80    | Servidor web               |
| Vite       | 5173  | Dev server (hot reload)    |
| MySQL      | 3306  | Banco de dados             |
| phpMyAdmin | 8080  | Interface do banco de dados|
| Redis      | 6379  | Cache e sessões            |

## Estrutura do Projeto

```
app/
├── Http/
│   └── Controllers/       # Controllers da aplicação
│       └── Auth/           # Controllers de autenticação
├── Models/                 # Models Eloquent
└── Providers/              # Provedores de serviço
config/                     # Configurações do Laravel
database/
├── migrations/             # Migrações do banco de dados
└── seeders/                # Seeders
resources/
├── views/                  # Views Blade
│   ├── layouts/            # Layouts e componentes reutilizáveis
│   └── components/         # Componentes Blade
└── sass/                   # Estilos
routes/
└── web.php                 # Rotas da aplicação
```

## Documentação da API (PHPDocumentor)

O projeto utiliza PHPDocumentor para gerar documentação a partir dos docblocks no código-fonte.

### Gerar a documentação

```bash
docker compose exec app php phpDocumentor.phar
```

A documentação será gerada em `docs/api/` e pode ser aberta no navegador.

## Tecnologias

- **Backend** - PHP 8.2, Laravel
- **Frontend** - Blade, Bootstrap, Vite
- **Banco de dados** - MySQL 8.0
- **Cache/Sessão** - Redis
- **Containerização** - Docker, Docker Compose
