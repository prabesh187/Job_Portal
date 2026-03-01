<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="container">
    <h1 class="mb-4">Candidate Dashboard</h1>

    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card text-white bg-primary">
                <div class="card-body">
                    <h5 class="card-title">Total Applications</h5>
                    <h2><?= $total_applications ?></h2>
                </div>
            </div>
        </div>
        <?php foreach ($application_stats as $stat): ?>
            <div class="col-md-2">
                <div class="card">
                    <div class="card-body text-center">
                        <h6><?= ucfirst($stat['status']) ?></h6>
                        <h4><?= $stat['count'] ?></h4>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5>My Applications</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($my_applications)): ?>
                        <p class="text-muted">You haven't applied to any jobs yet.</p>
                        <a href="<?= $this->url('jobs') ?>" class="btn btn-primary">Browse Jobs</a>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Job Title</th>
                                        <th>Company</th>
                                        <th>Status</th>
                                        <th>Applied Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($my_applications as $app): ?>
                                        <tr>
                                            <td>
                                                <a href="<?= $this->url('jobs/' . $app['job_id']) ?>">
                                                    <?= htmlspecialchars($app['title']) ?>
                                                </a>
                                            </td>
                                            <td><?= htmlspecialchars($app['company_name'] ?? 'N/A') ?></td>
                                            <td>
                                                <span class="badge bg-<?= $app['status'] === 'accepted' ? 'success' : ($app['status'] === 'rejected' ? 'danger' : 'warning') ?>">
                                                    <?= ucfirst($app['status']) ?>
                                                </span>
                                            </td>
                                            <td><?= date('M d, Y', strtotime($app['applied_at'])) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5>Recent Jobs</h5>
                </div>
                <div class="card-body">
                    <?php foreach ($recent_jobs as $job): ?>
                        <div class="mb-3">
                            <h6>
                                <a href="<?= $this->url('jobs/' . $job['id']) ?>">
                                    <?= htmlspecialchars($job['title']) ?>
                                </a>
                            </h6>
                            <p class="text-muted small mb-0"><?= htmlspecialchars($job['company_name'] ?? 'Company') ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
