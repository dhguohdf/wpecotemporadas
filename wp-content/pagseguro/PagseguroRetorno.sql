-- --------------------------------------------------------
-- -
-- Retorno PagSeguro 2.0 - PHP e MySQL
-- por Diogo Dourado - www.dourado.net
-- Última Atualização: 09/06/2011
-- - 
-- Se você ainda não é cadastrado no PagSeguro, utilize o link abaixo para se cadastrar:
-- https://pagseguro.uol.com.br/?ind=528005
-- -
-- --------------------------------------------------------

--
-- Estrutura da tabela 'PagSeguroProdutos'
--

CREATE TABLE IF NOT EXISTS 'PagSeguroProdutos' (
  'VendedorEmail' varchar(255) NOT NULL,
  'TransacaoID' varchar(32) NOT NULL,
  'Ordem' mediumint(5) unsigned NOT NULL,
  'ProdID' varchar(100) DEFAULT NULL,
  'ProdDescricao' varchar(100) DEFAULT NULL,
  'ProdValor' decimal(10,2) DEFAULT NULL,
  'ProdQuantidade' mediumint(5) unsigned DEFAULT NULL,
  'ProdFrete' decimal(10,2) DEFAULT NULL,
  KEY 'VendedorEmail' ('VendedorEmail','TransacaoID')
) ENGINE=MyISAM DEFAULT CHARSET=latin1;


-- --------------------------------------------------------

--
-- Estrutura da tabela 'PagSeguroTransacoes'
--

CREATE TABLE IF NOT EXISTS 'PagSeguroTransacoes' (
  'VendedorEmail' varchar(255) NOT NULL,
  'TransacaoID' varchar(32) NOT NULL,
  'Referencia' varchar(255) DEFAULT NULL,
  'Extras' decimal(10,2) DEFAULT NULL,
  'TipoFrete' char(2) DEFAULT NULL,
  'ValorFrete' decimal(10,2) DEFAULT NULL,
  'Anotacao' varchar(250) DEFAULT NULL,
  'DataTransacao' datetime NOT NULL,
  'TipoPagamento' varchar(30) NOT NULL,
  'StatusTransacao' varchar(30) NOT NULL,
  'CliNome' varchar(100) NOT NULL,
  'CliEmail' varchar(255) NOT NULL,
  'CliEndereco' varchar(200) NOT NULL,
  'CliNumero' varchar(10) DEFAULT NULL,
  'CliComplemento' varchar(100) DEFAULT NULL,
  'CliBairro' varchar(100) NOT NULL,
  'CliCidade' varchar(100) NOT NULL,
  'CliEstado' char(2) NOT NULL,
  'CliCEP' varchar(10) NOT NULL,
  'CliTelefone' varchar(16) DEFAULT NULL,
  'NumItens' mediumint(5) unsigned NOT NULL,
  'Data' datetime NOT NULL,
  'Status' tinyint(1) unsigned NOT NULL DEFAULT '0',
  KEY 'VendedorTransacao' ('VendedorEmail','TransacaoID'),
  KEY 'StatusTransacao' ('StatusTransacao'),
  KEY 'Status' ('Status')
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
