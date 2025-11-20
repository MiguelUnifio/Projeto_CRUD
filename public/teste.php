<?php
    session_start();

    if (!isset($_SESSION['dados'])) {
        $_SESSION['dados'] = [];
    }

    if (isset($_POST['cadastrar'])) {
        $nome = $_POST['nome'];
        $email = $_POST['email'];

        if (isset($_POST['indice']) && $_POST['indice'] !== '') {
            $i = (int)$_POST['indice'];
            if (isset($_SESSION['dados'][$i])) {
                $_SESSION['dados'][$i] = [$nome, $email];
            }
        } else {
            $_SESSION['dados'][] = [$nome, $email];
        }

        header('Location: '.$_SERVER['PHP_SELF']);
        exit;
    }

    
    if (isset($_GET['excluir'])) {
        $indice = (int)$_GET['excluir'];
        if (isset($_SESSION['dados'][$indice])) {
            unset($_SESSION['dados'][$indice]);
        }
        $_SESSION['dados'] = array_values($_SESSION['dados']);
        header('Location: '.$_SERVER['PHP_SELF']);
        exit;
    }

    $editNome = '';
    $editEmail = '';
    $editIndice = '';
    if (isset($_GET['editar'])) {
        $indice = (int)$_GET['editar'];
        if (isset($_SESSION['dados'][$indice])) {
            $editNome = htmlspecialchars($_SESSION['dados'][$indice][0], ENT_QUOTES);
            $editEmail = htmlspecialchars($_SESSION['dados'][$indice][1], ENT_QUOTES);
            $editIndice = $indice;
        }
    }
?>
<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <meta charset="UTF-8">
        <title>CRUD mais simples com editar</title>
    </head>
    <body>
        <h2>Cadastro</h2>
        <form method="post">
            <input type="hidden" name="indice" value="<?= $editIndice ?>">
            Nome: <input type="text" name="nome" required value="<?= $editNome ?>">
            Email: <input type="email" name="email" required value="<?= $editEmail ?>">
            <button type="submit" name="cadastrar"><?= $editIndice === '' ? 'Cadastrar' : 'Atualizar' ?></button>
            <a href="<?= $_SERVER['PHP_SELF'] ?>">Cancelar</a>
        </form>
        <h3>Usuários Cadastrados</h3>
        <table>
        <tr><th>#</th><th>Nome</th><th>Email</th><th>Ação</th></tr>
        <?php
            if (empty($_SESSION['dados'])) {
                echo "<tr><td colspan='4'>Nenhum cadastro ainda</td></tr>";
            } else {
                foreach ($_SESSION['dados'] as $i => $d) {
                    $nome = htmlspecialchars($d[0], ENT_QUOTES);
                    $email = htmlspecialchars($d[1], ENT_QUOTES);
                    echo "<tr>";
                    echo "<td>$i</td>";
                    echo "<td>$nome</td>";
                    echo "<td>$email</td>";
                    echo "<td>";
                    echo "<a href='?editar=$i'>Editar</a>";
                    echo "<a href='?excluir=$i' onclick=\"return confirm('Excluir?')\">Excluir</a>";
                    echo "</td>";
                    echo "</tr>";
                }
            }
        ?>
        </table>
    </body>
</html>
