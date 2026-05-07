<?php
require_once 'db_config.php';

function column_exists(mysqli $conn, string $table, string $column): bool {
    $stmt = $conn->prepare("SELECT COUNT(*) AS cnt FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = ? AND column_name = ?");
    if (!$stmt) {
        return false;
    }
    $stmt->bind_param("ss", $table, $column);
    $stmt->execute();
    $res = $stmt->get_result();
    $row = $res ? $res->fetch_assoc() : null;
    $stmt->close();
    return $row && (int)$row['cnt'] > 0;
}

$has_status = column_exists($conn, 'members', 'status');
$has_membership_end = column_exists($conn, 'members', 'membership_end');

$select_columns = [
    'm.member_id',
    'm.full_name',
    'm.email',
    'm.phone',
    'm.gender',
    'm.join_date',
    'mt.type_name'
];

$select_columns[] = $has_status ? 'm.status' : "NULL AS status";
$select_columns[] = $has_membership_end ? 'm.membership_end' : "NULL AS membership_end";

$query = "SELECT " . implode(', ', $select_columns) . "
          FROM members m
          LEFT JOIN membership_types mt ON m.type_id = mt.type_id";

$result = mysqli_query($conn, $query);

if (!$result) {
    die("Query Failed: " . mysqli_error($conn));
}


if (isset($_POST['action'])) {
    $action = $_POST['action'];
    $m_id = filter_var($_POST['member_id'] ?? null, FILTER_VALIDATE_INT, ["options" => ["min_range" => 1]]);

    if ($action === 'freeze') {
        if (!$has_status || !$has_membership_end) {
            header("Location: ../tier1_presentation/admin/userdata.php?error=Database+missing+membership+status+fields");
            exit();
        }
        $days = filter_var($_POST['days'] ?? null, FILTER_VALIDATE_INT, ["options" => ["min_range" => 1]]);
        if ($m_id !== false && $days !== false) {
            $freeze_stmt = $conn->prepare("CALL ApplyMemberFreeze(?, ?)");
            if ($freeze_stmt) {
                $freeze_stmt->bind_param("ii", $m_id, $days);
                $freeze_stmt->execute();
                $freeze_stmt->close();
            }
            header("Location: ../tier1_presentation/admin/userdata.php?success=Member+frozen+for+{$days}+days.");
            exit();
        }
    }

    if ($action === 'unfreeze' && $m_id !== false) {
        if (!$has_status) {
            header("Location: ../tier1_presentation/admin/userdata.php?error=Database+missing+member+status+field");
            exit();
        }
        $unfreeze_stmt = $conn->prepare("UPDATE members SET status = 'Active' WHERE member_id = ?");
        if ($unfreeze_stmt) {
            $unfreeze_stmt->bind_param("i", $m_id);
            $unfreeze_stmt->execute();
            $unfreeze_stmt->close();
        }
        header("Location: ../tier1_presentation/admin/userdata.php?success=Member+successfully+unfrozen.");
        exit();
    }
}
