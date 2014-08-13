<?php
/*

 Retorno PagSeguro 2.0 - PHP e MySQL
 por Diogo Dourado - www.dourado.net
 Última Atualização: 09/06/2011
 
 Se você ainda não é cadastrado no PagSeguro, utilize o link abaixo para se cadastrar:
 https://pagseguro.uol.com.br/?ind=528005

*/


header('Content-Type: text/html; charset=ISO-8859-1');

/* Edite este arquivo e insira suas configurações */

include 'PagSeguroRetornoConfig.php'; 

/* Não é necessário alterar nada desta linha para baixo */

include 'PagSeguroRetornoFuncoes.php';
define('TOKEN', $retorno_token);


if (count($_POST) > 0) {
	
	$npi = new PagSeguroNpi();
	$result = $npi->notificationPost();
	
	$transacaoID = isset($_POST['TransacaoID']) ? $_POST['TransacaoID'] : '';
	
	if ($result == "VERIFICADO") {
	
		// Recebendo Dados
		$VendedorEmail  = $_POST['VendedorEmail'];
		$TransacaoID = $_POST['TransacaoID'];
		$Referencia = $_POST['Referencia'];
		$Extras = MoedaBR($_POST['Extras']);
		$TipoFrete = $_POST['TipoFrete'];
		$ValorFrete = MoedaBR($_POST['ValorFrete']);
		$DataTransacao = ConverterData($_POST['DataTransacao']);
		$Anotacao = $_POST['Anotacao'];
		$TipoPagamento = $_POST['TipoPagamento'];
		$StatusTransacao = $_POST['StatusTransacao'];

		$CliNome = $_POST['CliNome'];
		$CliEmail = $_POST['CliEmail'];
		$CliEndereco = $_POST['CliEndereco'];
		$CliNumero = $_POST['CliNumero'];
		$CliComplemento = $_POST['CliComplemento'];
		$CliBairro = $_POST['CliBairro'];
		$CliCidade = $_POST['CliCidade'];
		$CliEstado = $_POST['CliEstado'];
		$CliCEP = $_POST['CliCEP'];
		$CliTelefone = $_POST['CliTelefone'];

		$NumItens = $_POST['NumItens'];
		
		// Gravando Dados
		mysql_query("INSERT into PagSeguroTransacoes SET
		VendedorEmail='$VendedorEmail',	
		TransacaoID='$TransacaoID',	
		Referencia='$Referencia',	
		Extras='$Extras',	
		TipoFrete='$TipoFrete',	
		ValorFrete='$ValorFrete',	
		DataTransacao='$DataTransacao',	
		Anotacao='$Anotacao',	
		TipoPagamento='$TipoPagamento',	
		StatusTransacao='$StatusTransacao',	
		CliNome='$CliNome',	
		CliEmail='$CliEmail',	
		CliEndereco='$CliEndereco',	
		CliNumero='$CliNumero',	
		CliComplemento='$CliComplemento',	
		CliBairro='$CliBairro',	
		CliCidade='$CliCidade',	
		CliEstado='$CliEstado',	
		CliCEP='$CliCEP',	
		CliTelefone='$CliTelefone',	
		NumItens='$NumItens',	
		Data=now();");

		// Recebendo e gravando produtos
		$Processo = mysql_query("SELECT VendedorEmail FROM PagSeguroProdutos WHERE VendedorEmail='$VendedorEmail' AND TransacaoID='$TransacaoID'");
		if (mysql_num_rows($Processo)==0) {
			for($i=1;$i<=$NumItens;$i++) {
				
				$ProdID = $_POST["ProdID_{$i}"];
				$ProdDescricao = $_POST["ProdDescricao_{$i}"];
				$ProdValor = MoedaBR($_POST["ProdValor_{$i}"]);
				$ProdQuantidade = $_POST["ProdQuantidade_{$i}"];
				$ProdFrete = MoedaBR($_POST["ProdFrete_{$i}"]);
		
				mysql_query("INSERT into PagSeguroProdutos SET
				VendedorEmail='$VendedorEmail',	
				TransacaoID='$TransacaoID',	
				Ordem='$i',	
				ProdID='$ProdID',	
				ProdDescricao='$ProdDescricao',	
				ProdValor='$ProdValor',	
				ProdQuantidade='$ProdQuantidade',	
				ProdFrete='$ProdFrete'");			
			}
		}
		
		
	} 
} else {
	Header("Location: $retorno_site"); exit();
}
?>