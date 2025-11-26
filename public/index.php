<?php
    session_start();
    if (!isset($_SESSION['users'])) {
        $_SESSION['users'] = [];
    }

    if (isset($_POST['register'])) {
        $user = [
            'id' => uniqid(),
            'name' => $_POST['name'],
            'email' => $_POST['email'],
            'password' => $_POST['password'],
        ];
        $_SESSION['users'][] = $user;
    }

    if (isset($_GET['delete'])) {
        $id = $_GET['delete'];
        $_SESSION['users'] = array_filter($_SESSION['users'], fn($u) => $u['id'] !== $id);
    }

    $editUser = null;
    if (isset($_GET['edit'])) {
        foreach ($_SESSION['users'] as $u) {
            if ($u['id'] === $_GET['edit']) {
                $editUser = $u;
                break;
            }
        }
    }

    if (isset($_POST['update'])) {
        foreach ($_SESSION['users'] as &$u) {
            if ($u['id'] === $_POST['id']) {
                $u['name'] = $_POST['name'];
                $u['email'] = $_POST['email'];
                $u['password'] = $_POST['password'];
            }
        }
    }
?>

<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <meta charset="UTF-8">
        <title>Cadastre-se - NeuroGenius</title>
        </head>
    <body>
        <div class="container">
            <div class="left"></div>
            <div class="right">
                <div class="form-box">
                    <center>
                        <img src="imagens/logo.png" width="150px">
                    </center>
                    <h2>Cadastre-se</h2>
                    <form method="POST">
                        <?php if ($editUser): ?>
                            <input type="hidden" name="id" value="<?= $editUser['id'] ?>">
                        <?php endif; ?>

                        <input type="text" name="name" placeholder="Nome" value="<?= $editUser['name'] ?? '' ?>" required>
                        <input type="email" name="email" placeholder="Email" value="<?= $editUser['email'] ?? '' ?>" required>
                        <input type="password" name="password" placeholder="Senha" value="<?= $editUser['password'] ?? '' ?>" required>

                        <?php if ($editUser): ?>
                            <button type="submit" name="update">Salvar alterações</button>
                        <?php else: ?>
                            <button type="submit" name="register">Cadastrar</button>
                        <?php endif; ?>
                    </form>

                    <button onclick="document.getElementById('table').style.display='block'">Mostrar Tabela</button>
                </div>
            </div>
        </div>

        <div id="table" class="table-box" style="display:none;">
            <h3>Usuários cadastrados</h3>
            <table>
                <tr><th>Nome</th><th>Email</th><th>Ações</th></tr>
                <?php foreach ($_SESSION['users'] as $u): ?>
                    <tr>
                        <td><?= $u['name'] ?></td>
                        <td><?= $u['email'] ?></td>
                        <td>
                            <a href="?edit=<?= $u['id'] ?>">Editar</a> |
                            <a href="?delete=<?= $u['id'] ?>" onclick="return confirm('Confirmar exclusão?')">Excluir</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>
    </body>
</html>
