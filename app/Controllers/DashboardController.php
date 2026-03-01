<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Models\Job;
use App\Models\Application;
use App\Models\User;

/**
 * Dashboard Controller
 */
class DashboardController extends Controller {
    
    /**
     * Show dashboard based on user role
     */
    public function index() {
        $this->requireAuth();

        $user = Auth::user();
        
        switch ($user['role']) {
            case 'admin':
                $this->adminDashboard();
                break;
            case 'employer':
                $this->employerDashboard();
                break;
            case 'candidate':
                $this->candidateDashboard();
                break;
            default:
                $this->redirect('login');
        }
    }

    /**
     * Admin dashboard
     */
    private function adminDashboard() {
        $jobModel = new Job();
        $applicationModel = new Application();
        $userModel = new User();

        $data = [
            'total_jobs' => $jobModel->count(),
            'active_jobs' => $jobModel->count(['status' => 'active']),
            'total_applications' => $applicationModel->count(),
            'total_users' => $userModel->count(),
            'recent_jobs' => $jobModel->getRecentJobs(10),
            'user_stats' => $userModel->getStatistics(),
            'job_stats' => $jobModel->getStatistics()
        ];

        $this->view('dashboard.admin', $data);
    }

    /**
     * Employer dashboard
     */
    private function employerDashboard() {
        $userId = Auth::id();
        $jobModel = new Job();
        $applicationModel = new Application();

        $data = [
            'total_jobs' => $jobModel->count(['employer_id' => $userId]),
            'active_jobs' => $jobModel->count(['employer_id' => $userId, 'status' => 'active']),
            'total_applications' => $applicationModel->getTotalCount($userId, 'employer'),
            'recent_jobs' => $jobModel->getJobs(['employer_id' => $userId], 1, 5),
            'application_stats' => $applicationModel->getStatistics($userId, 'employer')
        ];

        $this->view('dashboard.employer', $data);
    }

    /**
     * Candidate dashboard
     */
    private function candidateDashboard() {
        $userId = Auth::id();
        $applicationModel = new Application();
        $jobModel = new Job();

        $data = [
            'total_applications' => $applicationModel->getTotalCount($userId, 'candidate'),
            'my_applications' => $applicationModel->getCandidateApplications($userId),
            'application_stats' => $applicationModel->getStatistics($userId, 'candidate'),
            'recent_jobs' => $jobModel->getRecentJobs(5)
        ];

        $this->view('dashboard.candidate', $data);
    }
}
