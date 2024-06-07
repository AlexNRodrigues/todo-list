<?php

include_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tarefa'])) {
    $tarefa = $_POST['tarefa'];

    if (!empty($_POST['id_tarefa'])) {
        // Atualizar tarefa existente
        $id_tarefa = $_POST['id_tarefa'];
        $stmt = $conn->prepare("UPDATE tarefas SET tarefa = ? WHERE id = ?");
        $stmt->bind_param("si", $tarefa, $id_tarefa);
    } else {
        // Adicionar nova tarefa
        $stmt = $conn->prepare("INSERT INTO tarefas (tarefa) VALUES (?)");
        $stmt->bind_param("s", $tarefa);
    }

    $stmt->execute();
    $stmt->close();
}

// FInalizar tarefa
if (isset($_GET['finalizado'])) {
    $id_tarefa = $_GET['finalizado'];
    $stmt = $conn->prepare("UPDATE tarefas SET finalizado = 1 WHERE id = ?");
    $stmt->bind_param("i", $id_tarefa);
    $stmt->execute();
    $stmt->close();
}

// Remover tarefa
if (isset($_GET['delete'])) {
    $id_tarefa = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM tarefas WHERE id = ?");
    $stmt->bind_param("i", $id_tarefa);
    $stmt->execute();
    $stmt->close();
}

// Obter tarefas
$tarefas = $conn->query("SELECT id, tarefa, finalizado FROM tarefas ORDER BY id DESC");


// Obter tarefa para edição
$editar_tarefa = '';
$editar_tarefa_id = '';
if (isset($_GET['editar'])) {
    $id_tarefa = $_GET['editar'];
    $stmt_edit = $conn->prepare("SELECT tarefa FROM tarefas WHERE id = ?");
    $stmt_edit->bind_param("i", $id_tarefa);
    $stmt_edit->execute();
    $stmt_edit->bind_result($editar_tarefa);
    $stmt_edit->fetch();
    $editar_tarefa_id = $id_tarefa;
    $stmt_edit->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Todo List</title>
    <link rel="stylesheet" href="estilo.css">
</head>
<body>

    <section class="container">

        <h2>Todo List</h2>

        <form method="POST" action="">
            <input type="hidden" name="id_tarefa" value="<?= htmlspecialchars($editar_tarefa_id); ?>">
            <label for="tarefa">Nova tarefa:</label>
            <input type="text" id="tarefa" name="tarefa" value="<?= htmlspecialchars($editar_tarefa); ?>" required>
            <button type="submit">Adicionar</button>
        </form>
        <ul>
            <?php while ($row = $tarefas->fetch_assoc()): ?>
                <li>
                    <div class="tarefa">
                        <?= htmlspecialchars($row['tarefa']); ?>
                    </div>

                    <div class="botoes">

                        <a href="?editar=<?php echo $row['id']; ?>" class="editar">Editar</a>

                        <?php if ($row['finalizado'] == 1): ?>
                            <a href="#" class="finalizado">finalizado</a>
                        <?php else: ?>
                            <a href="?finalizado=<?= $row['id']; ?>" class="finalizar">Finalizar</a>
                        <?php endif; ?>

                        <a href="?delete=<?= $row['id']; ?>" class="deletar">Deletar</a>
                    </div>

                </li>
            <?php endwhile; ?>

        </ul>
    </section>
</body>
</html>

<?php $tarefas->close(); ?>
