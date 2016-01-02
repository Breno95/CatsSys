# catsSys

    Sistema administrativo do CATS utilizando Zend Framework 2

    Instalar php5, apache2, mysql
    Digite ou cole no terminal: `sudo apt-get install php5 mysql-server php5-mysql composer apache2 -y`
    OBS: lembre-se do usuário e senha inseridos na instalação do mysql. Eles serão necessários para a manipulação dos bancos de dados.

    Verificar se o apache está funcionando adequadamente

    vá no navegador e digite [http://localhost]([http://localhost) você deverá ver a página padrão de instalação do apache

    Configuração do virtual host
    Digite ou cole no terminal: `sudo gedit /etc/apache2/sites-available/cats-lab.conf`

    cole no arquivo de texto o seguinte conteúdo, substituindo `<HOME>` pelo valor retornado
    pelo comando `echo $HOME` no terminal

    ```
    <VirtualHost 127.1.1.100:80>

            ServerName cats-lab.lan

            ServerAdmin catsinformatica@gmail.com
            DocumentRoot "<HOME>/vhosts/cats-lab/public"

            <Directory "<HOME>/vhosts/cats-lab/public">
                    AllowOverride All
                    Require all granted
            </Directory>

            SetEnv "APP_ENV" "development"

            ErrorLog <HOME>/vhosts/cats-lab/error.log
            CustomLog <HOME>/vhosts/cats-lab/access.log combined

    </VirtualHost>

    # vim: syntax=apache ts=4 sw=4 sts=4 sr noet
    ```

    Salve e feche o arquivo de texto

    Pelo gerenciador de arquivos ou pelo terminal crie uma pasta chamada vhosts/cats-lab na raiz do diretório home do usuário (por exemplo: /home/marcio/vhosts/cats-lab)
    digite ou cole no terminal:
    ```
	cd ~
	mkdir vhosts
	cd vhosts
	mkdir cats-lab
        mkdir cats-lab/public
    ```

    para que o site possa ser acessado localmente via nome ao invés de um ip

    digite ou cole no terminal: `sudo gedit /etc/hosts`

    Adicione ao final do arquivo a linha:
	`127.1.1.100	cats-lab.lan # nome associado ao virtual host local de desenvolvimento`


    Habilitar modo de reescrita no apache2
    digite ou cole no terminal: sudo a2enmode rewrite

    Habilitar o site criado
    digite ou cole no terminal: `sudo a2ensite cats-lab.conf`

    Reiniciar o apache: sudo service apache2 restart

    Testar se o virtual host para o cats foi criado com sucesso

    Em `~/vhosts/cats-lab/public` crie um arquivo chamado index.php
    e cole o seguinte conteúdo

    ```
    <?php 
        phpinfo();
    ```

    No navegador digite [http://cats-lab.lan](http://cats-lab.lan), você deverá ver as configurações da instalação do php
    
_____________________________

        Segunda etapa
______________________________

   Instalar git
	* No terminal digite: `sudo apt-get install git`

   Instalar Netbeans IDE
	* acesse o link [https://netbeans.org/downloads/](https://netbeans.org/downloads/) e baixe a última versão do Netbeans para PHP.

    Clonar o projeto do CATS. vá na pasta vhosts e delete a pasta cats-lab, em seguida abra o terminal e digite o comando
        * `git clone https://github.com/marciodojr/catsSys.git`
    Renomeie a pasta clonada do github para cats-lab

    Instalar as bibliotecas externas
    Entre na pasta cats-lab e abra o terminal e digite
        * `COMPOSER_PROCESS_TIMEOUT=2000 composer install`
    
    Todas os pacotes necessários para o projeto serão baixados para a pasta .../vendor
    Obs: intencionalmente a pasta vendor está configurada para ficar fora do projeto do CATS para não misturar o código e permitir
    compartilhamento entre novos projetos.

    Além do composer (utilizado para o php), é utilizado um programa semelhante para js, css chamado bower
    
    Instalação do bower:
        * `sudo apt-get install npm`
        * `sudo npm install -g bower`
    
    Após instalar o bower vá na pasta `cats-lab/public` e no terminal digite:
        * `bower install`
    Todas as dependencias de css e Js serão instaladas. O sistema do CATS utiliza:
        * AdminLTE v2
        * Bootstrap v3
        * JQuery 2.x
        * Talvez alguma outra coisa que não me lembro agora.
    
    Criar arquivo local.php em ./config/autoload/
    ```
    /*
    * ./config/autoload/local.php
    *
    * inserir usuario, senha e nome do banco de dados que será utilizado
    * localmente
    */
    return array(
       'doctrine' => array(
           'connection' => array(
               'orm_default' => array(
                   'params' => array(
                       'user'     => 'root',
                       'password' => 'root',
                       'dbname'   => 'catssys',
                   ),
               ),
           ),
       ),
    );
    ```

    Fazer a cópia do arquivo de configurações do Zend Developer Tools
    Dentro da pasta cats-lab abra o terminal e digite:
    `cp ./../vendor/zendframework/zend-developer-tools/config/zenddevelopertools.local.php.dist ./config/autoload/zenddevelopertools.local.php`

    Criar um banco de dados Mysql com o usuário, senha e banco iguais aos valores inseridos no arquivo local.php

    Intencionalmente o git foi configurado para não sincronizar alguns arquivos:
        > todos os arquivos de desenvolvimento;
        > configuração local
        > configuração do projeto no Netbeans.
    Sendo assim é preciso importar um novo projeto no Netbeans. Para importar o projeto no Netbeans siga as instruções abaixo:

    File > New Project > (PHP Aplication with Existing Sources) > (Selecionar a pasta clonada do github (cats-lab), escolher a versão 5.5 do PHP)
    
    Toda manipulação de banco de dados feita pelo sistema do CATS será por meio de Mapeamento Objeto-Relacional
    desse modo é possível criar as tabelas do banco de dados a partir de certos objetos PHP
    
    Gravar entidades no banco de dados a partir de objetos PHP

    Abra o terminal na pasta cats-lab e digite os comandos 
    (Obs: o banco de dados catssys deve existir e o arquivo local.php deve estar configurado como mencionado anteriormente)
    
    Verifica se o mapeamento está correto e avisa se o schema do bando de dados é igual as classes mapeadas
        E1: `php public/index.php orm:validate-schema`

    Cria as tabelas do banco de dados (em caso de falha utilize o parâmetro `--force` ao final)
        E2: `php public/index.php orm:schema-tool:create`
    
    A medida que novos objetos que representam tabelas do banco de dados vão sendo criados é possível atualizar 
    o schema do banco. Primeiramente é preciso utilizar o comando E1 para verificar se o objeto foi criado corretamente 
    (validar o código antes de criar as tabelas) em seguida é utilizado o comando abaixo:
        E3: `php public/index.php orm:schema-tool:update --force`

    importar os dados para banco de dados
    `mysqldump --no-create-info -u root -p catssys <catssys_data.sql`

    Obs: o arquivo catssys_data.sql está junto com esse arquivo de documentação

    após executar todos estes passos (se nada der errado) va no navegador e digite
        - [http://cats-lab.lan/](http://cats-lab.lan/)
        Será exibida uma página que representa o site (só tem o necessário para acessar o sistema)
        Clique em login e insira as credenciais:
            * `username: fcadmin`
            * `password: 177598230afbg#`
        
        Se der tudo certo você estará dentro do sistema (ainda não tem muita coisa tudo do sistema antigo está sendo refeito
    e as novas funcionalidades ainda estão para serem criadas)
        
