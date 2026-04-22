<?php
require_once 'db.php';
date_default_timezone_set('Europe/Warsaw');

$error = "";

// 1. Obsługa usuwania (musi być przed pobieraniem danych)
if (isset($_GET['usun'])) {
    $id = (int)$_GET['usun'];
    $sql = "DELETE FROM rezerwacje WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id]);
    header("Location: index.php");
    exit;
}

// 2. Obsługa formularza
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['dodaj'])) {
    $imie = trim($_POST['imie_nazwisko']);
    $zasob = $_POST['zasob_nazwa'];
    $data = $_POST['data_rezerwacji'];
    $godzina = $_POST['godzina_start'];

    $wybrany_termin = strtotime("$data $godzina");
    $teraz = time();

    // WALIDACJA 1: Czy termin nie jest w przeszłości?
    if ($wybrany_termin < $teraz) {
        $error = "Nie można rezerwować terminów z przeszłości!";
    } else {
        // 2. Walidacja: Blokada tego samego zasobu (+/- 1h)
        // Łączymy datę i godzinę w pełny format Y-m-d H:i:s
        $wybrany_timestamp = "$data $godzina:00";

        $sql_check = "SELECT COUNT(*) FROM rezerwacje 
                      WHERE zasob_nazwa = ? 
                      AND data_rezerwacji = ? 
                      AND (
                          ABS(TIMESTAMPDIFF(SECOND, CONCAT(data_rezerwacji, ' ', godzina_start), ?)) < 3600
                      )";
        
        $stmt_check = $pdo->prepare($sql_check);
        $stmt_check->execute([$zasob, $data, $wybrany_timestamp]);
        
        if ($stmt_check->fetchColumn() > 0) {
            $error = "Ten zasób jest już zarezerwowany w tym czasie (wymagana 1h odstępu).";
        } else {
            // ZAPIS DO BAZY
            $sql = "INSERT INTO rezerwacje (imie_nazwisko, zasob_nazwa, data_rezerwacji, godzina_start) 
                    VALUES (?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$imie, $zasob, $data, $godzina]);
            header("Location: index.php");
            exit;
        }
    }
}

// 3. Pobieranie danych do tabeli
$rezerwacje = $pdo->query("SELECT * FROM rezerwacje ORDER BY data_rezerwacji ASC, godzina_start ASC")->fetchAll();
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System Rezerwacji Zasobów</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f0f2f5; margin: 0; padding: 20px; }
        .container { max-width: 900px; margin: 0 auto; background: white; padding: 30px; border-radius: 15px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); }
        h1, h2 { text-align: center; color: #1a73e8; }
        
        /* Styl błędu */
        .error-msg { background: #fee2e2; color: #dc2626; padding: 12px; border-radius: 8px; border: 1px solid #fecaca; text-align: center; margin-bottom: 20px; font-weight: bold; }
        
        /* Wyśrodkowany formularz */
        form { display: flex; flex-direction: column; gap: 15px; max-width: 450px; margin: 0 auto 40px auto; padding: 20px; border: 1px solid #e5e7eb; border-radius: 10px; }
        input, select, button { padding: 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px; }
        button { background: #1a73e8; color: white; border: none; font-weight: bold; cursor: pointer; transition: 0.3s; }
        button:hover { background: #1557b0; }

        /* Tabela */
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #e5e7eb; }
        th { background: #f8fafc; color: #475569; text-transform: uppercase; font-size: 12px; }
        tr:hover { background: #f1f5f9; }
        .btn-delete { color: #ef4444; text-decoration: none; font-weight: bold; font-size: 13px; }
        .btn-delete:hover { text-decoration: underline; }
    </style>
    <script>
function validateForm() {
    const data = document.querySelector('[name="data_rezerwacji"]').value;
    const godzina = document.querySelector('[name="godzina_start"]').value;

    const now = new Date();
    const selected = new Date(data + "T" + godzina);

    if (selected < now) {
        alert("Nie możesz wybrać przeszłości!");
        return false;
    }
    return true;
}
</script>
</head>
<body>

<div class="container">
    <h1>Zarezerwuj zasób</h1>

    <?php if ($error): ?>
        <div class="error-msg"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST" onsubmit="return validateForm()">
        <input type="text" name="imie_nazwisko" placeholder="Imię i Nazwisko" required>
        <select name="zasob_nazwa" required>
            <option value="">-- Wybierz zasób --</option>
            <option value="Sala Konferencyjna 1">Sala Konferencyjna 1</option>
            <option value="Sala Informatyczna 202">Sala Informatyczna 202</option>
            <option value="Projektor Epson #4">Projektor Epson #4</option>
            <option value="Laptop MacBook Pro">Laptop MacBook Pro</option>
        </select>
        <input type="date" name="data_rezerwacji" required min="<?= date('Y-m-d') ?>">
        <input type="time" name="godzina_start" required>
        <button type="submit" name="dodaj">Zarezerwuj teraz</button>
    </form>

    <hr>

    <h2>Aktualna lista rezerwacji</h2>
    <table>
        <thead>
            <tr>
                <th>Osoba</th>
                <th>Zasób</th>
                <th>Data</th>
                <th>Godzina</th>
                <th>Akcja</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($rezerwacje)): ?>
                <tr><td colspan="5" style="text-align:center;">Brak aktywnych rezerwacji.</td></tr>
            <?php else: ?>
                <?php foreach ($rezerwacje as $r): ?>
                <tr>
                    <td><?= htmlspecialchars($r['imie_nazwisko']) ?></td>
                    <td><strong><?= htmlspecialchars($r['zasob_nazwa']) ?></strong></td>
                    <td><?= date("d-m-Y", strtotime($r['data_rezerwacji'])) ?></td>
                    <td><?= date("H:i", strtotime($r['godzina_start'])) ?></td>
                    <td>
                        <a href="index.php?usun=<?= $r['id'] ?>" 
                           class="btn-delete" 
                           onclick="return confirm('Czy na pewno chcesz usunąć tę rezerwację?')">USUŃ</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

</body>
</html>
