# Futebol - Resultados e Jogos

![Football](https://upload.wikimedia.org/wikipedia/commons/7/7b/Football_icon.svg)

Este projeto é uma aplicação PHP que utiliza a API do **API-Football** para fornecer informações atualizadas sobre jogos, resultados, estatísticas de times, ligas e temporadas. O foco principal é oferecer dados detalhados sobre campeonatos brasileiros e internacionais, permitindo consultas personalizadas por temporada, liga e time.

---

## Índice

- [Sobre o Projeto](#sobre-o-projeto)
- [Funcionalidades](#funcionalidades)
- [Tecnologias Utilizadas](#tecnologias-utilizadas)
- [Pré-requisitos](#pré-requisitos)
- [Instalação e Configuração](#instalação-e-configuração)
  - [Configuração do arquivo `config.php`](#configuração-do-arquivo-configphp)
  - [Subindo o Servidor Local](#subindo-o-servidor-local)
- [Estrutura do Projeto](#estrutura-do-projeto)
  - [Descrição dos Arquivos](#descrição-dos-arquivos)
- [Como Funciona](#como-funciona)
  - [Exibição dos Jogos](#exibição-dos-jogos)
  - [Filtros de Liga e Temporada](#filtros-de-liga-e-temporada)
  - [Pesquisa de Time](#pesquisa-de-time)
- [Exemplos de Uso](#exemplos-de-uso)
- [Contribuição](#contribuição)
  - [Fluxo de Trabalho para Contribuições](#fluxo-de-trabalho-para-contribuições)
- [Licença](#licença)

---

## Sobre o Projeto

A aplicação foi desenvolvida para ser uma ferramenta simples e eficiente, que consome dados da API **API-Football** e permite aos usuários acessar informações atualizadas sobre futebol. Com foco em campeonatos brasileiros (como o Campeonato Brasileiro, Copa do Brasil, Libertadores e Sulamericana) e internacionais, a aplicação oferece uma interface intuitiva para consulta de resultados, estatísticas e detalhes de jogos.

---

## Funcionalidades

- **Exibição de Resultados**: Visualização de jogos recentes e próximos, com detalhes como placar, data, hora e informações adicionais.
- **Filtros de Liga e Temporada**: Permite ao usuário selecionar ligas e temporadas específicas para visualizar jogos.
- **Pesquisa de Times**: Busca por times específicos para visualizar estatísticas como gols, vitórias, empates e derrotas na temporada atual.
- **Responsividade**: Interface adaptável para diferentes dispositivos (desktop, tablet e mobile).
- **Integração com API-Football**: Consome dados em tempo real da API para fornecer informações atualizadas sobre jogos, equipes e estatísticas.

---

## Tecnologias Utilizadas

- **PHP**: Linguagem principal para o backend da aplicação.
- **HTML5 e CSS3**: Estruturação e estilização da interface.
- **JavaScript (Vanilla)**: Para funcionalidades dinâmicas, como filtros e pesquisas em tempo real.
- **API-Football**: API externa que fornece dados sobre futebol, como resultados, rankings e estatísticas.
- **Font Awesome**: Biblioteca de ícones para melhorar a experiência visual.
- **Google Fonts**: Importação de fontes personalizadas.

---

## Pré-requisitos

Para rodar a aplicação, você precisará dos seguintes requisitos:

- **PHP 7.4 ou superior**: A aplicação foi desenvolvida utilizando PHP 7.4+.
- **Servidor Web**: Apache, Nginx ou servidor embutido do PHP.
- **Chave da API**: Obtenha uma chave de API gratuita no site da [API-Football](https://rapidapi.com/api-football/api/api-football).

---

## Instalação e Configuração

### Configuração do arquivo `config.php`

1. Abra o arquivo `config.php` na raiz do projeto.
2. Insira sua chave de API no seguinte trecho:

   ```php
   <?php
   // Sua chave da API
   define('API_KEY', 'SUA_API_KEY_AQUI');
   // URL da API
   define('API_URL', 'https://api-football-v1.p.rapidapi.com/v3/');
   ?>
Subindo o Servidor Local
Servidor PHP embutido:

php -S localhost:8000
Acesse http://localhost:8000 no navegador.

Servidor Apache/Nginx:
Configure o servidor para apontar para a pasta do projeto.

---

## Como Funciona

---
Exibição dos Jogos
Jogos Futuros: Lista os próximos jogos para a temporada e liga selecionada.

---
Últimos Resultados: Exibe os jogos passados e os resultados para uma liga e temporada específica.

---
Filtros de Liga e Temporada
Liga: Campeonato Brasileiro, Copa do Brasil, Libertadores, Copa Sulamericana.

---
Temporada: Anos de 2020 até o ano atual.

---
Pesquisa de Time
Digite o nome de um time para visualizar estatísticas como gols marcados, vitórias e empates.

---
Exemplos de Uso
Exibir Últimos Resultados da Série A:

---
Selecione a Liga: Campeonato Brasileiro.

---
Escolha a Temporada: 2024.

---
Os últimos resultados da Série A serão exibidos.

---
Buscar Estatísticas de um Time:

---
Digite o nome do time (ex: "Flamengo") no campo de pesquisa.

---
O sistema exibirá estatísticas como gols marcados, vitórias e empates.

---
## Contribuição
Fluxo de Trabalho para Contribuições
Faça um fork deste repositório.

---
Clone o repositório para seu ambiente local:

---


git clone https://github.com/seu-usuario/futebol.git
Crie uma nova branch para sua feature ou correção:

---


git checkout -b minha-nova-feature
Faça suas modificações e commit:

---


git commit -am 'Adiciona nova feature'
Envie as alterações para o repositório remoto:

---


git push origin minha-nova-feature
Abra um pull request para a branch principal.

---
