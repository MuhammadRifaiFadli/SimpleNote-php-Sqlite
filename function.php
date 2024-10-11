<?php
// fungsi buat koneksi database
function connectDB() {
    return new SQLite3('note.sqlite');
}

function createTable() {
    $db = connectDB();
    $db->exec("CREATE TABLE IF NOT EXISTS notes (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        title TEXT NOT NULL,
        description TEXT NOT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )");
}

// Fungsi tambah catatan
function addNote($title, $description) {
  $db = connectDB();
 //waktu jakarta
  $current_time = (new DateTime('now', new DateTimeZone('Asia/Jakarta')))->format('Y-m-d H:i:s');

  $stmt = $db->prepare('INSERT INTO notes (title, description, created_at) VALUES (:title, :description, :created_at)');
  $stmt->bindValue(':title', $title, SQLITE3_TEXT);
  $stmt->bindValue(':description', $description, SQLITE3_TEXT);
  $stmt->bindValue(':created_at', $current_time, SQLITE3_TEXT);
  return $stmt->execute();
}


// Fungsi mengambil semua catatan
function getNotes($limit = 0) {
    $db = connectDB();
    
    // Query untuk mendapatkan catatan dengan opsi limit
    $queryStr = 'SELECT * FROM notes ORDER BY id DESC';
    if ($limit > 0) {
        $queryStr .= ' LIMIT ' . $limit;
    }
    
    $query = $db->query($queryStr);
    
    // Ambil data secara iteratif agar tidak menyimpan semuanya sekaligus di memori
    $notes = [];
    while ($row = $query->fetchArray(SQLITE3_ASSOC)) {
        $notes[] = $row;
    }
    
    return $notes;
}

// Fungsi untuk mendapatkan catatan berdasarkan ID
function getNoteById($id) {
    $db = connectDB();
    $stmt = $db->prepare("SELECT * FROM notes WHERE id = :id LIMIT 1");
    $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
    $result = $stmt->execute();
    
    // Hanya ambil satu catatan
    return $result->fetchArray(SQLITE3_ASSOC);
}

// Fungsi untuk mengedit catatan
function editNote($id, $title, $description) {
    $db = connectDB();
    $stmt = $db->prepare("UPDATE notes SET title = :title, description = :description WHERE id = :id");
    $stmt->bindValue(':title', $title, SQLITE3_TEXT);
    $stmt->bindValue(':description', $description, SQLITE3_TEXT);
    $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
    return $stmt->execute();
}

// Fungsi untuk menghapus 
function deleteNote($id) {
    $db = connectDB();
    $stmt = $db->prepare('DELETE FROM notes WHERE id = :id');
    $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
    return $stmt->execute();
}

// Cek jika tabel sudah ada
createTable();
?>
