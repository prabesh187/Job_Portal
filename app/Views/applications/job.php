<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="container">
    <div class="mb-4">
        <h1>Applications for: <?= htmlspecialchars($job['title']) ?></h1>
        <p class="text-muted">Total Applications: <?= count($applications) ?></p>
    </div>

    <?php if (empty($applications)): ?>
        <div class="alert alert-info">No applications received yet.</div>
    <?php else: ?>
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Candidate</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Experience</th>
                                <th>Status</th>
                                <th>Applied Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($applications as $app): ?>
                                <tr>
                                    <td><?= htmlspecialchars($app['full_name']) ?></td>
                                    <td><?= htmlspecialchars($app['email']) ?></td>
                                    <td><?= htmlspecialchars($app['phone'] ?? 'N/A') ?></td>
                                    <td><?= $app['experience_years'] ?? 0 ?> years</td>
                                    <td>
                                        <select class="form-select form-select-sm" onchange="updateApplicationStatus(<?= $app['id'] ?>, this.value)">
                                            <option value="pending" <?= $app['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                                            <option value="reviewed" <?= $app['status'] === 'reviewed' ? 'selected' : '' ?>>Reviewed</option>
                                            <option value="shortlisted" <?= $app['status'] === 'shortlisted' ? 'selected' : '' ?>>Shortlisted</option>
                                            <option value="accepted" <?= $app['status'] === 'accepted' ? 'selected' : '' ?>>Accepted</option>
                                            <option value="rejected" <?= $app['status'] === 'rejected' ? 'selected' : '' ?>>Rejected</option>
                                        </select>
                                    </td>
                                    <td><?= date('M d, Y', strtotime($app['applied_at'])) ?></td>
                                    <td>
                                        <?php if ($app['resume_path']): ?>
                                            <a href="<?= $this->url('uploads/resumes/' . basename($app['resume_path'])) ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-file-earmark-pdf"></i> Resume
                                            </a>
                                        <?php endif; ?>
                                        <?php if ($app['cover_letter']): ?>
                                            <button class="btn btn-sm btn-outline-info" onclick="showCoverLetter('<?= htmlspecialchars(addslashes($app['cover_letter'])) ?>')">
                                                <i class="bi bi-envelope"></i> Cover Letter
                                            </button>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- Cover Letter Modal -->
<div class="modal fade" id="coverLetterModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Cover Letter</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="coverLetterContent"></div>
        </div>
    </div>
</div>

<script>
const csrfToken = '<?= $csrf_token ?>';

function updateApplicationStatus(applicationId, status) {
    const formData = new FormData();
    formData.append('csrf_token', csrfToken);
    formData.append('status', status);
    
    fetch('<?= $this->url('applications/') ?>' + applicationId + '/status', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('Status updated successfully', 'success');
        } else {
            showToast(data.message, 'danger');
        }
    })
    .catch(error => {
        showToast('An error occurred', 'danger');
    });
}

function showCoverLetter(content) {
    document.getElementById('coverLetterContent').textContent = content;
    const modal = new bootstrap.Modal(document.getElementById('coverLetterModal'));
    modal.show();
}
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
