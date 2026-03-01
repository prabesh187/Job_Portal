<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Core\Security;
use App\Models\Job;
use App\Models\Application;
use App\Models\SavedJob;

/**
 * Job Controller
 */
class JobController extends Controller {
    
    /**
     * List all jobs with search and filters
     */
    public function index() {
        $jobModel = new Job();
        
        $page = max(1, (int)$this->query('page', 1));
        $perPage = $this->config['pagination']['per_page'];
        
        $filters = [
            'keyword' => $this->query('keyword'),
            'location' => $this->query('location'),
            'job_type' => $this->query('job_type'),
            'category' => $this->query('category'),
            'status' => 'active'
        ];

        $jobs = $jobModel->getJobs($filters, $page, $perPage);
        $totalJobs = $jobModel->countJobs($filters);
        $totalPages = ceil($totalJobs / $perPage);

        $this->view('jobs.index', [
            'jobs' => $jobs,
            'filters' => $filters,
            'current_page' => $page,
            'total_pages' => $totalPages,
            'total_jobs' => $totalJobs
        ]);
    }

    /**
     * Show job details
     */
    public function show($id) {
        $jobModel = new Job();
        $job = $jobModel->getJobDetails($id);

        if (!$job) {
            $_SESSION['error'] = 'Job not found';
            $this->redirect('jobs');
        }

        // Increment views
        $jobModel->incrementViews($id);

        $data = ['job' => $job];

        // Check if user is logged in
        if (Auth::check()) {
            $userId = Auth::id();
            
            // Check if already applied
            if (Auth::isCandidate()) {
                $applicationModel = new Application();
                $data['has_applied'] = $applicationModel->hasApplied($id, $userId);
                
                // Check if saved
                $savedJobModel = new SavedJob();
                $data['is_saved'] = $savedJobModel->isSaved($userId, $id);
            }
            
            $data['csrf_token'] = Security::generateCsrfToken();
        }

        $this->view('jobs.show', $data);
    }

    /**
     * Show create job form
     */
    public function create() {
        $this->requireRole('employer');
        $this->view('jobs.create', ['csrf_token' => Security::generateCsrfToken()]);
    }

    /**
     * Store new job
     */
    public function store() {
        $this->requireRole('employer');
        $this->validateCsrfToken();

        $data = [
            'employer_id' => Auth::id(),
            'title' => $this->input('title'),
            'description' => $this->input('description'),
            'requirements' => $this->input('requirements'),
            'location' => $this->input('location'),
            'job_type' => $this->input('job_type'),
            'salary_min' => $this->input('salary_min'),
            'salary_max' => $this->input('salary_max'),
            'category' => $this->input('category'),
            'status' => $this->input('status', 'active')
        ];

        // Validation
        if (empty($data['title']) || empty($data['description']) || empty($data['job_type'])) {
            $_SESSION['error'] = 'Required fields are missing';
            $this->redirect('jobs/create');
        }

        $jobModel = new Job();
        $jobId = $jobModel->create($data);

        if ($jobId) {
            $_SESSION['success'] = 'Job posted successfully';
            $this->redirect('jobs/' . $jobId);
        } else {
            $_SESSION['error'] = 'Failed to create job';
            $this->redirect('jobs/create');
        }
    }

    /**
     * Show edit job form
     */
    public function edit($id) {
        $this->requireRole('employer');
        
        $jobModel = new Job();
        $job = $jobModel->find($id);

        if (!$job || $job['employer_id'] != Auth::id()) {
            $_SESSION['error'] = 'Job not found or unauthorized';
            $this->redirect('dashboard');
        }

        $this->view('jobs.edit', [
            'job' => $job,
            'csrf_token' => Security::generateCsrfToken()
        ]);
    }

    /**
     * Update job
     */
    public function update($id) {
        $this->requireRole('employer');
        $this->validateCsrfToken();

        $jobModel = new Job();
        $job = $jobModel->find($id);

        if (!$job || $job['employer_id'] != Auth::id()) {
            $_SESSION['error'] = 'Job not found or unauthorized';
            $this->redirect('dashboard');
        }

        $data = [
            'title' => $this->input('title'),
            'description' => $this->input('description'),
            'requirements' => $this->input('requirements'),
            'location' => $this->input('location'),
            'job_type' => $this->input('job_type'),
            'salary_min' => $this->input('salary_min'),
            'salary_max' => $this->input('salary_max'),
            'category' => $this->input('category'),
            'status' => $this->input('status')
        ];

        if ($jobModel->update($id, $data)) {
            $_SESSION['success'] = 'Job updated successfully';
            $this->redirect('jobs/' . $id);
        } else {
            $_SESSION['error'] = 'Failed to update job';
            $this->redirect('jobs/' . $id . '/edit');
        }
    }

    /**
     * Delete job
     */
    public function delete($id) {
        $this->requireRole('employer');
        $this->validateCsrfToken();

        $jobModel = new Job();
        $job = $jobModel->find($id);

        if (!$job || $job['employer_id'] != Auth::id()) {
            $this->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        if ($jobModel->delete($id)) {
            $this->json(['success' => true, 'message' => 'Job deleted successfully']);
        } else {
            $this->json(['success' => false, 'message' => 'Failed to delete job'], 500);
        }
    }

    /**
     * AJAX search jobs
     */
    public function search() {
        $jobModel = new Job();
        
        $filters = [
            'keyword' => $this->query('keyword'),
            'location' => $this->query('location'),
            'job_type' => $this->query('job_type'),
            'category' => $this->query('category'),
            'status' => 'active'
        ];

        $page = max(1, (int)$this->query('page', 1));
        $perPage = 10;

        $jobs = $jobModel->getJobs($filters, $page, $perPage);
        $totalJobs = $jobModel->countJobs($filters);

        $this->json([
            'success' => true,
            'jobs' => $jobs,
            'total' => $totalJobs,
            'page' => $page,
            'total_pages' => ceil($totalJobs / $perPage)
        ]);
    }
}
