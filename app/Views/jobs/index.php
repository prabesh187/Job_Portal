<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="container">
    <h1 class="mb-4">Browse Jobs</h1>

    <!-- Search and Filter Form -->
    <div class="card mb-4">
        <div class="card-body">
            <form id="searchForm" method="GET" action="<?= $this->url('jobs') ?>">
                <div class="row g-3">
                    <div class="col-md-4">
                        <input type="text" class="form-control" name="keyword" placeholder="Job title or keyword" value="<?= htmlspecialchars($filters['keyword'] ?? '') ?>">
                    </div>
                    <div class="col-md-3">
                        <input type="text" class="form-control" name="location" placeholder="Location" value="<?= htmlspecialchars($filters['location'] ?? '') ?>">
                    </div>
                    <div class="col-md-2">
                        <select class="form-select" name="job_type">
                            <option value="">Job Type</option>
                            <option value="full-time" <?= ($filters['job_type'] ?? '') === 'full-time' ? 'selected' : '' ?>>Full Time</option>
                            <option value="part-time" <?= ($filters['job_type'] ?? '') === 'part-time' ? 'selected' : '' ?>>Part Time</option>
                            <option value="contract" <?= ($filters['job_type'] ?? '') === 'contract' ? 'selected' : '' ?>>Contract</option>
                            <option value="internship" <?= ($filters['job_type'] ?? '') === 'internship' ? 'selected' : '' ?>>Internship</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-search"></i> Search
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <p class="text-muted">Found <?= $total_jobs ?> jobs</p>

    <!-- Jobs List -->
    <div id="jobsList">
        <?php if (empty($jobs)): ?>
            <div class="alert alert-info">No jobs found matching your criteria.</div>
        <?php else: ?>
            <?php foreach ($jobs as $job): ?>
                <div class="card mb-3 job-card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-9">
                                <h5 class="card-title">
                                    <a href="<?= $this->url('jobs/' . $job['id']) ?>" class="text-decoration-none">
                                        <?= htmlspecialchars($job['title']) ?>
                                    </a>
                                </h5>
                                <p class="text-muted mb-2">
                                    <i class="bi bi-building"></i> <?= htmlspecialchars($job['company_name'] ?? 'Company') ?>
                                    <span class="ms-3"><i class="bi bi-geo-alt"></i> <?= htmlspecialchars($job['location']) ?></span>
                                </p>
                                <p class="card-text"><?= htmlspecialchars(substr($job['description'], 0, 150)) ?>...</p>
                                <div class="d-flex gap-2">
                                    <span class="badge bg-primary"><?= ucfirst(str_replace('-', ' ', $job['job_type'])) ?></span>
                                    <?php if ($job['category']): ?>
                                        <span class="badge bg-secondary"><?= htmlspecialchars($job['category']) ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-3 text-end">
                                <p class="text-muted small">
                                    <i class="bi bi-clock"></i> <?= date('M d, Y', strtotime($job['created_at'])) ?>
                                </p>
                                <a href="<?= $this->url('jobs/' . $job['id']) ?>" class="btn btn-outline-primary">View Details</a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- Pagination -->
    <?php if ($total_pages > 1): ?>
        <nav aria-label="Job pagination">
            <ul class="pagination justify-content-center">
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <li class="page-item <?= $i === $current_page ? 'active' : '' ?>">
                        <a class="page-link" href="<?= $this->url('jobs?page=' . $i . '&' . http_build_query($filters)) ?>">
                            <?= $i ?>
                        </a>
                    </li>
                <?php endfor; ?>
            </ul>
        </nav>
    <?php endif; ?>
</div>

<script>
// AJAX search functionality
document.getElementById('searchForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    const params = new URLSearchParams(formData);
    
    fetch('<?= $this->url('jobs/search') ?>?' + params.toString())
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateJobsList(data.jobs);
            }
        });
});

function updateJobsList(jobs) {
    const container = document.getElementById('jobsList');
    if (jobs.length === 0) {
        container.innerHTML = '<div class="alert alert-info">No jobs found matching your criteria.</div>';
        return;
    }
    
    let html = '';
    jobs.forEach(job => {
        html += `
            <div class="card mb-3 job-card">
                <div class="card-body">
                    <h5 class="card-title">
                        <a href="<?= $this->url('jobs/') ?>${job.id}" class="text-decoration-none">
                            ${escapeHtml(job.title)}
                        </a>
                    </h5>
                    <p class="text-muted mb-2">
                        <i class="bi bi-building"></i> ${escapeHtml(job.company_name || 'Company')}
                        <span class="ms-3"><i class="bi bi-geo-alt"></i> ${escapeHtml(job.location)}</span>
                    </p>
                    <p class="card-text">${escapeHtml(job.description.substring(0, 150))}...</p>
                    <span class="badge bg-primary">${job.job_type.replace('-', ' ')}</span>
                </div>
            </div>
        `;
    });
    container.innerHTML = html;
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
