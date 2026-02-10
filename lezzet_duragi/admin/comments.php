<?php
require_once 'includes/header.php';

// --- CEVAPLAMA İŞLEMİ ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_reply'])) {
    $comment_id = $_POST['comment_id'];
    $reply_text = trim($_POST['reply_text']);
    
    $stmt = $pdo->prepare("UPDATE comments SET reply = ? WHERE id = ?");
    $stmt->execute([$reply_text, $comment_id]);
    
    header("Location: comments.php?success=cevaplandi");
    exit;
}

// --- ONAYLAMA İŞLEMİ ---
if (isset($_GET['approve'])) {
    $id = $_GET['approve'];
    $stmt = $pdo->prepare("UPDATE comments SET is_approved = 1 WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: comments.php?success=onaylandi");
    exit;
}

// --- GİZLEME İŞLEMİ ---
if (isset($_GET['hide'])) {
    $id = $_GET['hide'];
    $stmt = $pdo->prepare("UPDATE comments SET is_approved = 0 WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: comments.php?success=gizlendi");
    exit;
}

// --- SİLME İŞLEMİ ---
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM comments WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: comments.php?success=silindi");
    exit;
}

// Yorumları Çek
$sql = "SELECT c.*, u.name as user_name, u.email as user_email 
        FROM comments c 
        LEFT JOIN users u ON c.user_id = u.id 
        ORDER BY c.created_at DESC";
$comments = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container-fluid">
    <h2 class="mb-4">Müşteri Yorumları</h2>

    <?php if(isset($_GET['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show">
            İşlem başarıyla gerçekleştirildi.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
            <span>Yorum Listesi</span>
            <span class="badge bg-light text-dark"><?= count($comments) ?> Toplam</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-secondary">
                        <tr>
                            <th width="50">Durum</th>
                            <th width="150">Kullanıcı</th>
                            <th width="100">Puan</th>
                            <th>Yorum & Cevap</th>
                            <th width="120">Tarih</th>
                            <th class="text-end" width="180">İşlemler</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($comments as $comment): ?>
                        <tr>
                            <td>
                                <?php if($comment['is_approved']): ?>
                                    <span class="badge bg-success" title="Yayında"><i class="fas fa-check"></i></span>
                                <?php else: ?>
                                    <span class="badge bg-warning text-dark" title="Onay Bekliyor"><i class="fas fa-clock"></i></span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="fw-bold"><?= htmlspecialchars($comment['user_name'] ?? 'Misafir') ?></div>
                                <div class="small text-muted"><?= htmlspecialchars($comment['user_email'] ?? '') ?></div>
                            </td>
                            <td>
                                <div class="text-warning">
                                    <?php for($i=1; $i<=5; $i++): ?>
                                        <?php if($i <= $comment['rating']): ?>
                                            <i class="fas fa-star"></i>
                                        <?php else: ?>
                                            <i class="far fa-star text-secondary"></i>
                                        <?php endif; ?>
                                    <?php endfor; ?>
                                </div>
                            </td>
                            <td>
                                <div class="text-break fst-italic">
                                    "<?= htmlspecialchars($comment['comment']) ?>"
                                </div>
                                <?php if(!empty($comment['reply'])): ?>
                                    <div class="mt-2 p-2 bg-light border-start border-4 border-primary rounded small">
                                        <strong class="text-primary"><i class="fas fa-reply me-1"></i> Restoran Cevabı:</strong><br>
                                        <?= htmlspecialchars($comment['reply']) ?>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <small class="text-muted"><?= date('d.m.Y', strtotime($comment['created_at'])) ?></small>
                            </td>
                            <td class="text-end">
                                <!-- CEVAPLA BUTONU -->
                                <button type="button" class="btn btn-sm btn-primary" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#replyModal"
                                        onclick="setReplyData(<?= $comment['id'] ?>, '<?= htmlspecialchars($comment['reply'] ?? '', ENT_QUOTES) ?>')">
                                    <i class="fas fa-reply"></i>
                                </button>

                                <?php if(!$comment['is_approved']): ?>
                                    <a href="comments.php?approve=<?= $comment['id'] ?>" class="btn btn-sm btn-success" title="Onayla">
                                        <i class="fas fa-check"></i>
                                    </a>
                                <?php else: ?>
                                    <a href="comments.php?hide=<?= $comment['id'] ?>" class="btn btn-sm btn-secondary" title="Gizle">
                                        <i class="fas fa-eye-slash"></i>
                                    </a>
                                <?php endif; ?>
                                
                                <a href="comments.php?delete=<?= $comment['id'] ?>" 
                                   class="btn btn-sm btn-danger" 
                                   onclick="return confirm('Silmek istediğinize emin misiniz?');">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- CEVAPLAMA MODALI (Penceresi) -->
<div class="modal fade" id="replyModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">Yoruma Cevap Ver</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="comment_id" id="modalCommentId">
                    <div class="mb-3">
                        <label class="form-label">Cevabınız:</label>
                        <textarea name="reply_text" id="modalReplyText" class="form-control" rows="4" placeholder="Müşteriye cevabınızı buraya yazın..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                    <button type="submit" name="submit_reply" class="btn btn-primary">Cevabı Kaydet</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal İçin JS -->
<script>
function setReplyData(id, currentReply) {
    document.getElementById('modalCommentId').value = id;
    document.getElementById('modalReplyText').value = currentReply;
}
</script>

<?php require_once 'includes/footer.php'; ?>