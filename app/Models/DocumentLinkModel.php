<?php

namespace App\Models;

use CodeIgniter\Model;

class DocumentLinkModel extends Model
{
    protected $table = 'document_links';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = ['source_type', 'source_id', 'target_type', 'target_id', 'relation_type', 'created_by', 'created_at'];
    protected $useTimestamps = false;

    /**
     * Fetch memo documents linked to a travel request, supporting both the legacy
     * request -> memo relation and the new memo -> request relation.
     */
    public function getMemosForTravel($travelId)
    {
        $db = \Config\Database::connect();
        $sql = "SELECT d.id, d.doc_no, d.title, d.status, d.created_at, dl.relation_type
                FROM document_links dl
                INNER JOIN documents d ON d.id = dl.target_id
                WHERE dl.source_type = 'travel_request'
                  AND dl.source_id = ?
                  AND dl.target_type = 'document'
                  AND d.doc_type = 'internal'
                UNION
                SELECT d.id, d.doc_no, d.title, d.status, d.created_at, dl.relation_type
                FROM document_links dl
                INNER JOIN documents d ON d.id = dl.source_id
                WHERE dl.source_type = 'document'
                  AND dl.source_id = d.id
                  AND dl.target_type = 'travel_request'
                  AND dl.target_id = ?
                  AND dl.relation_type IN ('request_memo', 'report_memo')
                  AND d.doc_type = 'internal'
                ORDER BY created_at DESC";
        return $db->query($sql, [(int)$travelId, (int)$travelId])->getResultArray();
    }

    /**
     * Find the travel request connected to a memo, supporting old and new links.
     */
    public function getTravelForMemo($documentId)
    {
        $db = \Config\Database::connect();
        $sql = "SELECT tr.id, tr.doc_no, tr.title, tr.status, tr.start_date, tr.end_date
                FROM document_links dl
                INNER JOIN travel_requests tr
                    ON tr.id = dl.source_id AND dl.source_type = 'travel_request'
                WHERE dl.target_type = 'document'
                  AND dl.target_id = ?
                  AND dl.relation_type = 'created_from'
                UNION
                SELECT tr.id, tr.doc_no, tr.title, tr.status, tr.start_date, tr.end_date
                FROM document_links dl
                INNER JOIN travel_requests tr
                    ON tr.id = dl.target_id AND dl.target_type = 'travel_request'
                WHERE dl.source_type = 'document'
                  AND dl.source_id = ?
                  AND dl.relation_type IN ('request_memo', 'report_memo')
                LIMIT 1";
        return $db->query($sql, [(int)$documentId, (int)$documentId])->getRowArray();
    }
}
