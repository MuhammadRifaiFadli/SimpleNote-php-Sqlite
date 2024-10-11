<?php
require 'function.php';
createTable();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action']) && $_POST['action'] == 'add') {
        $title = $_POST['title'];
        $description = $_POST['description'];
        addNote($title, $description);
    } elseif (isset($_POST['action']) && $_POST['action'] == 'edit') {
        $id = $_POST['id'];
        $title = $_POST['title'];
        $description = $_POST['description'];
        editNote($id, $title, $description);
    }
    header('Location: index.php');
    exit();
}

$notes = getNotes();

$editNote = null;
if (isset($_GET['edit_id'])) {
    $editNote = getNoteById($_GET['edit_id']);
}

$readNote = null;
if (isset($_GET['read_id'])) {
    $readNote = getNoteById($_GET['read_id']);
}

if (isset($_GET['delete_id'])) {
    deleteNote($_GET['delete_id']);
    header('Location: index.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">  
    <title>Notes App</title>
    <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.0/css/line.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="popup-box <?php echo $editNote ? 'show' : ''; ?>">
        <div class="popup">
            <div class="content">
                <header>
                    <p><?php echo $editNote ? 'Edit Note' : 'Add New Note'; ?></p>
                    <i class="uil uil-times" onclick="document.querySelector('.popup-box').classList.remove('show');"></i>
                </header>
                <form action="index.php" method="POST">
                    <input type="hidden" name="action" value="<?php echo $editNote ? 'edit' : 'add'; ?>">
                    <?php if ($editNote): ?>
                        <input type="hidden" name="id" value="<?php echo $editNote['id']; ?>">
                    <?php endif; ?>
                    <div class="row title">
                        <label>Title</label>
                        <input type="text" name="title" required spellcheck="false" value="<?php echo $editNote['title'] ?? ''; ?>">
                    </div>
                    <div class="row description">
                        <label>Description</label>
                        <textarea name="description" required spellcheck="false"><?php echo $editNote['description'] ?? ''; ?></textarea>
                    </div>
                    <button type="submit"><?php echo $editNote ? 'Update Note' : 'Add Note'; ?></button>
                </form>
            </div>
        </div>
    </div>

    <div class="popup-box read-popup <?php echo $readNote ? 'show' : ''; ?>">
        <div class="popup">
            <div class="content">
                <header>
                    <p>Read Note</p>
                    <i class="uil uil-times" onclick="document.querySelector('.read-popup').classList.remove('show');"></i>
                </header>
                <?php if ($readNote): ?>
                    <div class="title"><?php echo htmlspecialchars($readNote['title']); ?></div>
                    <div class="description"><?php echo nl2br(htmlspecialchars($readNote['description'])); ?></div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="wrapper">
        <li class="add-box" onclick="document.querySelector('.popup-box').classList.add('show');">
            <div class="icon"><i class="uil uil-plus"></i></div>
            <p>Add new note</p>
        </li>

        <?php foreach ($notes as $note): ?>
            <li class="note">
                <div class="details">
                    <p><?php echo htmlspecialchars($note['title']); ?></p>
                    <span><?php echo nl2br(htmlspecialchars($note['description'])); ?></span>
                </div>
                <div class="bottom-content">
                    <span><?php echo date('d-m-Y H:i:s', strtotime($note['created_at'])); ?></span>
                    <div class="settings">
                        <a href="?read_id=<?php echo $note['id']; ?>">Read</a>
                        <a href="?edit_id=<?php echo $note['id']; ?>">Edit</a>
                        <a href="?delete_id=<?php echo $note['id']; ?>" onclick="return confirm('Apakah kamu yakin ingin menghapus catatan ini?');">Delete</a>
                    </div>
                </div>
            </li>
        <?php endforeach; ?>
    </div>
</body>
</html>