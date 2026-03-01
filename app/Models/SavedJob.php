<?php
namespace App\Models;

use App\Core\Model;

/**
 * SavedJob Model
 */
class SavedJob extends Model {
    protected $table = 'saved_jobs';

    /**
     * Save a job
     */
    public function saveJob($userId, $jobId) {
        try {
            return $this->create([
                'user_id' => $userId,
                'job_id' => $jobId
            ]);
        } catch (\PDOException $e) {
            return false;
        }
    }

    /**
     * Unsave a job
     */
    public function unsaveJob($userId, $jobId) {
        $sql = "DELETE FROM {$this->table} WHERE user_id = ? AND job_id = ?";
        $stmt = $this->query($sql, [$userId, $jobId]);
        return $stmt->rowCount() > 0;
    }

    /**
     * Check if job is saved
     */
    public function isSaved($userId, $jobId) {
        $sql = "SELECT COUNT(*) as count FROM {$this->table} 
                WHERE user_id = ? AND job_id = ?";
        $stmt = $this->query($sql, [$userId, $jobId]);
        $result = $stmt->fetch();
        return $result['count'] > 0;
    }

    /**
     * Get user's saved jobs
     */
    public function getUserSavedJobs($userId) {
        $sql = "SELECT j.*, ep.company_name, sj.saved_at
                FROM {$this->table} sj
                JOIN jobs j ON sj.job_id = j.id
                JOIN users u ON j.employer_id = u.id
                LEFT JOIN employer_profiles ep ON u.id = ep.user_id
                WHERE sj.user_id = ? AND j.status = 'active'
                ORDER BY sj.saved_at DESC";
        
        $stmt = $this->query($sql, [$userId]);
        return $stmt->fetchAll();
    }
}
