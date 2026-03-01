<?php
namespace App\Models;

use App\Core\Model;

/**
 * Application Model
 */
class Application extends Model {
    protected $table = 'applications';

    /**
     * Create application
     */
    public function apply($jobId, $candidateId, $coverLetter = null) {
        try {
            return $this->create([
                'job_id' => $jobId,
                'candidate_id' => $candidateId,
                'cover_letter' => $coverLetter,
                'status' => 'pending'
            ]);
        } catch (\PDOException $e) {
            // Handle duplicate application
            return false;
        }
    }

    /**
     * Check if user already applied
     */
    public function hasApplied($jobId, $candidateId) {
        $sql = "SELECT COUNT(*) as count FROM {$this->table} 
                WHERE job_id = ? AND candidate_id = ?";
        $stmt = $this->query($sql, [$jobId, $candidateId]);
        $result = $stmt->fetch();
        return $result['count'] > 0;
    }

    /**
     * Get applications for a job
     */
    public function getJobApplications($jobId, $status = null) {
        $sql = "SELECT a.*, u.full_name, u.email, u.phone,
                       cp.resume_path, cp.skills, cp.experience_years
                FROM {$this->table} a
                JOIN users u ON a.candidate_id = u.id
                LEFT JOIN candidate_profiles cp ON u.id = cp.user_id
                WHERE a.job_id = ?";
        
        $params = [$jobId];
        
        if ($status) {
            $sql .= " AND a.status = ?";
            $params[] = $status;
        }
        
        $sql .= " ORDER BY a.applied_at DESC";
        
        $stmt = $this->query($sql, $params);
        return $stmt->fetchAll();
    }

    /**
     * Get candidate applications
     */
    public function getCandidateApplications($candidateId) {
        $sql = "SELECT a.*, j.title, j.location, j.job_type,
                       ep.company_name
                FROM {$this->table} a
                JOIN jobs j ON a.job_id = j.id
                JOIN users u ON j.employer_id = u.id
                LEFT JOIN employer_profiles ep ON u.id = ep.user_id
                WHERE a.candidate_id = ?
                ORDER BY a.applied_at DESC";
        
        $stmt = $this->query($sql, [$candidateId]);
        return $stmt->fetchAll();
    }

    /**
     * Update application status
     */
    public function updateStatus($applicationId, $status) {
        return $this->update($applicationId, ['status' => $status]);
    }

    /**
     * Get application statistics
     */
    public function getStatistics($userId = null, $role = null) {
        $sql = "SELECT 
                    a.status,
                    COUNT(*) as count
                FROM {$this->table} a";
        
        $params = [];
        
        if ($userId && $role === 'candidate') {
            $sql .= " WHERE a.candidate_id = ?";
            $params[] = $userId;
        } elseif ($userId && $role === 'employer') {
            $sql .= " JOIN jobs j ON a.job_id = j.id WHERE j.employer_id = ?";
            $params[] = $userId;
        }
        
        $sql .= " GROUP BY a.status";
        
        $stmt = $this->query($sql, $params);
        return $stmt->fetchAll();
    }

    /**
     * Get total applications count
     */
    public function getTotalCount($userId = null, $role = null) {
        $sql = "SELECT COUNT(*) as total FROM {$this->table} a";
        
        $params = [];
        
        if ($userId && $role === 'candidate') {
            $sql .= " WHERE a.candidate_id = ?";
            $params[] = $userId;
        } elseif ($userId && $role === 'employer') {
            $sql .= " JOIN jobs j ON a.job_id = j.id WHERE j.employer_id = ?";
            $params[] = $userId;
        }
        
        $stmt = $this->query($sql, $params);
        $result = $stmt->fetch();
        return $result['total'];
    }
}
