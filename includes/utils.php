<?php
class Utils {
    public static function sanitizeInput($data) {
        if (is_array($data)) {
            return array_map([self::class, 'sanitizeInput'], $data);
        }
        return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
    }

    public static function formatPrice($price) {
        return CURRENCY . number_format($price, 2);
    }

    public static function generatePagination($currentPage, $totalPages, $searchQuery = '') {
        $html = '<div class="pagination">';
        
        for ($i = 1; $i <= $totalPages; $i++) {
            $active = $i == $currentPage ? "class='active'" : "";
            $queryString = $searchQuery ? "&search=$searchQuery" : "";
            $html .= "<a href='?page=$i$queryString' $active>$i</a>";
        }
        
        $html .= '</div>';
        return $html;
    }

    public static function respondJSON($data) {
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    public static function validateEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    public static function generateRandomString($length = 10) {
        return bin2hex(random_bytes($length));
    }
}