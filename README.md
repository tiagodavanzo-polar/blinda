# Módulos PHP Blinda

### Telas:

* **Painel Produção:** contém todas as linhas em produção ou a produzir;
* **Painel Engenharia:** contém todas as linhas* sinalizadas para a engenharia após o primeiro checklist;
* **Compras MRP I:** tela contendo todos os skus, demandas (Pós-Vendas), estoque, rascunho, ordens de compra e disponível.

### Configurações:

1. Copiar o arquivo config.example.php;
2. Renomear a cópia para config.php;
3. Preencher os valores com as credenciais do ambiente;
4. Informar na const SHOWENGENHARIA quais usuários terão acesso às ações do painel de engenharia.

### Pré-requisitos:

1. PHP 8.2 ou superior;
2. php_mailparse
3. php_pdo_sqlsrv_82_ts_x64