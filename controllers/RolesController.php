<?php
class RolesController
{
    public function index()
    {
        $roles = db()->select('roles', ['deleted_at' => null], 'id ASC');

        $rolePermissions = [];
        foreach ($roles as $role) {
            $perms = db()->getAll(
                "SELECT p.* FROM permissions p 
                 JOIN role_permissions rp ON p.id = rp.permission_id 
                 WHERE rp.role_id = ? AND p.deleted_at IS NULL 
                 ORDER BY p.module ASC",
                [$role['id']]
            );
            $rolePermissions[$role['id']] = $perms;
        }

        render('roles/index', ['roles' => $roles, 'rolePermissions' => $rolePermissions]);
    }

    public function create()
    {
        $permissions = db()->select('permissions', ['deleted_at' => null], 'module ASC, name ASC');

        // Group by module
        $grouped = [];
        foreach ($permissions as $perm) {
            $module = $perm['module'] ?? 'عمومی';
            if (!isset($grouped[$module])) {
                $grouped[$module] = [];
            }
            $grouped[$module][] = $perm;
        }

        render('roles/form', [
            'pageTitle' => 'ایجاد نقش جدید',
            'permissions' => $permissions,
            'groupedPermissions' => $grouped,
        ], 'main');
    }

    public function store()
    {
        $name = trim($_POST['name'] ?? '');
        $displayName = trim($_POST['display_name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $permissionIds = $_POST['permission_ids'] ?? [];

        if (empty($name)) {
            setFlash('error', 'نام نقش الزامی است.', 'error');
            redirect('admin/roles/create');
        }

        try {
            $roleId = db()->insert('roles', [
                'name' => $name,
                'display_name' => $displayName ?: $name,
                'description' => $description ?: null,
            ]);

            // Sync permissions
            if (!empty($permissionIds) && is_array($permissionIds)) {
                foreach ($permissionIds as $permId) {
                    db()->insert('role_permissions', [
                        'role_id' => $roleId,
                        'permission_id' => (int) $permId,
                    ]);
                }
            }

            logActivity('create', 'roles', $roleId, 'ایجاد نقش: ' . $name);
            setFlash('success', 'نقش با موفقیت ایجاد شد.');
        } catch (Exception $e) {
            setFlash('error', 'خطا در ایجاد نقش.', 'error');
        }

        redirect('admin/roles');
    }

    public function edit($id)
    {
        $role = db()->selectOne('roles', ['id' => $id, 'deleted_at' => null]);
        if (!$role) {
            setFlash('error', 'نقش مورد نظر یافت نشد.', 'error');
            redirect('admin/roles');
        }

        $permissions = db()->select('permissions', ['deleted_at' => null], 'module ASC, name ASC');

        // Group by module
        $grouped = [];
        foreach ($permissions as $perm) {
            $module = $perm['module'] ?? 'عمومی';
            if (!isset($grouped[$module])) {
                $grouped[$module] = [];
            }
            $grouped[$module][] = $perm;
        }

        // Get current role permissions
        $rolePerms = db()->getAll(
            "SELECT permission_id FROM role_permissions WHERE role_id = ?",
            [$id]
        );
        $itemPermissions = array_column($rolePerms, 'permission_id');

        render('roles/form', [
            'pageTitle' => 'ویرایش نقش',
            'item' => $role,
            'permissions' => $permissions,
            'groupedPermissions' => $grouped,
            'itemPermissions' => $itemPermissions,
            'isEdit' => true,
        ], 'main');
    }

    public function update($id)
    {
        $role = db()->selectOne('roles', ['id' => $id, 'deleted_at' => null]);
        if (!$role) {
            setFlash('error', 'نقش مورد نظر یافت نشد.', 'error');
            redirect('admin/roles');
        }

        $name = trim($_POST['name'] ?? '');
        $displayName = trim($_POST['display_name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $permissionIds = $_POST['permission_ids'] ?? [];

        if (empty($name)) {
            setFlash('error', 'نام نقش الزامی است.', 'error');
            redirect('admin/roles/' . $id . '/edit');
        }

        try {
            db()->updateById('roles', $id, [
                'name' => $name,
                'display_name' => $displayName ?: $name,
                'description' => $description ?: null,
            ]);

            // Sync permissions: delete old, insert new
            db()->delete('role_permissions', ['role_id' => $id]);

            if (!empty($permissionIds) && is_array($permissionIds)) {
                foreach ($permissionIds as $permId) {
                    db()->insert('role_permissions', [
                        'role_id' => (int) $id,
                        'permission_id' => (int) $permId,
                    ]);
                }
            }

            logActivity('update', 'roles', $id, 'ویرایش نقش: ' . $name);
            setFlash('success', 'نقش با موفقیت بروزرسانی شد.');
        } catch (Exception $e) {
            setFlash('error', 'خطا در بروزرسانی نقش.', 'error');
        }

        redirect('admin/roles');
    }

    public function delete($id)
    {
        $role = db()->selectOne('roles', ['id' => $id, 'deleted_at' => null]);
        if (!$role) {
            setFlash('error', 'نقش مورد نظر یافت نشد.', 'error');
            redirect('admin/roles');
        }

        try {
            softDelete('roles', $id);
            logActivity('delete', 'roles', $id, 'حذف نقش: ' . $role['name']);
            setFlash('success', 'نقش با موفقیت حذف شد.');
        } catch (Exception $e) {
            setFlash('error', 'خطا در حذف نقش.', 'error');
        }

        redirect('admin/roles');
    }
}