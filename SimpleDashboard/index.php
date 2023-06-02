<!doctype html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

    <title>Hello, world!</title>
</head>
<style>
    h1 {color: #fff}
    .table thead th:nth-child(2),
    .table tbody td:nth-child(2),
    .table thead th:nth-child(3),
    .table tbody td:nth-child(3),
    .table thead th:nth-child(4),
    .table tbody td:nth-child(4){
        border-left: 1px solid #000;
    }
</style>

<?php
// Pfad zur XML-Datei
$xmlFile = 'data.xml';

// Überprüfen, ob die Datei existiert
if (file_exists($xmlFile)) {
    // XML-Datei öffnen und den Inhalt lesen
    $xmlContent = file_get_contents($xmlFile);
    // Überprüfen, ob das Lesen erfolgreich war
    if ($xmlContent !== false) {
        // XML-String parsen
        $xml = simplexml_load_string($xmlContent);
        // Überprüfen, ob das Parsen erfolgreich war
        if ($xml !== false) {
        } else {
            // Fehler beim Parsen der XML-Datei
            echo 'Fehler beim Parsen der XML-Datei.';
        }
    } else {
        // Fehler beim Lesen der XML-Datei
        echo 'Fehler beim Lesen der XML-Datei.';
    }
} else {
    // Die XML-Datei existiert nicht
    echo 'Die XML-Datei existiert nicht.';
}
?>
<body>
<div class="container-fluid bg-dark text-center py-2">
    <h1 class="my-auto" style="font-size: 24px">Simple XML Dashboard</h1>
</div>
<div class="container-fluid text-center py-2" style="background: rgba(0, 0, 0, 0.05)">
    <div class="container py-3">
        <div class="row">
            <div class="col-6 border m-auto py-4 shadow-sm">
                <h3>10</h3>
                <span>Total</span>
            </div>
            <div class="col-6">
                <button id="alertCheck" class="btn btn-lg border fw-bold m-auto py-4 shadow-lg w-100">
                    <h3>2</h3>
                    <span>Alerts 🚨</span>
                </button>
            </div>
        </div>
    </div>
    <div class="container pt-2">
        <div class="row">
            <div class="col-md-3 my-auto py-3">
                <input type="checkbox" id="prodCheck" style="transform: scale(2.5);"><span class="mx-3">Alerts 🚨</span>
            </div>
            <div class="col-md-9 my-auto">
                <input type="text" id="filter-input" class="form-control py-2" placeholder="Filter...">
            </div>
        </div>
    </div>
</div>
<div class="container mt-2">
    <div class="row">
        <div class="col-12 mt-3">
            <table id='contenttable' class="table table-striped">
                <thead>
                <tr>
                    <th scope="col" id="header-id"># <span class="sort-icon" id="sort-icon-id"></span></th>
                    <th scope="col" id="header-name">⚫ Name <span class="sort-icon" id="sort-icon-name"></span></th>
                    <th scope="col" id="header-status" class="text-center">⚫ Status <span class="sort-icon" id="sort-icon-status"></span></th>
                    <th scope="col" id="header-dev" class="text-center">⚫ DEV results <span class="sort-icon" id="sort-icon-dev"></span></th>
                    <th scope="col" id="header-test" class="text-center">⚫ TEST results <span class="sort-icon" id="sort-icon-test"></span></th>
                    <th scope="col" id="header-prod" class="text-center">⚫ PROD results <span class="sort-icon" id="sort-icon-prod"></span></th>
                </tr>
                </thead>

                <tbody>
                <?php
                // Daten aus dem XML-Objekt extrahieren und in ein Array übertragen
                $data = [];
                foreach ($xml->object as $object) {
                    $devRows = (int)$object->DevRows;
                    $qsRows = (int)$object->QsRows;
                    $prodRows = (int)$object->ProdRows;

                    $status = $devRows > 0 && $qsRows > 0 && $prodRows > 0 ? '✔️' : ($prodRows === 0 ? '🚨' : '🚨️');

                    $row = [
                        'Database' => (string)$object->Database,
                        'Schema' => (string)$object->Schema,
                        'Table' => (string)$object->Table,
                        'Status' => $status,
                        'DevRows' => (string)$object->DevRows,
                        'QsRows' => (string)$object->QsRows,
                        'ProdRows' => (string)$object->ProdRows
                    ];
                    $data[] = $row;
                }

                // Funktion zum Vergleich der Daten basierend auf den Spalten 'Database', 'Schema' und 'Table'
                function compareData($a, $b) {
                    $result = strcmp($a['Database'], $b['Database']);
                    if ($result == 0) {
                        $result = strcmp($a['Schema'], $b['Schema']);
                        if ($result == 0) {
                            $result = strcmp($a['Table'], $b['Table']);
                        }
                    }
                    return $result;
                }

                // Array nach den Spalten 'Database', 'Schema' und 'Table' sortieren
                usort($data, 'compareData');

                // HTML-Code mit HEREDOC-Syntax einfügen
                $counter = 1; // Initialisierung des Counters
                foreach ($data as $row) {
                    $displayClass = $row['Status'] === '✔️' ? 'class="status-ok"' : '';
                    $htmlCode = <<<HTML
                        <tr $displayClass>
                            <th scope="row">$counter</th>
                            <td>{$row['Database']}.{$row['Schema']}.{$row['Table']}</td>
                            <td class="text-center">{$row['Status']}</td>
                            <td class="text-center">{$row['DevRows']}</td>
                            <td class="text-center">{$row['QsRows']}</td>
                            <td class="text-center">{$row['ProdRows']}</td>
                        </tr>
                        HTML;
                    echo $htmlCode;
                    $counter++; // Erhöhung des Counters nach jeder Schleifeniteration
                }
                ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</div>

<!-- Optional JavaScript; choose one of the two! -->

<!-- Option 1: Bootstrap Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>

<!-- Option 2: Separate Popper and Bootstrap JS -->
<!--
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous"></script>
-->
<script>
    // Funktion zum Ändern der Sichtbarkeit der Zeilen
    function changeRowVisibility(isChecked) {
        let rows = document.querySelectorAll('.status-ok');

        rows.forEach(row => {
            row.style.display = isChecked ? 'none' : 'table-row';
        });
    }

    // JavaScript zum Erkennen von Änderungen an der Checkbox
    document.getElementById('prodCheck').addEventListener('change', function() {
        let isChecked = this.checked;
        changeRowVisibility(isChecked);
    });

    // JavaScript zum Erkennen von Klicks auf den Button
    document.getElementById('alertCheck').addEventListener('click', function() {
        let checkbox = document.getElementById('prodCheck');
        checkbox.checked = !checkbox.checked; // Optional, wenn Sie möchten, dass der Button die Checkbox umschaltet
        let isChecked = checkbox.checked;
        changeRowVisibility(isChecked);
    });
</script>
<script>
    // JavaScript-Code zum Filtern der Tabelle
    document.addEventListener('DOMContentLoaded', function () {
        var filterInput = document.getElementById('filter-input');

        filterInput.addEventListener('input', function () {
            var filterValue = this.value.toLowerCase();
            var tableRows = document.querySelectorAll('.table tbody tr');

            tableRows.forEach(function (row) {
                var rowData = row.querySelector('td:nth-child(2)').textContent.toLowerCase();

                if (rowData.includes(filterValue)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    });
</script>

<script>
    // JavaScript to detect changes on the checkbox
    document.getElementById('prodCheck').addEventListener('change', function() {
        let isChecked = this.checked;
        let rows = document.querySelectorAll('.status-ok');

        rows.forEach(row => {
            row.style.display = isChecked ? 'none' : 'table-row';
        });
    });
</script>

<script>
    // Define sorting order for each header
    const sortOrder = {
        "header-id": true,
        "header-name": true,
        "header-status": true,
        "header-dev": true,
        "header-test": true,
        "header-prod": true
    }

    // Function to sort the table
    function sortTable(headerId, columnIndex) {
        const table = document.getElementById('contenttable');
        const tbody = table.tBodies[0];  // Get the tbody
        const rows = Array.from(tbody.rows);

        // Sort rows based on column content
        rows.sort((a, b) => {
            const aValue = a.cells[columnIndex].innerText;
            const bValue = b.cells[columnIndex].innerText;

            return sortOrder[headerId] ? aValue.localeCompare(bValue) : bValue.localeCompare(aValue);
        });

        // Reverse the sort order for the next sort
        sortOrder[headerId] = !sortOrder[headerId];

        // Re-append rows to the tbody
        rows.forEach(row => tbody.appendChild(row));
    }

    // Add click event listeners to headers
    document.querySelectorAll('th').forEach((header, index) => {
        header.addEventListener('click', () => sortTable(header.id, index));
    });
</script>


</body>
</html>