<?php
// === URL & Asset Helpers ===
function url($path = '')
{
    return APP_URL . '/' . ltrim($path, '/');
}

function asset($path = '')
{
    return url('public/' . ltrim($path, '/'));
}

function redirect($path)
{
    header('Location: ' . url($path));
    exit;
}

// === Flash Messages ===
function setFlash($key, $message, $type = 'success')
{
    $_SESSION['flash'] = [
        'key' => $key,
        'message' => $message,
        'type' => $type,
    ];
}

function getFlash()
{
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

function renderFlash()
{
    $flash = getFlash();
    if (!$flash) return '';
    $colors = [
        'error'   => '#dc3545',
        'danger'  => '#dc3545',
        'warning' => '#f59e0b',
        'success' => '#10b981',
    ];
    $color = $colors[$flash['type']] ?? '#10b981';
    $icon = ($flash['type'] === 'error' || $flash['type'] === 'danger') ? 'fa-circle-exclamation' 
          : (($flash['type'] === 'warning') ? 'fa-triangle-exclamation' : 'fa-circle-check');
    return '<div class="alert" style="background:' . $color . ';color:#fff;padding:12px 20px;border-radius:8px;margin-bottom:20px;display:flex;align-items:center;gap:8px;">'
        . '<i class="fas ' . $icon . '"></i> '
        . e($flash['message']) . '</div>';
}

// === Date/Time Formatters ===
function formatDate($dateStr)
{
    return jalali()->formatDate($dateStr);
}

function formatDateTime($dateStr)
{
    return jalali()->formatDateTime($dateStr);
}

function formatMonth($month)
{
    return jalali()->getMonthName((int)$month);
}

// === Number/Currency ===
function formatCurrency($amount)
{
    return number_format((float)$amount, 0) . ' تومان';
}

function formatNumber($num)
{
    return number_format((float)$num, 0);
}

// === Old Input (for form repopulation) ===
function old($key, $default = '')
{
    if (isset($_SESSION['old_input'][$key])) {
        $val = $_SESSION['old_input'][$key];
        unset($_SESSION['old_input'][$key]);
        return $val;
    }
    return $default;
}

function flashOldInput()
{
    $_SESSION['old_input'] = $_POST ?? [];
}

// === Soft Delete Helper ===
function softDelete($table, $id)
{
    return db()->softDelete($table, $id);
}

// === Security ===
function e($str)
{
    return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8');
}

// === Jalali to Gregorian Conversion ===
function jalaliToGregorian($jalaliStr)
{
    if (empty($jalaliStr)) return null;
    // Convert Persian digits to Latin
    $persianDigits = ['۰','۱','۲','۳','۴','۵','۶','۷','۸','۹'];
    $latinDigits = ['0','1','2','3','4','5','6','7','8','9'];
    $str = str_replace($persianDigits, $latinDigits, $jalaliStr);
    $parts = explode('/', $str);
    if (count($parts) !== 3) return null;
    list($jy, $jm, $jd) = array_map('intval', $parts);
    list($gy, $gm, $gd) = jalali()->toGregorian($jy, $jm, $jd);
    return sprintf('%04d-%02d-%02d', $gy, $gm, $gd);
}

// === Activity Log ===
function logActivity($action, $module, $recordId = null, $description = '')
{
    try {
        db()->insert('activity_logs', [
            'user_id' => auth()->id() ?: 0,
            'action' => $action,
            'module' => $module,
            'record_id' => $recordId,
            'description' => $description,
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? '',
            'created_at' => date('Y-m-d H:i:s'),
        ]);
    } catch (Exception $ex) {
        // Silent fail
    }
}

// === Pagination ===
function pagination($current, $total, $perPage, $baseUrl)
{
    $totalPages = ceil($total / $perPage);
    if ($totalPages <= 1) return '';

    $html = '<div class="pagination">';
    if ($current > 1) {
        $html .= '<a href="' . url($baseUrl . '?page=' . ($current - 1)) . '" class="page-link">قبلی</a>';
    }
    for ($i = 1; $i <= $totalPages; $i++) {
        $active = ($i === $current) ? ' active' : '';
        $html .= '<a href="' . url($baseUrl . '?page=' . $i) . '" class="page-link' . $active . '">' . $i . '</a>';
    }
    if ($current < $totalPages) {
        $html .= '<a href="' . url($baseUrl . '?page=' . ($current + 1)) . '" class="page-link">بعدی</a>';
    }
    $html .= '</div>';
    return $html;
}

// === View Rendering ===
function render($viewPath, $data = [], $layout = 'main')
{
    extract($data);
    ob_start();
    include BASE_PATH . '/views/' . $viewPath . '.php';
    $content = ob_get_clean();
    include BASE_PATH . '/views/layouts/' . $layout . '.php';
}

// === Auth Check Helpers ===
function requireAuth()
{
    if (!auth()->check()) {
        setFlash('error', 'لطفاً ابتدا وارد شوید.', 'error');
        redirect('auth/login');
    }
}

function requireRole($roles)
{
    requireAuth();
    $userRoles = auth()->user()['roles'] ?? [];
    if (!is_array($roles)) $roles = [$roles];
    $hasRole = false;
    foreach ($roles as $r) {
        if (in_array($r, $userRoles)) {
            $hasRole = true;
            break;
        }
    }
    if (!$hasRole) {
        setFlash('error', 'شما دسترسی به این بخش ندارید.', 'error');
        redirect('admin/dashboard');
    }
}

// === Settings Helper ===
function getSetting($key, $default = '')
{
    static $settingsCache = null;
    if ($settingsCache === null) {
        try {
            $rows = db()->select('settings', ['deleted_at' => null]);
            $settingsCache = [];
            foreach ($rows as $row) {
                $settingsCache[$row['key']] = $row['value'];
            }
        } catch (Exception $e) {
            $settingsCache = [];
        }
    }
    return $settingsCache[$key] ?? $default;
}

function getClubSettings()
{
    static $cache = null;
    if ($cache !== null) return $cache;
    try {
        $rows = db()->select('settings', ['deleted_at' => null]);
        $cache = [];
        foreach ($rows as $row) {
            $cache[$row['key']] = $row['value'];
        }
    } catch (Exception $e) {
        $cache = [];
    }
    return $cache;
}