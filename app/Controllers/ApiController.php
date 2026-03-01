<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Job;

/**
 * API Controller
 * REST API endpoints
 */
class ApiController extends Controller {
    
    /**
     * Get jobs list (JSON)
     */
    public function jobs() {
        header('Content-Type: application/json');
        
        $jobModel = new Job();
        
        $page = max(1, (int)$this->query('page', 1));
        $perPage = min(50, (int)$this->query('per_page', 10));
        
        $filters = [
            'keyword' => $this->query('keyword'),
            'location' => $this->query('location'),
            'job_type' => $this->query('job_type'),
            'category' => $this->query('category'),
            'status' => 'active'
        ];

        $jobs = $jobModel->getJobs($filters, $page, $perPage);
        $totalJobs = $jobModel->countJobs($filters);

        $this->json([
            'success' => true,
            'data' => $jobs,
            'meta' => [
                'total' => $totalJobs,
                'page' => $page,
                'per_page' => $perPage,
                'total_pages' => ceil($totalJobs / $perPage)
            ]
        ]);
    }

    /**
     * Get single job (JSON)
     */
    public function job($id) {
        header('Content-Type: application/json');
        
        $jobModel = new Job();
        $job = $jobModel->getJobDetails($id);

        if (!$job || $job['status'] !== 'active') {
            $this->json([
                'success' => false,
                'message' => 'Job not found'
            ], 404);
        }

        $this->json([
            'success' => true,
            'data' => $job
        ]);
    }
}
