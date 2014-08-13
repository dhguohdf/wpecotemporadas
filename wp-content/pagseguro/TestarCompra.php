<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Compra Concluida</title>
</head>

<body>

<?php
$EmailVendedor = 'pagamentos@ecotemporadas.com';
$QuantidadeProdutos = rand(1,3);
?>

Teste para popular o carrinho de compras do PagSeguro. <br /><br />

Vendedor: <?php echo $EmailVendedor; ?><br /><br />
Quantidade de Produtos: <?php echo $QuantidadeProdutos; ?><br /><br />

<form target="PagSeguro" action="https://pagseguro.uol.com.br/security/webpagamentos/webpagto.aspx" method="post" name="TestePS" id="TestePS" />

<input type="hidden" name="email_cobranca" value="<?php echo $EmailVendedor; ?>">
<input type="hidden" name="tipo" value="CP" />
<input type="hidden" name="moeda" value="BRL" />
<input type="hidden" name="ref_transacao" value="Numero Randomico: <?php echo rand(1,999999); ?>" />

<?php for ($i=1;$i<=$QuantidadeProdutos;$i++) { ?>  
<input type="hidden" name="item_id_<?php echo $i; ?>" value="<?php echo rand(1,999999); ?>" />
<input type="hidden" name="item_descr_<?php echo $i; ?>" value="Produto <?php echo $i; ?> Teste" />
<input type="hidden" name="item_quant_<?php echo $i; ?>" value="<?php echo rand(1,2); ?>" />
<input type="hidden" name="item_valor_<?php echo $i; ?>" value="0<?php echo rand(1,3); ?>" />
<input type="hidden" name="item_frete_<?php echo $i; ?>" value="0" />
<?php } ?>

<input type="hidden" name="cliente_nome" value="Jose Silva Testador" />

<input type="hidden" name="cliente_cep" value="39400000" />
<input type="hidden" name="cliente_end" value="Rua das Flores" />
<input type="hidden" name="cliente_num" value="123" />
<input type="hidden" name="cliente_compl" value="apto 69" />
<input type="hidden" name="cliente_bairro" value="." />
<input type="hidden" name="cliente_cidade" value="Montes Claros" />
<input type="hidden" name="cliente_uf" value="MG" />
<input type="hidden" name="cliente_pais" value="BRA" />
<input type="hidden" name="cliente_ddd" value="99" />

<input type="hidden" name="cliente_tel" value="43211234" />
<input type="hidden" name="cliente_email" value="josetestador@josetestador00.com" />

<input type="submit" name="ok" value="Gerar" />
</form>
  
</body>
</html>
  