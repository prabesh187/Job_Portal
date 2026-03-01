<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Core\Security;
use App\Models\Application;
use App\Models\Job;

/**
 * Application Controller
 */
class ApplicationController extends Controller {
    
    /**
     * Apply for a job
     */
    public function apply($jobId) {
        $this->requireRole('candidate');
        $this->validateCsrfToken();

        $applicationModel = new Application();
        $jobModel = new Job();
        
        $job = $jobModel->find($jobId);
        if (!$job || $job['status'] !== 'active') {
            $this->json(['success' => false, 'message' => 'Job not available'], 404);
        }

        $userId = Auth::id();
        
        // Check if already applied
        if ($applicationModel->hasApplied($jobId, $userId)) {
            $this->json(['success' => false, 'message' => 'Already applied to this job'], 400);
        }

        $coverLetter = $this->input('cover_letter');
        
        $applicationId = $applicationModel->apply($jobId, $userId, $coverLetter);

        if ($applicationId) {
            // Send email notification (if enabled)
            $this->sendApplicationNotification($job, $userId);
            
            $this->json(['success' => true, 'message' => 'Application submitted successfully']);
        } else {
            $this->json(['success' => false, 'message' => 'Failed to submit application'], 500);
        }
    }

    /**
     * View applications for a job (Employer)
     */
    public function jobApplications($jobId) {
        $this->requireRole('employer');

        $jobModel = new Job();
        $job = $jobModel->find($jobId);

        if (!$job || $job['employer_id'] != Auth::id()) {
            $_SESSION['error'] = 'Job not found or unauthorized';
            $this->redirect('dashboard');
        }

        $applicationModel = new Application();
        $applications = $applicationModel->getJobApplications($jobId);

        $this->view('applications.job', [
            'job' => $job,
            'applications' => $applications,
            'csrf_token' => Security::generateCsrfToken()
        ]);
    }

    /**
     * Update application status
     */
    public function updateStatus($applicationId) {
        $this->requireRole('employer');
        $this->validateCsrfToken();

        $status = $this->input('status');
        $validStatuses = ['pending', 'reviewed', 'shortlisted', 'rejected', 'accepted'];

        if (!in_array($status, $validStatuses)) {
            $this->json(['success' => false, 'message' => 'Invalid status'], 400);
        }

        $applicationModel = new Application();
        $application = $applicationModel->find($applicationId);

        if (!$application) {
            $this->json(['success' => false, 'message' => 'Application not found'], 404);
        }

        // Verify employer owns the job
        $jobModel = new Job();
        $job = $jobModel->find($application['job_id']);
        
        if ($job['employer_id'] != Auth::id()) {
            $this->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        if ($applicationModel->updateStatus($applicationId, $status)) {
            // Send email notification to candidate
            $this->sendStatusUpdateNotification($application, $status);
            
            $this->json(['success' => true, 'message' => 'Status updated successfully']);
        } else {
            $this->json(['success' => false, 'message' => 'Failed to update status'], 500);
        }
    }

    /**
     * Send application notification email
     */
    private function sendApplicationNotification($job, $candidateId) {
        if (!$this->config['email']['enabled']) {
            return;
        }

        // Email sending logic would go here
        // Using PHPMailer or similar library
    }

    /**
     * Send status update notification
     */
    private function sendStatusUpdateNotification($application, $status) {
        if (!$this->config['email']['enabled']) {
            return;
        }

        // Email sending logic would go here
    }
}
