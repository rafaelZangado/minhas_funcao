<script src="js/FormataMoeda.js"></script>

<?php
  ini_set('display_errors', 'off');

    class Meu_banco {
        //parent::conexao();

        protected $conn;
        function __construct ($banco = 'comeia_sistemas') {
            $user = 'localhost';
            $root = 'root';
            $senha = '';

            $this->conn = new mysqli($user, $root, $senha, $banco);
            if( $this->conn->connect_error) {
                die ("erro". $this->conn->connect_error);
            }
        }   
    }

    class Tabela extends Meu_banco {

        //listando dados da tabela
        function listar_dados ($dados = []){
            $minha_tabela = [];

            $select = "SELECT * FROM ".$dados['tabela'];
            $result = $this->conn->query($select);

            while ($row = $result->fetch_array()) {
                $minha_tabela [ ] = $row;
            }
            return $minha_tabela;
        }


        //inserindo dados na tabela
        function salvando_dados () {
            $salvar = $_GET;
            if ( empty($salvar)) {
                die("campo vazipo");
            }
            $insert = "INSERT INTO tabela_teste
            (nome_qualquer, preco)
            VALUE (?, ?)
            ";

            $stmt = $this->conn->prepare($insert);
            $params = [
                $salvar['nome_qualquer'],
                $salvar['preco'],
            ];
            $stmt->bind_param("ss",...$params);
            if($stmt->execute ()) {
                unset ($dados);
            }
        }
        //deletando dados da tabela
        function deletar_dados () {
            if($_GET['excluir']) {
                $sqlExcluir = "DELETE FROM tabela_teste WHERE id = ? ";
                $stmt = $this->conn->prepare( $sqlExcluir);
                $stmt->bind_param("i", $_GET['excluir']);
                $stmt->execute();
            } else {
                print 'erro ao deletar';
            }
        }
    }

    $Tabela = new Tabela();        
    $tabelas = $Tabela->listar_dados([
        'tabela' => 'tabela_teste',
    ]);

    #$Tabela->salvando_dados();
    $Tabela->deletar_dados();

   
?>

    <form method="GET">
        Nome qualquer: 
        <input type="text" name="nome_qualquer"><br>
        R$ preço: 
       
        <input type="text" class="form-control" name="preco"
        onkeydown="FormataMoeda(this,10,event)" id="">
        <button>salvar</button>
    </form>    
    <hr>

        <table class="table table-striped">
            <thead>
                <th> nome |</th>
                <th> R$ | </th>
                <th> Deletar dados </th>
            </thead>
            <tbody>       
                <?php 
               
                    foreach ($tabelas as $valor) {     
                        $total =  $total + number_format($valor['preco'],2,",",".");              
                    ?> 
                        <tr>
                            <td><?=$valor['nome_qualquer']?></td>
                            <td><?=number_format($valor['preco'],2,",",".");?></td>
                            <td><a href="teste.php?excluir=<?=$valor['id']?>">Deletar</a></td>
                            <td><a href="teste.php?editar=<?=$valor['id']?>">Editar</a></td>
                        </tr>
                        <?php 
                    } 
                ?>           
            </tbody>
        </table>
        <h2>Total: R$ <?=number_format($total,2,",",".")?></h2>
    <hr>
    
    <?php
        if ($_GET['editar']) {
            
            class Editar extends Tabela {
                function editar_dados ($dados = []) {
                    $atualizar = [];
                    //$update = "UPDATE tabela_teste".$dados['tabela']."WHERE id='$id'";
                    $this->listar_dados([
                        'tabela' => 'tabela_teste',
                    ]);                
                    
                }
            }
        }

        $Editar = new Editar();
       print $Editar->editar_dados();
    ?>