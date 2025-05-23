<?php
$file = 'data.txt';
$page = $_GET['page'] ?? 'input';

$data = [];
if (file_exists($file)) {
    $rows = explode("\n", trim(file_get_contents($file)));
    foreach ($rows as $i => $row) {
        if ($row !== '') {
            $data[] = explode('|', $row);
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $page === 'input') {
    $nama = trim($_POST['nama'] ?? '');
    $nim = trim($_POST['nim'] ?? '');
    $fakultas = trim($_POST['fakultas'] ?? '');
    $prodi = trim($_POST['prodi'] ?? '');
    $sks = intval($_POST['sks'] ?? 0);
    $total = intval($_POST['total'] ?? 0);

    if ($nama && is_numeric($nim) && $fakultas && $prodi && $sks > 0 && $total > 0) {
        $data[] = [$nama, $nim, $fakultas, $prodi, $sks, $total];
        file_put_contents($file, implode("\n", array_map(fn($d) => implode('|', $d), $data)));
        header("Location: ?page=admin");
        exit;
    } else {
        $error = "Semua data harus diisi dengan benar.";
    }
}

if ($page === 'hapus' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    if (isset($data[$id])) {
        unset($data[$id]);
        file_put_contents($file, implode("\n", array_map(fn($d) => implode('|', $d), $data)));
        header("Location: ?page=admin");
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $page === 'edit' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    if (isset($data[$id])) {
        $nama = trim($_POST['nama'] ?? '');
        $nim = trim($_POST['nim'] ?? '');
        $fakultas = trim($_POST['fakultas'] ?? '');
        $prodi = trim($_POST['prodi'] ?? '');
        $sks = intval($_POST['sks'] ?? 0);
        $total = intval($_POST['total'] ?? 0);
        if ($nama && is_numeric($nim) && $fakultas && $prodi && $sks > 0 && $total > 0) {
            $data[$id] = [$nama, $nim, $fakultas, $prodi, $sks, $total];
            file_put_contents($file, implode("\n", array_map(fn($d) => implode('|', $d), $data)));
            header("Location: ?page=admin");
            exit;
        } else {
            $error = "Semua data harus diisi dengan benar.";
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Aplikasi Mahasiswa</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background: #f4f7fc;
      color: #333;
      margin: 0;
      padding: 0;
      text-align: center;
    }

    header img {
  max-width: 100px;
  margin-top: 20px;
  display: block;
  margin-left: auto;
  margin-right: auto;
}
    }

    h2 {
      margin: 10px 0;
      font-size: 24px;
    }

    nav a {
      text-decoration: none;
      color: #1a73e8;
      margin: 0 10px;
      font-weight: bold;
    }

    .container {
      background: #fff;
      max-width: 600px;
      margin: 20px auto;
      padding: 30px;
      border-radius: 10px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
      text-align: left;
    }

    form input, select {
      width: 100%; /* tambahkan baris ini */
      padding: 10px;
      height: 38px;
      border-radius: 5px;
      border: 1px solid #ccc;
      box-sizing: border-box;
    }

    button {
      background: #1a73e8;
      color: white;
      border: none;
      padding: 10px 20px;
      border-radius: 5px;
      cursor: pointer;
      font-weight: bold;
    }

    button:hover {
      background: #155cc0;
    }

    table {
      width: 90%;
      margin: 20px auto;
      border-collapse: collapse;
    }

    th, td {
      padding: 10px;
      border: 1px solid #ddd;
    }

    th {
      background: #1a73e8;
      color: white;
    }

    tr:nth-child(even) {
      background-color: #f2f2f2;
    }

    .error {
      color: red;
      font-weight: bold;
      text-align: center;
    }

    @media (max-width: 600px) {
      .container, table {
        width: 95%;
      }

      form input, select, button {
        font-size: 16px;
      }
    }
  </style>
</head>
<body>
  <header>
    <img src="UNPAM_logo1.png" alt="Logo Universitas">
    <h2>Aplikasi Data Mahasiswa</h2>
    <nav>
      <a href="?page=input">Tambah Data</a> |
      <a href="?page=admin">Lihat Data</a>
    </nav>
  </header>
  <hr>

  <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>

  <?php if ($page === 'input'): ?>
    <div class="container">
      <h3>Input Data</h3>
      <form method="POST">
        Nama: <input type="text" name="nama" required><br>
        NIM: <input type="text" name="nim" required pattern="\d+"><br>
        Fakultas:
        <select name="fakultas">
          <option>HUKUM</option><option>SASTRA</option><option>ILMU SOSIAL</option>
        </select><br>
        Prodi: <input type="text" name="prodi" required><br>
        Jumlah SKS: <input type="number" name="sks" required><br>
        Total Biaya: <input type="number" name="total" required><br>
        <button type="submit">Simpan</button>
      </form>
    </div>

  <?php elseif ($page === 'admin'): ?>
    <h3>Data Mahasiswa</h3>
    <table>
      <tr><th>No</th><th>Nama</th><th>NIM</th><th>Fakultas</th><th>Prodi</th><th>SKS</th><th>Total</th><th>Aksi</th></tr>
      <?php foreach ($data as $i => $m): ?>
        <tr>
          <td><?= $i+1 ?></td><td><?= $m[0] ?></td><td><?= $m[1] ?></td><td><?= $m[2] ?></td>
          <td><?= $m[3] ?></td><td><?= $m[4] ?></td><td><?= $m[5] ?></td>
          <td>
            <a href="?page=edit&id=<?= $i ?>">Edit</a> |
            <a href="?page=hapus&id=<?= $i ?>" onclick="return confirm('Yakin hapus?')">Hapus</a>
          </td>
        </tr>
      <?php endforeach; ?>
    </table>

  <?php elseif ($page === 'edit' && isset($_GET['id'])): ?>
    <?php $id = intval($_GET['id']); $m = $data[$id] ?? null; ?>
    <?php if ($m): ?>
      <div class="container">
        <h3>Edit Data</h3>
        <form method="POST">
          Nama: <input type="text" name="nama" value="<?= $m[0] ?>" required><br>
          NIM: <input type="text" name="nim" value="<?= $m[1] ?>" required><br>
          Fakultas:
          <select name="fakultas">
            <option value="HUKUM" <?= $m[2]=="HUKUM"?"selected":"" ?>>HUKUM</option>
            <option value="SASTRA" <?= $m[2]=="SASTRA"?"selected":"" ?>>SASTRA</option>
            <option value="ILMU SOSIAL" <?= $m[2]=="ILMU SOSIAL"?"selected":"" ?>>ILMU SOSIAL</option>
          </select><br>
          Prodi: <input type="text" name="prodi" value="<?= $m[3] ?>" required><br>
          Jumlah SKS: <input type="number" name="sks" value="<?= $m[4] ?>" required><br>
          Total Biaya: <input type="number" name="total" value="<?= $m[5] ?>" required><br>
          <button type="submit">Update</button>
        </form>
      </div>
    <?php else: ?>
      <p>Data tidak ditemukan.</p>
    <?php endif; ?>
  <?php endif; ?>
</body>
</html>
