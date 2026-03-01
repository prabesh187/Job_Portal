<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow">
                <div class="card-body p-4">
                    <h2 class="mb-4">Edit Job</h2>
                    
                    <form method="POST" action="<?= $this->url('jobs/' . $job['id'] . '/edit') ?>">
                        <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                        
                        <div class="row">
                            <div class="col-md-8 mb-3">
                                <label for="title" class="form-label">Job Title *</label>
                                <input type="text" class="form-control" id="title" name="title" value="<?= htmlspecialchars($job['title']) ?>" required>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="job_type" class="form-label">Job Type *</label>
                                <select class="form-select" id="job_type" name="job_type" required>
                                    <option value="full-time" <?= $job['job_type'] === 'full-time' ? 'selected' : '' ?>>Full Time</option>
                                    <option value="part-time" <?= $job['job_type'] === 'part-time' ? 'selected' : '' ?>>Part Time</option>
                                    <option value="contract" <?= $job['job_type'] === 'contract' ? 'selected' : '' ?>>Contract</option>
                                    <option value="internship" <?= $job['job_type'] === 'internship' ? 'selected' : '' ?>>Internship</option>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="location" class="form-label">Location</label>
                                <input type="text" class="form-control" id="location" name="location" value="<?= htmlspecialchars($job['location']) ?>">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="category" class="form-label">Category</label>
                                <input type="text" class="form-control" id="category" name="category" value="<?= htmlspecialchars($job['category']) ?>">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="salary_min" class="form-label">Minimum Salary</label>
                                <input type="number" class="form-control" id="salary_min" name="salary_min" value="<?= $job['salary_min'] ?>">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="salary_max" class="form-label">Maximum Salary</label>
                                <input type="number" class="form-control" id="salary_max" name="salary_max" value="<?= $job['salary_max'] ?>">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Job Description *</label>
                            <textarea class="form-control" id="description" name="description" rows="6" required><?= htmlspecialchars($job['description']) ?></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="requirements" class="form-label">Requirements</label>
                            <textarea class="form-control" id="requirements" name="requirements" rows="4"><?= htmlspecialchars($job['requirements']) ?></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status">
                                <option value="active" <?= $job['status'] === 'active' ? 'selected' : '' ?>>Active</option>
                                <option value="closed" <?= $job['status'] === 'closed' ? 'selected' : '' ?>>Closed</option>
                                <option value="draft" <?= $job['status'] === 'draft' ? 'selected' : '' ?>>Draft</option>
                            </select>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle"></i> Update Job
                            </button>
                            <a href="<?= $this->url('jobs/' . $job['id']) ?>" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
