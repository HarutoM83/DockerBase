<?php
// 1. データベース接続設定ファイルをインクルード
require_once 'common.php';

// ログインしていない場合は処理を中断
if (empty($_SESSION['user_id'])) {
    $title = 'エラー';
    require_once 'header.php';
    echo "<h1>エラー</h1><p>アイテムを強化するには、まず<a href='select_user.php'>ユーザーを選択</a>してください。</p>";
    require_once 'footer.php';
    exit;
}
$current_user_id = $_SESSION['user_id'];

try {
    // PDO接続の確立
    $pdo = getDb();

    // 2. 2枚以上所持しているアイテムを取得するSQLクエリ
    // user_items から item_id ごとにカウントし、2個以上のものに絞り込み
    $sql = "
        SELECT
            ui.item_id,
            i.item_name,
            COUNT(ui.item_id) AS total_count
        FROM
            user_items ui
        JOIN
            items i ON ui.item_id = i.item_id
        WHERE
            ui.user_id = :user_id
        GROUP BY
            ui.item_id, i.item_name
        HAVING
            COUNT(ui.item_id) >= 2
        ORDER BY
            i.item_id DESC
    ";

    // 3. プリペアドステートメントの実行
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':user_id', $current_user_id, PDO::PARAM_INT);
    $stmt->execute();
    
    // 結果の取得
    $target_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    // データベース接続またはクエリ実行エラー
    die("データベースエラー: " . $e->getMessage());
}
$title = '画面① 強化対象アイテム選択';
require_once 'header.php';
?>
    <h1>強化対象アイテム選択</h1>

    <?php if (empty($target_items)): ?>
        <p>現在、強化のベースとして利用できるアイテムはありません。（同じアイテムIDを2個以上所持していません。）</p>
    <?php else: ?>
        <p>強化のベースとして利用するアイテムを選択してください。</p>
        <table>
            <thead>
                <tr>
                    <th>アイテムID</th>
                    <th>アイテム名</th>
                    <th>所持数</th>
                    <th>選択</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($target_items as $item): ?>
                <tr>
                    <td><?php echo htmlspecialchars($item['item_id']); ?></td>
                    <td><?php echo htmlspecialchars($item['item_name']); ?></td>
                    <td><?php echo htmlspecialchars($item['total_count']); ?></td>
                    <td>
                        <a href="forge_select.php?target_item_id=<?php echo htmlspecialchars($item['item_id']); ?>">
                            強化する
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
<div style="text-align: center; margin-top: 20px;">
    <a href="menu.php">メニューに戻る</a>
</div>
<?php
require_once 'footer.php';
?>