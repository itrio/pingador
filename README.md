# Pingador

Um simples utilitário ICMP. Você insere uma lista de IPs ou domínios e a ferramenta envia pacotes ICMP (pings) para cada um deles, indicando o estado e a latência de cada host.

![](screenshots/index.png)
> Você pode usar o **Pingador** com IPs ou domínios.

## Como funciona?
1. Você insere uma lista de IPs e/ou domínios e clica no botão *PINGAR!*
2. Os IPs/domínios digitados aparecerão em uma tabela logo abaixo.
3. Um ping será feito para cada IP/domínio digitado.
4. O estado e a latência de cada host aparecerá no respectivo campo das colunas *status* e *tempo*.
5. Você pode limpar todas as linhas da tabela ou apenas uma que não interesse mais.
6. Você pode refazer o ping para todos os hosts da tabela ou para apenas um que você deseje atualizar.
7. Um relatório em formato *.txt* ou *.xlsx* poderá ser baixado para o seu computador contendo a lista de IPs/domínios que você inseriu e os respectivos *status*.

## Atenção!

- A ferramenta executa explicitamente comandos batch. Portanto, o funcionamento em servidores que não operem em ambiente Windows provavelmente será comprometido.
- Verifique se a função *exec()* não encontra-se desabilitada no arquivo *php.ini* do seu servidor. Essa função costuma estar desabilitada em servidores compartilhados por questões de segurança.

## Instalação
1. Clone o repositório:
~~~~
$ git clone git@github.com:itrio/pingador.git
~~~~
2. Navegue até a pasta de instalação e rode o composer:
~~~~
$ cd pingador
$ composer install
~~~~
3. Inicialize o seu servidor PHP e acesse o Pingador:
~~~~
http://localhost/pingador
~~~~

## Construído com

* [jQuery](https://maven.apache.org/) - Biblioteca Javascript
* [Materialize](https://github.com/Dogfalo/materialize) - Framework CSS baseado em Material Design
* [PhpSpreadsheet](https://github.com/PHPOffice/PhpSpreadsheet) - Biblioteca para leitura e escrita de arquivos
* [DataTables](https://github.com/DataTables/DataTables) - Biblioteca para gerenciamento de tabelas

## Autor

**Itrio Netuno** - 
[GitHub](https://github.com/itrio) -
[LinkedIn](https://www.linkedin.com/in/itrionetuno/)
