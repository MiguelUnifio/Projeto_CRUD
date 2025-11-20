<?php
    session_start();

    if (!isset($_SESSION['users'])) {
        $_SESSION['users'] = [];
    }

    function next_user_id() {
        $ids = array_column($_SESSION['users'], 'id');
        return $ids ? (max($ids) + 1) : 1;
    }

    $flash = '';
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $action = $_POST['action'] ?? '';

        if ($action === 'register') {
            $name = trim($_POST['name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';

            if ($name === '' || $email === '' || $password === '') {
                $_SESSION['flash'] = 'Preencha todos os campos.';
                header('Location: '.$_SERVER['PHP_SELF']);
                exit;
            }

            // Verifica e-mail duplicado
            foreach ($_SESSION['users'] as $u) {
                if (strtolower($u['email']) === strtolower($email) && (!isset($_POST['edit_id']) || $u['id'] != (int)$_POST['edit_id'])) {
                    $_SESSION['flash'] = 'E-mail já cadastrado.';
                    header('Location: '.$_SERVER['PHP_SELF']);
                    exit;
                }
            }

            if (!empty($_POST['edit_id'])) {
                $edit_id = (int)$_POST['edit_id'];
                foreach ($_SESSION['users'] as &$u) {
                    if ($u['id'] === $edit_id) {
                        $u['name'] = $name;
                        $u['email'] = $email;
                        if ($password !== '') {
                            $u['password'] = password_hash($password, PASSWORD_DEFAULT);
                        }
                        $_SESSION['flash'] = 'Usuário atualizado com sucesso.';
                        break;
                    }
                }
                unset($u);
            } else {
                $_SESSION['users'][] = [
                    'id' => next_user_id(),
                    'name' => $name,
                    'email' => $email,
                    'password' => password_hash($password, PASSWORD_DEFAULT)
                ];
                $_SESSION['flash'] = 'Usuário cadastrado com sucesso.';
            }

            header('Location: '.$_SERVER['PHP_SELF']);
            exit;
        }

        if ($action === 'cancel') {
            header('Location: '.$_SERVER['PHP_SELF']);
            exit;
        }

        if ($action === 'delete') {
            $id = (int)$_POST['id'];
            $_SESSION['users'] = array_values(array_filter($_SESSION['users'], fn($u) => $u['id'] !== $id));
            $_SESSION['flash'] = 'Usuário excluído.';
            header('Location: '.$_SERVER['PHP_SELF']);
            exit;
        }

        if ($action === 'edit') {
            $edit_id = (int)$_POST['id'];
            header('Location: '.$_SERVER['PHP_SELF'].'?edit_id='.$edit_id);
            exit;
        }
    }

    $edit_user = null;
    if (!empty($_GET['edit_id'])) {
        $id = (int)$_GET['edit_id'];
        foreach ($_SESSION['users'] as $u) {
            if ($u['id'] === $id) {
                $edit_user = $u;
                break;
            }
        }
    }
?>
<!doctype html>
<html lang="pt-BR">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width,initial-scale=1">
        <title>Cadastro de Usuários (Sessions)</title>
        <link rel="stylesheet" href="css/default.css">
    </head>
    <body>
        <div class="container">
            <h1>Cadastro de Usuários</h1>
            <?php if ($flash): ?>
                <div class="flash"><?= htmlspecialchars($flash) ?></div>
            <?php endif; ?>

            <session class="formulario">
                <form method="post">
                    <input type="hidden" name="action" value="register">
                    <?php if ($edit_user): ?>
                        <input type="hidden" name="edit_id" value="<?= intval($edit_user['id']) ?>">
                    <?php endif; ?>

                    <div class="form-row">
                        <div class="col">
                            <label for="name">Nome</label>
                            <input type="text" name="name" id="name" value="<?= htmlspecialchars($edit_user['name'] ?? '') ?>" required>
                        </div>
                        <div class="col">
                            <label for="email">E-mail</label>
                            <input type="email" name="email" id="email" value="<?= htmlspecialchars($edit_user['email'] ?? '') ?>" required>
                        </div>
                        <div class="col">
                            <label for="password"><?= $edit_user ? 'Senha (deixe em branco para manter)' : 'Senha' ?></label>
                            <input type="password" name="password" id="password" <?= $edit_user ? '' : 'required' ?>>
                        </div>
                    </div>

                    <div class="actions">
                        <button type="submit" class="btn-primary"><?= $edit_user ? 'Atualizar' : 'Cadastrar' ?></button>
                        <button type="submit" name="action" value="cancel" class="btn-ghost">Cancelar</button>
                    </div>
                </form>
            </session>
            <h2>Usuários Cadastrados</h2>
            <?php if (empty($_SESSION['users'])): ?>
                <p class="small">Nenhum usuário cadastrado.</p>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nome</th>
                            <th>E-mail</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($_SESSION['users'] as $u): ?>
                        <tr>
                            <td><?= intval($u['id']) ?></td>
                            <td><?= htmlspecialchars($u['name']) ?></td>
                            <td><?= htmlspecialchars($u['email']) ?></td>
                            <td>
                                <form method="post" style="display:inline">
                                    <input type="hidden" name="action" value="edit">
                                    <input type="hidden" name="id" value="<?= intval($u['id']) ?>">
                                    <button type="submit" class="btn-ghost">Editar</button>
                                </form>
                                <form method="post" style="display:inline" onsubmit="return confirm('Excluir usuário <?= htmlspecialchars($u['name']) ?>?');">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?= intval($u['id']) ?>">
                                    <button type="submit" class="btn-ghost">Excluir</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </body>
</html>
