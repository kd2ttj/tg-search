<?php
// Process form submission and search
$search_results = [];
$search_term = isset($_POST['search']) ? trim($_POST['search']) : '';
$exact_match = isset($_POST['exact_match']) ? true : false;

// Load and search CSV if there's a search term
if (!empty($search_term)) {
    // Open the CSV file (assuming it's named 'data.csv' in the same directory)
    if (($handle = fopen("bm.csv", "r")) !== FALSE) {
        $data = [];
        
        // Read CSV into array
        while (($row = fgetcsv($handle, 1000, ",")) !== FALSE) {
            $data[] = [
                'row' => $row[0],
                'channel' => $row[1],
                'name' => $row[2]
            ];
        }
        fclose($handle);

        // Search through the array
        foreach ($data as $entry) {
            if ($exact_match) {
                // Exact match comparison
                if ($entry['channel'] === $search_term || $entry['name'] === $search_term) {
                    $search_results[] = $entry;
                }
            } else {
                // Partial match comparison (case-insensitive)
                if (stripos($entry['channel'], $search_term) !== false || 
                    stripos($entry['name'], $search_term) !== false) {
                    $search_results[] = $entry;
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>CSV Search</title>
    <style>
        .results {
            margin-top: 20px;
        }
        table {
            border-collapse: collapse;
            width: 100%;
            max-width: 600px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .form-group {
            margin: 10px 0;
        }
    </style>
</head>
<body>
    <h2>BM Talkgroups</h2>
    <form method="post" action="">
        <div class="form-group">
            <input type="text" name="search" value="<?php echo htmlspecialchars($search_term); ?>" 
                   placeholder="Search by channel or name">
            <input type="submit" value="Search">
        </div>
        <div class="form-group">
            <input type="checkbox" name="exact_match" id="exact_match" 
                   <?php echo $exact_match ? 'checked' : ''; ?>>
            <label for="exact_match">Exact match only</label>
        </div>
    </form>

    <?php if (!empty($search_term)): ?>
        <div class="results">
            <h3>Search Results for "<?php echo htmlspecialchars($search_term); ?>" 
                <?php echo $exact_match ? '(Exact Match)' : '(Partial Match)'; ?>:</h3>
            <?php if (empty($search_results)): ?>
                <p>No matches found.</p>
            <?php else: ?>
                              <table>
                    <tr>
                        <th>Row</th>
                        <th>Channel</th>
                        <th>Name</th>
                    </tr>
                    <?php foreach ($search_results as $result): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($result['row']); ?></td>
                            <td><?php echo htmlspecialchars($result['channel']); ?></td>
                            <td><?php echo htmlspecialchars($result['name']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            <?php endif; ?>
        </div>
    <?php endif; ?>
    <a href="index.php" >index</a><br />
</body>
</html>