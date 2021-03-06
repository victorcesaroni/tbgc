<?php
include 'header.php';
?>

<?php
if (!isset($_SESSION['logado'])) {
	 header('Location: index.php');
}
?>

if (!($_SESSION['tipo'] == 1 || $_SESSION['tipo'] == 2)) {
	sem_permissao();
}

<section class="home container" id="consultarEstoque">

  <div class="row">
      <div class="col-sm-6 col-sm-offset-3">
        <h2>
			Lista de Requisições
        </h2>
      </div>
  </div>
  <div class="row">
  	<div class="col-sm-8 col-sm-offset-2">
  		<form action="" method="" class="boxCadastro">
  			<br>
  			<div class="row">
                <div class="col-sm-10 col-sm-offset-1">
                    <div class="row cabecalhoEstoque">
                    <div class="col-md-1">Núm.</div>
                    <div class="col-md-2">Data</div>                                 
                    <div class="col-md-8">Observação</div>
                    <div class="col-sm-1">Impr.</div>
                    </div>
                </div>
                <div class="listaEstoque col-sm-9 col-sm-offset-1" id="chapasDePapelao">
                    <ul class="list-group">
                    <?php
                        $rows = db_select("SELECT cod, date_format(data, '%d/%m/%Y') as data_fmt, observacao FROM requisicoes ORDER BY data desc");

                        foreach ($rows as $row) {
                            $observacao = ($row['observacao'] !== "" ? utf8_decode($row['observacao']) : "NC");                           

                            if (strlen($observacao) > 46)
                            {
                                $observacao = substr($observacao, 0, 46);
                                $observacao .= "...";
                            }
                            
                            echo "<li class=\"list-group-item\">
                                    <div class=\"row tipoMaterial\">
                                        <div class=\"col-md-1\">#$row[cod]</div> 
                                        <div class=\"col-md-2\">$row[data_fmt]</div>                                       
                                        <div class=\"col-md-8\">" . $observacao . "</div>
                                        <div class=\"col-sm-1 tipoMaterialLast\">
                                            <a href=\"impressaorequisicao.php?cod=$row[cod]\">
                                                <i class=\"fa fa-print\" aria-hidden=\"true\"></i>
                                            </a>
                                        </div>  
                                    </div>
                                </li>";
                        }
                    ?>                     
                    </ul>
                </div>
  			</div>

  			<br>
  			<div class="row">
  				<div class="col-sm-10 col-sm-offset-1">
  					<div class="modal fade" id="modalCadastroDialog">
  						<div class="modal-dialog">
  							<div class="modal-content">
  							<div class="modal-header">
  								<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
  								<h4 class="modal-title">Informação</h4>
  							</div>
  							<div class="modal-body">
  								<p><div id="modalCadastroMessage"></div></p>
  							</div>
  							<div class="modal-footer">
  								<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
  							</div>
  							</div><!-- /.modal-content -->
  						</div><!-- /.modal-dialog -->
  					</div><!-- /.modal -->
  				</div>
  			</div>
  		<form>
  	</div>
  </div>
</section>

<?php
include 'footer.php';
?>
