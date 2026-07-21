<?php
require_once __DIR__ . '/config.php';

// Parse request URI
if (isset($_GET['q']) && $_GET['q'] !== '') {
    // .htaccess rewrite: q contains the path relative to the project dir
    $requestUri = '/' . trim($_GET['q'], '/');
} else {
    // Direct access or no rewrite
    $requestUri = '/' . trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
    // Strip the APP_URL prefix (e.g. /wave2)
    $requestUri = preg_replace('#^' . preg_quote(APP_URL, '#') . '#', '', $requestUri);
}
$requestUri = $requestUri ?: '/';
$method = $_SERVER['REQUEST_METHOD'];

// Route matching
function routeMatch($pattern, $uri)
{
    $pattern = preg_replace('/\{[a-zA-Z]+\}/', '([^/]+)', $pattern);
    $pattern = '#^' . $pattern . '$#';
    return (bool) preg_match($pattern, $uri);
}

function getRouteParam($pattern, $uri, $index = 1)
{
    $pattern = preg_replace('/\{[a-zA-Z]+\}/', '([^/]+)', $pattern);
    if (preg_match('#^' . $pattern . '$#', $uri, $matches)) {
        return isset($matches[$index]) ? $matches[$index] : null;
    }
    return null;
}

// Include all controllers
$controllerFiles = glob(BASE_PATH . '/controllers/*.php');
foreach ($controllerFiles as $file) {
    require_once $file;
}

// ==================== ROUTES ====================

// --- Public ---
if ($requestUri === '/' && $method === 'GET') {
    $c = new HomeController();
    $c->index();
}
// --- Auth ---
elseif ($requestUri === '/auth/login' && $method === 'GET') {
    $c = new AuthController();
    $c->loginForm();
}
elseif ($requestUri === '/auth/login' && $method === 'POST') {
    $c = new AuthController();
    $c->login();
}
elseif ($requestUri === '/auth/loading' && $method === 'GET') {
    $c = new AuthController();
    $c->loading();
}
elseif ($requestUri === '/auth/logout' && $method === 'GET') {
    $c = new AuthController();
    $c->logout();
}
elseif ($requestUri === '/auth/register' && $method === 'GET') {
    $c = new AuthController();
    $c->registerForm();
}
elseif ($requestUri === '/auth/register' && $method === 'POST') {
    $c = new AuthController();
    $c->register();
}
// --- Admin Dashboard ---
elseif ($requestUri === '/admin/dashboard' && $method === 'GET') {
    requireRole(['admin', 'manager', 'receptionist', 'accountant']);
    $c = new DashboardController();
    $c->index();
}
// --- Members ---
elseif ($requestUri === '/admin/members' && $method === 'GET') {
    requireRole(['admin', 'manager', 'receptionist']);
    $c = new MembersController();
    $c->index();
}
elseif ($requestUri === '/admin/members/create' && $method === 'GET') {
    requireRole(['admin', 'manager', 'receptionist']);
    $c = new MembersController();
    $c->create();
}
elseif ($requestUri === '/admin/members/store' && $method === 'POST') {
    requireRole(['admin', 'manager', 'receptionist']);
    $c = new MembersController();
    $c->store();
}
elseif (routeMatch('/admin/members/{id}/edit', $requestUri) && $method === 'GET') {
    requireRole(['admin', 'manager', 'receptionist']);
    $id = getRouteParam('/admin/members/{id}/edit', $requestUri);
    $c = new MembersController();
    $c->edit($id);
}
elseif (routeMatch('/admin/members/{id}/update', $requestUri) && $method === 'POST') {
    requireRole(['admin', 'manager', 'receptionist']);
    $id = getRouteParam('/admin/members/{id}/update', $requestUri);
    $c = new MembersController();
    $c->update($id);
}
elseif (routeMatch('/admin/members/{id}/delete', $requestUri) && $method === 'POST') {
    requireRole(['admin', 'manager']);
    $id = getRouteParam('/admin/members/{id}/delete', $requestUri);
    $c = new MembersController();
    $c->delete($id);
}
elseif (routeMatch('/admin/members/{id}/approve', $requestUri) && $method === 'POST') {
    requireRole(['admin', 'manager', 'receptionist']);
    $id = getRouteParam('/admin/members/{id}/approve', $requestUri);
    $c = new MembersController();
    $c->approve($id);
}
elseif (routeMatch('/admin/members/{id}/detail', $requestUri) && $method === 'GET') {
    requireRole(['admin', 'manager', 'receptionist']);
    $id = getRouteParam('/admin/members/{id}/detail', $requestUri);
    $c = new MembersController();
    $c->detail($id);
}
elseif ($requestUri === '/admin/members/pending' && $method === 'GET') {
    requireRole(['admin', 'manager', 'receptionist']);
    $c = new MembersController();
    $c->pendingMembers();
}
elseif (routeMatch('/admin/members/{id}/reject', $requestUri) && $method === 'POST') {
    requireRole(['admin', 'manager']);
    $id = getRouteParam('/admin/members/{id}/reject', $requestUri);
    $c = new MembersController();
    $c->reject($id);
}
// --- Memberships ---
elseif ($requestUri === '/admin/memberships' && $method === 'GET') {
    requireRole(['admin', 'manager', 'receptionist']);
    $c = new MembershipsController();
    $c->index();
}
elseif ($requestUri === '/admin/memberships/create' && $method === 'GET') {
    requireRole(['admin', 'manager']);
    $c = new MembershipsController();
    $c->create();
}
elseif ($requestUri === '/admin/memberships/store' && $method === 'POST') {
    requireRole(['admin', 'manager']);
    $c = new MembershipsController();
    $c->store();
}
elseif (routeMatch('/admin/memberships/{id}/edit', $requestUri) && $method === 'GET') {
    requireRole(['admin', 'manager']);
    $id = getRouteParam('/admin/memberships/{id}/edit', $requestUri);
    $c = new MembershipsController();
    $c->edit($id);
}
elseif (routeMatch('/admin/memberships/{id}/update', $requestUri) && $method === 'POST') {
    requireRole(['admin', 'manager']);
    $id = getRouteParam('/admin/memberships/{id}/update', $requestUri);
    $c = new MembershipsController();
    $c->update($id);
}
elseif (routeMatch('/admin/memberships/{id}/delete', $requestUri) && $method === 'POST') {
    requireRole(['admin', 'manager']);
    $id = getRouteParam('/admin/memberships/{id}/delete', $requestUri);
    $c = new MembershipsController();
    $c->delete($id);
}
// --- Classes ---
elseif ($requestUri === '/admin/classes' && $method === 'GET') {
    requireRole(['admin', 'manager', 'receptionist']);
    $c = new ClassesController();
    $c->index();
}
elseif ($requestUri === '/admin/classes/create' && $method === 'GET') {
    requireRole(['admin', 'manager']);
    $c = new ClassesController();
    $c->create();
}
elseif ($requestUri === '/admin/classes/store' && $method === 'POST') {
    requireRole(['admin', 'manager']);
    $c = new ClassesController();
    $c->store();
}
elseif (routeMatch('/admin/classes/{id}/edit', $requestUri) && $method === 'GET') {
    requireRole(['admin', 'manager']);
    $id = getRouteParam('/admin/classes/{id}/edit', $requestUri);
    $c = new ClassesController();
    $c->edit($id);
}
elseif (routeMatch('/admin/classes/{id}/update', $requestUri) && $method === 'POST') {
    requireRole(['admin', 'manager']);
    $id = getRouteParam('/admin/classes/{id}/update', $requestUri);
    $c = new ClassesController();
    $c->update($id);
}
elseif (routeMatch('/admin/classes/{id}/delete', $requestUri) && $method === 'POST') {
    requireRole(['admin', 'manager']);
    $id = getRouteParam('/admin/classes/{id}/delete', $requestUri);
    $c = new ClassesController();
    $c->delete($id);
}
// --- Coaches ---
elseif ($requestUri === '/admin/coaches' && $method === 'GET') {
    requireRole(['admin', 'manager']);
    $c = new CoachesController();
    $c->index();
}
elseif ($requestUri === '/admin/coaches/create' && $method === 'GET') {
    requireRole(['admin', 'manager']);
    $c = new CoachesController();
    $c->create();
}
elseif ($requestUri === '/admin/coaches/store' && $method === 'POST') {
    requireRole(['admin', 'manager']);
    $c = new CoachesController();
    $c->store();
}
elseif (routeMatch('/admin/coaches/{id}/edit', $requestUri) && $method === 'GET') {
    requireRole(['admin', 'manager']);
    $id = getRouteParam('/admin/coaches/{id}/edit', $requestUri);
    $c = new CoachesController();
    $c->edit($id);
}
elseif (routeMatch('/admin/coaches/{id}/update', $requestUri) && $method === 'POST') {
    requireRole(['admin', 'manager']);
    $id = getRouteParam('/admin/coaches/{id}/update', $requestUri);
    $c = new CoachesController();
    $c->update($id);
}
elseif (routeMatch('/admin/coaches/{id}/delete', $requestUri) && $method === 'POST') {
    requireRole(['admin', 'manager']);
    $id = getRouteParam('/admin/coaches/{id}/delete', $requestUri);
    $c = new CoachesController();
    $c->delete($id);
}
// --- Events ---
elseif ($requestUri === '/admin/events' && $method === 'GET') {
    requireRole(['admin', 'manager']);
    $c = new EventsController();
    $c->index();
}
elseif ($requestUri === '/admin/events/create' && $method === 'GET') {
    requireRole(['admin', 'manager']);
    $c = new EventsController();
    $c->create();
}
elseif ($requestUri === '/admin/events/store' && $method === 'POST') {
    requireRole(['admin', 'manager']);
    $c = new EventsController();
    $c->store();
}
elseif (routeMatch('/admin/events/{id}/edit', $requestUri) && $method === 'GET') {
    requireRole(['admin', 'manager']);
    $id = getRouteParam('/admin/events/{id}/edit', $requestUri);
    $c = new EventsController();
    $c->edit($id);
}
elseif (routeMatch('/admin/events/{id}/update', $requestUri) && $method === 'POST') {
    requireRole(['admin', 'manager']);
    $id = getRouteParam('/admin/events/{id}/update', $requestUri);
    $c = new EventsController();
    $c->update($id);
}
elseif (routeMatch('/admin/events/{id}/delete', $requestUri) && $method === 'POST') {
    requireRole(['admin', 'manager']);
    $id = getRouteParam('/admin/events/{id}/delete', $requestUri);
    $c = new EventsController();
    $c->delete($id);
}
// --- Payments ---
elseif ($requestUri === '/admin/payments' && $method === 'GET') {
    requireRole(['admin', 'manager', 'accountant']);
    $c = new PaymentsController();
    $c->index();
}
elseif ($requestUri === '/admin/payments/create' && $method === 'GET') {
    requireRole(['admin', 'manager', 'accountant']);
    $c = new PaymentsController();
    $c->create();
}
elseif ($requestUri === '/admin/payments/store' && $method === 'POST') {
    requireRole(['admin', 'manager', 'accountant']);
    $c = new PaymentsController();
    $c->store();
}
elseif (routeMatch('/admin/payments/{id}/edit', $requestUri) && $method === 'GET') {
    requireRole(['admin', 'manager', 'accountant']);
    $id = getRouteParam('/admin/payments/{id}/edit', $requestUri);
    $c = new PaymentsController();
    $c->edit($id);
}
elseif (routeMatch('/admin/payments/{id}/update', $requestUri) && $method === 'POST') {
    requireRole(['admin', 'manager', 'accountant']);
    $id = getRouteParam('/admin/payments/{id}/update', $requestUri);
    $c = new PaymentsController();
    $c->update($id);
}
elseif (routeMatch('/admin/payments/{id}/delete', $requestUri) && $method === 'POST') {
    requireRole(['admin', 'manager', 'accountant']);
    $id = getRouteParam('/admin/payments/{id}/delete', $requestUri);
    $c = new PaymentsController();
    $c->delete($id);
}
// --- Equipment ---
elseif ($requestUri === '/admin/equipment' && $method === 'GET') {
    requireRole(['admin', 'manager']);
    $c = new EquipmentController();
    $c->index();
}
elseif ($requestUri === '/admin/equipment/create' && $method === 'GET') {
    requireRole(['admin', 'manager']);
    $c = new EquipmentController();
    $c->create();
}
elseif ($requestUri === '/admin/equipment/store' && $method === 'POST') {
    requireRole(['admin', 'manager']);
    $c = new EquipmentController();
    $c->store();
}
elseif (routeMatch('/admin/equipment/{id}/edit', $requestUri) && $method === 'GET') {
    requireRole(['admin', 'manager']);
    $id = getRouteParam('/admin/equipment/{id}/edit', $requestUri);
    $c = new EquipmentController();
    $c->edit($id);
}
elseif (routeMatch('/admin/equipment/{id}/update', $requestUri) && $method === 'POST') {
    requireRole(['admin', 'manager']);
    $id = getRouteParam('/admin/equipment/{id}/update', $requestUri);
    $c = new EquipmentController();
    $c->update($id);
}
elseif (routeMatch('/admin/equipment/{id}/delete', $requestUri) && $method === 'POST') {
    requireRole(['admin', 'manager']);
    $id = getRouteParam('/admin/equipment/{id}/delete', $requestUri);
    $c = new EquipmentController();
    $c->delete($id);
}
// --- Insurance ---
elseif ($requestUri === '/admin/insurance' && $method === 'GET') {
    requireRole(['admin', 'manager', 'receptionist']);
    $c = new InsuranceController();
    $c->index();
}
elseif ($requestUri === '/admin/insurance/create' && $method === 'GET') {
    requireRole(['admin', 'manager', 'receptionist']);
    $c = new InsuranceController();
    $c->create();
}
elseif ($requestUri === '/admin/insurance/store' && $method === 'POST') {
    requireRole(['admin', 'manager', 'receptionist']);
    $c = new InsuranceController();
    $c->store();
}
elseif (routeMatch('/admin/insurance/{id}/edit', $requestUri) && $method === 'GET') {
    requireRole(['admin', 'manager', 'receptionist']);
    $id = getRouteParam('/admin/insurance/{id}/edit', $requestUri);
    $c = new InsuranceController();
    $c->edit($id);
}
elseif (routeMatch('/admin/insurance/{id}/update', $requestUri) && $method === 'POST') {
    requireRole(['admin', 'manager', 'receptionist']);
    $id = getRouteParam('/admin/insurance/{id}/update', $requestUri);
    $c = new InsuranceController();
    $c->update($id);
}
elseif (routeMatch('/admin/insurance/{id}/delete', $requestUri) && $method === 'POST') {
    requireRole(['admin', 'manager', 'receptionist']);
    $id = getRouteParam('/admin/insurance/{id}/delete', $requestUri);
    $c = new InsuranceController();
    $c->delete($id);
}
// --- Tickets ---
elseif ($requestUri === '/admin/tickets' && $method === 'GET') {
    requireRole(['admin', 'manager', 'receptionist']);
    $c = new TicketsController();
    $c->index();
}
elseif (routeMatch('/admin/tickets/{id}', $requestUri) && $method === 'GET') {
    requireRole(['admin', 'manager', 'receptionist']);
    $id = getRouteParam('/admin/tickets/{id}', $requestUri);
    $c = new TicketsController();
    $c->show($id);
}
elseif (routeMatch('/admin/tickets/{id}/reply', $requestUri) && $method === 'POST') {
    requireRole(['admin', 'manager', 'receptionist']);
    $id = getRouteParam('/admin/tickets/{id}/reply', $requestUri);
    $c = new TicketsController();
    $c->reply($id);
}
// --- Notifications ---
elseif ($requestUri === '/admin/notifications' && $method === 'GET') {
    requireRole(['admin', 'manager']);
    $c = new NotificationsController();
    $c->index();
}
elseif ($requestUri === '/admin/notifications/create' && $method === 'GET') {
    requireRole(['admin', 'manager']);
    $c = new NotificationsController();
    $c->create();
}
elseif ($requestUri === '/admin/notifications/store' && $method === 'POST') {
    requireRole(['admin', 'manager']);
    $c = new NotificationsController();
    $c->store();
}
// --- Reports ---
elseif ($requestUri === '/admin/reports' && $method === 'GET') {
    requireRole(['admin', 'manager', 'accountant']);
    $c = new ReportsController();
    $c->index();
}
// --- Settings ---
elseif ($requestUri === '/admin/settings' && $method === 'GET') {
    requireRole(['admin']);
    $c = new SettingsController();
    $c->index();
}
elseif ($requestUri === '/admin/settings/update' && $method === 'POST') {
    requireRole(['admin']);
    $c = new SettingsController();
    $c->update();
}
// --- Roles ---
elseif ($requestUri === '/admin/roles' && $method === 'GET') {
    requireRole(['admin']);
    $c = new RolesController();
    $c->index();
}
elseif ($requestUri === '/admin/roles/create' && $method === 'GET') {
    requireRole(['admin']);
    $c = new RolesController();
    $c->create();
}
elseif ($requestUri === '/admin/roles/store' && $method === 'POST') {
    requireRole(['admin']);
    $c = new RolesController();
    $c->store();
}
elseif (routeMatch('/admin/roles/{id}/edit', $requestUri) && $method === 'GET') {
    requireRole(['admin']);
    $id = getRouteParam('/admin/roles/{id}/edit', $requestUri);
    $c = new RolesController();
    $c->edit($id);
}
elseif (routeMatch('/admin/roles/{id}/update', $requestUri) && $method === 'POST') {
    requireRole(['admin']);
    $id = getRouteParam('/admin/roles/{id}/update', $requestUri);
    $c = new RolesController();
    $c->update($id);
}
elseif (routeMatch('/admin/roles/{id}/delete', $requestUri) && $method === 'POST') {
    requireRole(['admin']);
    $id = getRouteParam('/admin/roles/{id}/delete', $requestUri);
    $c = new RolesController();
    $c->delete($id);
}
// --- Activity Logs ---
elseif ($requestUri === '/admin/activity-logs' && $method === 'GET') {
    requireRole(['admin']);
    $c = new ActivityLogsController();
    $c->index();
}
// --- Portal ---
elseif ($requestUri === '/portal/dashboard' && $method === 'GET') {
    requireRole(['member']);
    $c = new PortalController();
    $c->dashboard();
}
elseif ($requestUri === '/portal/profile' && $method === 'GET') {
    requireRole(['member']);
    $c = new PortalController();
    $c->profile();
}
elseif ($requestUri === '/portal/profile/update' && $method === 'POST') {
    requireRole(['member']);
    $c = new PortalController();
    $c->profileUpdate();
}
elseif ($requestUri === '/portal/classes' && $method === 'GET') {
    requireRole(['member']);
    $c = new PortalController();
    $c->classes();
}
elseif (routeMatch('/portal/classes/{id}/register', $requestUri) && $method === 'POST') {
    requireRole(['member']);
    $id = getRouteParam('/portal/classes/{id}/register', $requestUri);
    $c = new PortalController();
    $c->classRegister($id);
}
elseif (routeMatch('/portal/classes/{id}/unregister', $requestUri) && $method === 'POST') {
    requireRole(['member']);
    $id = getRouteParam('/portal/classes/{id}/unregister', $requestUri);
    $c = new PortalController();
    $c->classUnregister($id);
}
elseif ($requestUri === '/portal/payments' && $method === 'GET') {
    requireRole(['member']);
    $c = new PortalController();
    $c->payments();
}
elseif ($requestUri === '/portal/insurance' && $method === 'GET') {
    requireRole(['member']);
    $c = new PortalController();
    $c->insurance();
}
elseif ($requestUri === '/portal/insurance/upload' && $method === 'POST') {
    requireRole(['member']);
    $c = new PortalController();
    $c->uploadInsurance();
}
elseif ($requestUri === '/portal/attendance' && $method === 'GET') {
    requireRole(['member']);
    $c = new PortalController();
    $c->attendance();
}
elseif ($requestUri === '/portal/tickets' && $method === 'GET') {
    requireRole(['member']);
    $c = new PortalController();
    $c->tickets();
}
elseif ($requestUri === '/portal/tickets/create' && $method === 'GET') {
    requireRole(['member']);
    $c = new PortalController();
    $c->ticketForm();
}
elseif ($requestUri === '/portal/tickets/store' && $method === 'POST') {
    requireRole(['member']);
    $c = new PortalController();
    $c->ticketStore();
}
elseif (routeMatch('/portal/tickets/{id}', $requestUri) && $method === 'GET') {
    requireRole(['member']);
    $c = new PortalController();
    $c->ticketDetail(getRouteParam('/portal/tickets/{id}', $requestUri));
}
elseif ($requestUri === '/portal/notifications' && $method === 'GET') {
    requireRole(['member']);
    $c = new PortalController();
    $c->notifications();
}
// --- Assignments ---
elseif ($requestUri === '/admin/assignments' && $method === 'GET') {
    requireRole(['admin', 'manager', 'receptionist']);
    $c = new AssignmentsController();
    $c->index();
}
elseif ($requestUri === '/admin/assignments/members' && $method === 'GET') {
    requireRole(['admin', 'manager', 'receptionist']);
    $c = new AssignmentsController();
    $c->getActiveMembers();
}
elseif ($requestUri === '/admin/assignments/assign' && $method === 'POST') {
    requireRole(['admin', 'manager', 'receptionist']);
    $c = new AssignmentsController();
    $c->assignClass();
}
elseif ($requestUri === '/admin/assignments/remove' && $method === 'POST') {
    requireRole(['admin', 'manager', 'receptionist']);
    $c = new AssignmentsController();
    $c->removeClass();
}
// --- Coach Panel ---
elseif ($requestUri === '/coach/dashboard' && $method === 'GET') {
    requireRole(['coach']);
    $c = new CoachController();
    $c->dashboard();
}
elseif ($requestUri === '/coach/classes' && $method === 'GET') {
    requireRole(['coach']);
    $c = new CoachController();
    $c->classes();
}
elseif (routeMatch('/coach/class-students/{id}', $requestUri) && $method === 'GET') {
    requireRole(['coach']);
    $id = getRouteParam('/coach/class-students/{id}', $requestUri);
    $c = new CoachController();
    $c->classStudents($id);
}
elseif ($requestUri === '/coach/attendance' && $method === 'GET') {
    requireRole(['coach']);
    $c = new CoachController();
    $c->attendance();
}
elseif ($requestUri === '/coach/attendance-form' && $method === 'GET') {
    requireRole(['coach']);
    $c = new CoachController();
    $c->attendanceForm();
}
elseif ($requestUri === '/coach/attendance/save' && $method === 'POST') {
    requireRole(['coach']);
    $c = new CoachController();
    $c->saveAttendance();
}
elseif ($requestUri === '/coach/profile' && $method === 'GET') {
    requireRole(['coach']);
    $c = new CoachController();
    $c->profile();
}
elseif ($requestUri === '/coach/profile/update' && $method === 'POST') {
    requireRole(['coach']);
    $c = new CoachController();
    $c->profileUpdate();
}
elseif ($requestUri === '/coach/profile/change-password' && $method === 'POST') {
    requireRole(['coach']);
    $c = new CoachController();
    $c->changePassword();
}
elseif ($requestUri === '/coach/notifications' && $method === 'GET') {
    requireRole(['coach']);
    $c = new CoachController();
    $c->notifications();
}
// --- Registration (public) ---
elseif ($requestUri === '/registration' && $method === 'GET') {
    $c = new RegistrationController();
    $c->index();
}
elseif ($requestUri === '/registration/store' && $method === 'POST') {
    $c = new RegistrationController();
    $c->store();
}
// --- Upload ---
elseif ($requestUri === '/upload' && $method === 'POST') {
    requireAuth();
    $c = new UploadController();
    $c->upload();
}
// --- Slider Management ---
elseif ($requestUri === '/admin/sliders' && $method === 'GET') {
    requireRole(['admin', 'manager']);
    $c = new SliderController();
    $c->index();
}
elseif ($requestUri === '/admin/sliders/create' && $method === 'GET') {
    requireRole(['admin', 'manager']);
    $c = new SliderController();
    $c->create();
}
elseif ($requestUri === '/admin/sliders/store' && $method === 'POST') {
    requireRole(['admin', 'manager']);
    $c = new SliderController();
    $c->store();
}
elseif (routeMatch('/admin/sliders/{id}/edit', $requestUri) && $method === 'GET') {
    requireRole(['admin', 'manager']);
    $c = new SliderController();
    $id = getRouteParam('/admin/sliders/{id}/edit', $requestUri);
    $c->edit($id);
}
elseif (routeMatch('/admin/sliders/{id}/update', $requestUri) && $method === 'POST') {
    requireRole(['admin', 'manager']);
    $c = new SliderController();
    $id = getRouteParam('/admin/sliders/{id}/update', $requestUri);
    $c->update($id);
}
elseif (routeMatch('/admin/sliders/{id}/delete', $requestUri) && $method === 'POST') {
    requireRole(['admin', 'manager']);
    $c = new SliderController();
    $id = getRouteParam('/admin/sliders/{id}/delete', $requestUri);
    $c->delete($id);
}
// API: Get active sliders for portal/coach display
elseif ($requestUri === '/api/sliders/active' && $method === 'GET') {
    $c = new SliderController();
    $c->getActiveSliders();
}
// API: Toggle slider status
elseif (routeMatch('/admin/sliders/{id}/toggle', $requestUri) && $method === 'POST') {
    requireRole(['admin', 'manager']);
    $c = new SliderController();
    $id = getRouteParam('/admin/sliders/{id}/toggle', $requestUri);
    $c->toggleStatus($id);
}

// --- Help Guide Management ---
elseif ($requestUri === '/admin/help-guides' && $method === 'GET') {
    requireRole(['admin', 'manager']);
    $c = new HelpGuideController();
    $c->index();
}
elseif ($requestUri === '/admin/help-guides/create' && $method === 'GET') {
    requireRole(['admin', 'manager']);
    $c = new HelpGuideController();
    $c->create();
}
elseif ($requestUri === '/admin/help-guides/store' && $method === 'POST') {
    requireRole(['admin', 'manager']);
    $c = new HelpGuideController();
    $c->store();
}
elseif (routeMatch('/admin/help-guides/{id}/edit', $requestUri) && $method === 'GET') {
    requireRole(['admin', 'manager']);
    $c = new HelpGuideController();
    $id = getRouteParam('/admin/help-guides/{id}/edit', $requestUri);
    $c->edit($id);
}
elseif (routeMatch('/admin/help-guides/{id}/update', $requestUri) && $method === 'POST') {
    requireRole(['admin', 'manager']);
    $c = new HelpGuideController();
    $id = getRouteParam('/admin/help-guides/{id}/update', $requestUri);
    $c->update($id);
}
elseif (routeMatch('/admin/help-guides/{id}/delete', $requestUri) && $method === 'POST') {
    requireRole(['admin', 'manager']);
    $c = new HelpGuideController();
    $id = getRouteParam('/admin/help-guides/{id}/delete', $requestUri);
    $c->delete($id);
}

// 404
else {
    http_response_code(404);
    echo '<!DOCTYPE html><html dir="rtl"><head><meta charset="utf-8"><title>404</title></head>';
    echo '<body style="font-family:Vazirmatn,Tahoma;text-align:center;padding:60px;">';
    echo '<h1>404 - صفحه مورد نظر یافت نشد</h1>';
    echo '<a href="' . url('/') . '">بازگشت به خانه</a>';
    echo '</body></html>';
}