# MaxCam RH
Este é o repositório oficial do sistema de gerenciamento de Recursos Humanos da MaxCam. O projeto é construído com Laravel 12 no backend e Vue.js com Inertia no frontend, e todo o ambiente de desenvolvimento é gerenciado via Laravel Sail (Docker).
## Pré-requisitos

Para rodar este projeto, você precisa **obrigatoriamente** ter apenas o **Docker Desktop** instalado e rodando na sua máquina.

* [Docker Desktop para Mac, Windows ou Linux](https://www.docker.com/products/docker-desktop/)

> **Atenção usuários Windows:** É fundamental que você tenha o **WSL 2 (Windows Subsystem for Linux)** instalado e ativado, pois o Docker Desktop depende dele. Siga [este guia da Microsoft](https://docs.microsoft.com/pt-br/windows/wsl/install) para a instalação.

## Guia de Instalação Passo a Passo

Siga estas instruções na ordem exata. Todos os comandos devem ser executados no seu terminal (como PowerShell, Terminal do Mac/Linux ou o terminal do seu VS Code).
1. **Clone o Repositório:**
    ```bash
    git clone git@github.com:devGustavoMuniz/maxcamrh.git
    cd maxcamrh
    ```

2.  **Copie o Arquivo de Ambiente:**
    ```bash
    cp .env.example .env
    ```
    *(Opcional: Revise o `.env` para configurações como `APP_PORT`)*


3.  **Instale as Dependências do Composer usando Docker:**
    (Isso instala o Laravel Sail e outras dependências PHP sem precisar de PHP/Composer no seu sistema host.)
    ```bash
    docker run --rm \
        -u "$(id -u):$(id -g)" \
        -v "$(pwd):/var/www/html" \
        -w /var/www/html \
        laravelsail/php84-composer:latest \
        composer install --ignore-platform-reqs --no-interaction --no-plugins --no-scripts --prefer-dist
    ```

    Dica: Daqui para frente, você pode criar um alias para não ter que digitar ./vendor/bin/sail toda vez. Adicione alias sail="./vendor/bin/sail" ao seu arquivo de configuração do terminal (.zshrc, .bashrc, etc.). Se não quiser fazer isso, apenas continue usando o caminho completo.


4.  **Construa e Inicie os Contêineres do Sail:**
    ```bash
    sail up -d --build
    ```
    *Aguarde alguns instantes para os serviços iniciarem.*


5. **Instale o composer completamente:**
    ```bash
    sail composer install
    ```

6. **Gere a Chave da Aplicação e link simbólico para storage:**
    ```bash
    sail artisan key:generate
    
    sail artisan storage:link
    ```

7. **Execute as Migrations e Seeders:**
    ```bash
    sail artisan migrate:fresh --seed
    ```

8. **Instale as Dependências NPM:**
    ```bash
    sail npm i
    ```

9. **Construa os Assets NPM :**
    ```bash
    sail npm run dev
    ```

10. **Acesse a Aplicação:**
    Abra seu navegador e acesse `http://localhost` (ou a porta configurada em `APP_PORT` no seu `.env`).


11. **Para Parar os Contêineres:**
    ```bash
    ./vendor/bin/sail down
    ```
