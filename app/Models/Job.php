<?php
namespace App\Models;

use App\Core\Model;

/**
 * Job Model
 */
class Job extends Model {
    protected $table = 'jobs';

    /**
     * Get jobs with pagination and filters
     */
    public function getJobs($filters = [], $page = 1, $perPage = 10) {
        $offset = ($page - 1) * $perPage;
        $params = [];
        
        $sql = "SELECT j.*, u.full_name as employer_name, ep.company_name 
                FROM {$this->table} j
                LEFT JOIN users u ON j.employer_id = u.id
                LEFT JOIN employer_profiles ep ON u.id = ep.user_id
                WHERE 1=1";

        if (!empty($filters['keyword'])) {
            $sql .= " AND (j.title LIKE ? OR j.description LIKE ?)";
            $keyword = '%' . $filters['keyword'] . '%';
            $params[] = $keyword;
            $params[] = $keyword;
        }

        if (!empty($filters['location'])) {
            $sql .= " AND j.location LIKE ?";
            $params[] = '%' . $filters['location'] . '%';
        }

        if (!empty($filters['job_type'])) {
            $sql .= " AND j.job_type = ?";
            $params[] = $filters['job_type'];
        }

        if (!empty($filters['category'])) {
            $sql .= " AND j.category = ?";
            $params[] = $filters['category'];
        }

        if (!empty($filters['status'])) {
            $sql .= " AND j.status = ?";
            $params[] = $filters['status'];
        }

        if (!empty($filters['employer_id'])) {
            $sql .= " AND j.employer_id = ?";
            $params[] = $filters['employer_id'];
        }

        $sql .= " ORDER BY j.created_at DESC LIMIT ? OFFSET ?";
        $params[] = $perPage;
        $params[] = $offset;

        $stmt = $this->query($sql, $params);
        return $stmt->fetchAll();
    }

    /**
     * Count jobs with filters
     */
    public function countJobs($filters = []) {
        $params = [];
        
        $sql = "SELECT COUNT(*) as total FROM {$this->table} WHERE 1=1";

        if (!empty($filters['keyword'])) {
            $sql .= " AND (title LIKE ? OR description LIKE ?)";
            $keyword = '%' . $filters['keyword'] . '%';
            $params[] = $keyword;
            $params[] = $keyword;
        }

        if (!empty($filters['location'])) {
            $sql .= " AND location LIKE ?";
            $params[] = '%' . $filters['location'] . '%';
        }

        if (!empty($filters['job_type'])) {
            $sql .= " AND job_type = ?";
            $params[] = $filters['job_type'];
        }

        if (!empty($filters['category'])) {
            $sql .= " AND category = ?";
            $params[] = $filters['category'];
        }

        if (!empty($filters['status'])) {
            $sql .= " AND status = ?";
            $params[] = $filters['status'];
        }

        if (!empty($filters['employer_id'])) {
            $sql .= " AND employer_id = ?";
            $params[] = $filters['employer_id'];
        }

        $stmt = $this->query($sql, $params);
        $result = $stmt->fetch();
        return $result['total'];
    }

    /**
     * Get job details with employer info
     */
    public function getJobDetails($jobId) {
        $sql = "SELECT j.*, u.full_name as employer_name, u.email as employer_email,
                       ep.company_name, ep.company_description, ep.company_website, ep.location as company_location
                FROM {$this->table} j
                LEFT JOIN users u ON j.employer_id = u.id
                LEFT JOIN employer_profiles ep ON u.id = ep.user_id
                WHERE j.id = ?";
        
        $stmt = $this->query($sql, [$jobId]);
        return $stmt->fetch();
    }

    /**
     * Increment job views
     */
    public function incrementViews($jobId) {
        $sql = "UPDATE {$this->table} SET views = views + 1 WHERE id = ?";
        return $this->query($sql, [$jobId]);
    }

    /**
     * Get recent jobs
     */
    public function getRecentJobs($limit = 5) {
        $sql = "SELECT j.*, ep.company_name 
                FROM {$this->table} j
                LEFT JOIN users u ON j.employer_id = u.id
                LEFT JOIN employer_profiles ep ON u.id = ep.user_id
                WHERE j.status = 'active'
                ORDER BY j.created_at DESC
                LIMIT ?";
        
        $stmt = $this->query($sql, [$limit]);
        return $stmt->fetchAll();
    }

    /**
     * Get job statistics
     */
    public function getStatistics($employerId = null) {
        $sql = "SELECT 
                    status,
                    COUNT(*) as count
                FROM {$this->table}";
        
        $params = [];
        if ($employerId) {
            $sql .= " WHERE employer_id = ?";
            $params[] = $employerId;
        }
        
        $sql .= " GROUP BY status";
        
        $stmt = $this->query($sql, $params);
        return $stmt->fetchAll();
    }
}
