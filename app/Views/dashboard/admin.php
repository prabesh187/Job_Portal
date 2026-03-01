<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="container">
    <h1 class="mb-4">Admin Dashboard</h1>

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
                    <h5 class="card-title">Total Users</h5>
                    <h2><?= $total_users ?></h2>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5>User Statistics</h5>
                </div>
                <div class="card-body">
                    <canvas id="userStatsChart"></canvas>
                    <div class="mt-3">
                        <?php foreach ($user_stats as $stat): ?>
                            <div class="d-flex justify-content-between mb-2">
                                <span><?= ucfirst($stat['role']) ?>s:</span>
                                <strong><?= $stat['count'] ?></strong>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5>Job Statistics</h5>
                </div>
                <div class="card-body">
                    <canvas id="jobStatsChart"></canvas>
                    <div class="mt-3">
                        <?php foreach ($job_stats as $stat): ?>
                            <div class="d-flex justify-content-between mb-2">
                                <span><?= ucfirst($stat['status']) ?> Jobs:</span>
                                <strong><?= $stat['count'] ?></strong>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5>Recent Jobs</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Job Title</th>
                            <th>Employer</th>
                            <th>Location</th>
                            <th>Status</th>
                            <th>Posted Date</th>
                            <th>Views</th>
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
                                <td><?= htmlspecialchars($job['employer_name']) ?></td>
                                <td><?= htmlspecialchars($job['location']) ?></td>
                                <td>
                                    <span class="badge bg-<?= $job['status'] === 'active' ? 'success' : 'secondary' ?>">
                                        <?= ucfirst($job['status']) ?>
                                    </span>
                                </td>
                                <td><?= date('M d, Y', strtotime($job['created_at'])) ?></td>
                                <td><?= $job['views'] ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
