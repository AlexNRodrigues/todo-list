<?php

include_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tarefa'])) {
    $tarefa = $_POST['tarefa'];
    $stmt = $conn->prepare("INSERT INTO tarefas (tarefa) VALUES (?)");
    $stmt->bind_param("s", $tarefa);
    $stmt->execute();
    $stmt->close();
}

if (isset($_GET['finalizado'])) {
    $id_tarefa = $_GET['finalizado'];
    $stmt = $conn->prepare("UPDATE tarefas SET finalizado = 1 WHERE id = ?");
    $stmt->bind_param("i", $id_tarefa);
    $stmt->execute();
    $stmt->close();
}

if (isset($_GET['delete'])) {
    $id_tarefa = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM tarefas WHERE id = ?");
    $stmt->bind_param("i", $id_tarefa);
    $stmt->execute();
    $stmt->close();
}

$stmt = $conn->query("SELECT id, tarefa, finalizado FROM tarefas ORDER BY id DESC");

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
            <label for="tarefa">Nova tarefa:</label>
            <input type="text" id="tarefa" name="tarefa" required>
            <button type="submit">Adicionar</button>
        </form>
        <ul>
            <?php while ($row = $stmt->fetch_assoc()): ?>
                <li>
                    <div class="tarefa">
                        <?= htmlspecialchars($row['tarefa']); ?>
                    </div>

                    <div class="botoes">

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

<?php
$stmt->close();
$conn->close();
?>
