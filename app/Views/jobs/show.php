<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="container">
    <div class="row">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-body">
                    <h1 class="mb-3"><?= htmlspecialchars($job['title']) ?></h1>
                    
                    <div class="mb-4">
                        <p class="text-muted mb-2">
                            <i class="bi bi-building"></i> <strong><?= htmlspecialchars($job['company_name'] ?? 'Company') ?></strong>
                        </p>
                        <p class="text-muted mb-2">
                            <i class="bi bi-geo-alt"></i> <?= htmlspecialchars($job['location']) ?>
                        </p>
                        <p class="text-muted mb-2">
                            <i class="bi bi-clock"></i> Posted <?= date('M d, Y', strtotime($job['created_at'])) ?>
                        </p>
                        <p class="text-muted">
                            <i class="bi bi-eye"></i> <?= $job['views'] ?> views
                        </p>
                    </div>

                    <div class="mb-4">
                        <span class="badge bg-primary me-2"><?= ucfirst(str_replace('-', ' ', $job['job_type'])) ?></span>
                        <?php if ($job['category']): ?>
                            <span class="badge bg-secondary"><?= htmlspecialchars($job['category']) ?></span>
                        <?php endif; ?>
                    </div>

                    <?php if ($job['salary_min'] || $job['salary_max']): ?>
                        <div class="mb-4">
                            <h5>Salary Range</h5>
                            <p>
                                $<?= number_format($job['salary_min']) ?> - $<?= number_format($job['salary_max']) ?>
                            </p>
                        </div>
                    <?php endif; ?>

                    <div class="mb-4">
                        <h5>Job Description</h5>
                        <p><?= nl2br(htmlspecialchars($job['description'])) ?></p>
                    </div>

                    <?php if ($job['requirements']): ?>
                        <div class="mb-4">
                            <h5>Requirements</h5>
                            <p><?= nl2br(htmlspecialchars($job['requirements'])) ?></p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow mb-3">
                <div class="card-body">
                    <?php if (\App\Core\Auth::check() && \App\Core\Auth::isCandidate()): ?>
                        <?php if (isset($has_applied) && $has_applied): ?>
                            <button class="btn btn-secondary w-100" disabled>
                                <i class="bi bi-check-circle"></i> Already Applied
                            </button>
                        <?php else: ?>
                            <button class="btn btn-primary w-100 mb-2" data-bs-toggle="modal" data-bs-target="#applyModal">
                                <i class="bi bi-send"></i> Apply Now
                            </button>
                        <?php endif; ?>
                        
                        <button class="btn btn-outline-primary w-100" onclick="toggleSaveJob(<?= $job['id'] ?>)">
                            <i class="bi bi-bookmark<?= isset($is_saved) && $is_saved ? '-fill' : '' ?>" id="saveIcon"></i>
                            <span id="saveText"><?= isset($is_saved) && $is_saved ? 'Saved' : 'Save Job' ?></span>
                        </button>
                    <?php elseif (!\App\Core\Auth::check()): ?>
                        <a href="<?= $this->url('login') ?>" class="btn btn-primary w-100">
                            Login to Apply
                        </a>
                    <?php endif; ?>
                </div>
            </div>

            <?php if ($job['company_description']): ?>
                <div class="card shadow">
                    <div class="card-body">
                        <h5>About Company</h5>
                        <p><?= htmlspecialchars($job['company_description']) ?></p>
                        <?php if ($job['company_website']): ?>
                            <a href="<?= htmlspecialchars($job['company_website']) ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-globe"></i> Visit Website
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Apply Modal -->
<?php if (\App\Core\Auth::check() && \App\Core\Auth::isCandidate() && (!isset($has_applied) || !$has_applied)): ?>
<div class="modal fade" id="applyModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Apply for <?= htmlspecialchars($job['title']) ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="applyForm">
                <div class="modal-body">
                    <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                    <div class="mb-3">
                        <label for="cover_letter" class="form-label">Cover Letter (Optional)</label>
                        <textarea class="form-control" id="cover_letter" name="cover_letter" rows="5" placeholder="Tell the employer why you're a great fit..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Submit Application</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById('applyForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    
    fetch('<?= $this->url('applications/apply/' . $job['id']) ?>', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            location.reload();
        } else {
            alert(data.message);
        }
    });
});

function toggleSaveJob(jobId) {
    fetch('<?= $this->url('jobs/save/') ?>' + jobId, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'csrf_token=<?= $csrf_token ?>'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const icon = document.getElementById('saveIcon');
            const text = document.getElementById('saveText');
            if (data.saved) {
                icon.classList.add('bi-bookmark-fill');
                icon.classList.remove('bi-bookmark');
                text.textContent = 'Saved';
            } else {
                icon.classList.add('bi-bookmark');
                icon.classList.remove('bi-bookmark-fill');
                text.textContent = 'Save Job';
            }
        }
    });
}
</script>
<?php endif; ?>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
