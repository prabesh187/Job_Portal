<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Models\SavedJob;

/**
 * Saved Job Controller
 */
class SavedJobController extends Controller {
    
    /**
     * Toggle save/unsave job
     */
    public function toggle($jobId) {
        $this->requireRole('candidate');
        $this->validateCsrfToken();

        $userId = Auth::id();
        $savedJobModel = new SavedJob();

        if ($savedJobModel->isSaved($userId, $jobId)) {
            $result = $savedJobModel->unsaveJob($userId, $jobId);
            $this->json([
                'success' => $result,
                'saved' => false,
                'message' => 'Job removed from saved list'
            ]);
        } else {
            $result = $savedJobModel->saveJob($userId, $jobId);
            $this->json([
                'success' => (bool)$result,
                'saved' => true,
                'message' => 'Job saved successfully'
            ]);
        }
    }

    /**
     * List saved jobs
     */
    public function index() {
        $this->requireRole('candidate');

        $userId = Auth::id();
        $savedJobModel = new SavedJob();
        $savedJobs = $savedJobModel->getUserSavedJobs($userId);

        $this->view('jobs.saved', [
            'saved_jobs' => $savedJobs
        ]);
    }
}
