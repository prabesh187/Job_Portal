<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Employer Dashboard</h1>
        <a href="<?= $this->url('jobs/create') ?>" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Post New Job
        </a>
    </div>

    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-white bg-primary">
                <div class="card-body">
                    <h5 class="card-title">Total Jobs</h5>
                    <h2><?= $total_jobs ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-success">
                <div class="card-body">
                    <h5 class="card-title">Active Jobs</h5>
                    <h2><?= $active_jobs ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-info">
                <div class="card-body">
                    <h5 class="card-title">Total Applications</h5>
                    <h2><?= $total_applications ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-warning">
                <div class="card-body">
                    <h5 class="card-title">Pending Reviews</h5>
                    <h2>
                        <?php
                        $pending = array_filter($application_stats, fn($s) => $s['status'] === 'pending');
                        echo !empty($pending) ? $pending[0]['count'] : 0;
                        ?>
                    </h2>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5>My Job Postings</h5>
        </div>
        <div class="card-body">
            <?php if (empty($recent_jobs)): ?>
                <p class="text-muted">You haven't posted any jobs yet.</p>
                <a href="<?= $this->url('jobs/create') ?>" class="btn btn-primary">Post Your First Job</a>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Job Title</th>
                                <th>Location</th>
                                <th>Type</th>
                                <th>Status</th>
                                <th>Posted Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recent_jobs as $job): ?>
                                <tr>
                                    <td>
                                        <a href="<?= $this->url('jobs/' . $job['id']) ?>">
                                            <?= htmlspecialchars($job['title']) ?>
                                        </a>
                                    </td>
                                    <td><?= htmlspecialchars($job['location']) ?></td>
                                    <td><?= ucfirst(str_replace('-', ' ', $job['job_type'])) ?></td>
                                    <td>
                                        <span class="badge bg-<?= $job['status'] === 'active' ? 'success' : 'secondary' ?>">
                                            <?= ucfirst($job['status']) ?>
                                        </span>
                                    </td>
                                    <td><?= date('M d, Y', strtotime($job['created_at'])) ?></td>
                                    <td>
                                        <a href="<?= $this->url('applications/job/' . $job['id']) ?>" class="btn btn-sm btn-info">
                                            <i class="bi bi-people"></i> Applications
                                        </a>
                                        <a href="<?= $this->url('jobs/' . $job['id'] . '/edit') ?>" class="btn btn-sm btn-warning">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
