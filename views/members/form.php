<?php $isEdit = isset($member) && !empty($member); ?>

<div class="page-header-row">
    <div class="page-header">
        <h2 class="page-title"><?php echo $isEdit ? 'ویرایش عضو' : 'ثبت عضو جدید'; ?></h2>
        <p><?php echo $isEdit ? 'اطلاعات عضو را ویرایش کنید' : 'اطلاعات عضو جدید را وارد کنید'; ?></p>
    </div>
    <div class="page-header-actions">
        <a href="<?php echo url('admin/members'); ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-right"></i>
            بازگشت به لیست
        </a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="<?php echo $isEdit ? url('admin/members/' . $member['id'] . '/update') : url('admin/members/store'); ?>">
            <?php echo csrf_field(); ?>

            <!-- Row 1: first_name, last_name -->
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">نام <span class="required">*</span></label>
                    <input type="text" name="first_name" class="form-input" required value="<?php echo e($isEdit ? $member['first_name'] : old('first_name') ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label class="form-label">نام خانوادگی <span class="required">*</span></label>
                    <input type="text" name="last_name" class="form-input" required value="<?php echo e($isEdit ? $member['last_name'] : old('last_name') ?? ''); ?>">
                </div>
            </div>

            <!-- Row 2: national_code, phone -->
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">کد ملی <span class="required">*</span></label>
                    <input type="text" name="national_code" class="form-input" required maxlength="10" pattern="\d{10}" placeholder="۱۰ رقم" value="<?php echo e($isEdit ? $member['national_code'] : old('national_code') ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label class="form-label">تلفن <span class="required">*</span></label>
                    <input type="tel" name="phone" class="form-input" required value="<?php echo e($isEdit ? $member['phone'] : old('phone') ?? ''); ?>">
                </div>
            </div>

            <!-- Row 3: email, birth_date -->
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">ایمیل</label>
                    <input type="email" name="email" class="form-input" dir="ltr" style="text-align:right;" value="<?php echo e($isEdit ? $member['email'] : old('email') ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label class="form-label">تاریخ تولد</label>
                    <input type="text" name="birth_date" class="form-input jalali-date" data-datepicker placeholder="مثلاً ۱۳۷۰/۰۶/۱۵" value="<?php echo e($isEdit ? $member['birth_date'] : old('birth_date') ?? ''); ?>">
                </div>
            </div>

            <!-- Row 4: gender, emergency_contact -->
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">جنسیت</label>
                    <select name="gender" class="form-input">
                        <option value="">انتخاب کنید</option>
                        <option value="male" <?php echo ($isEdit && $member['gender'] === 'male') || (!$isEdit && old('gender') === 'male') ? 'selected' : ''; ?>>مرد</option>
                        <option value="female" <?php echo ($isEdit && $member['gender'] === 'female') || (!$isEdit && old('gender') === 'female') ? 'selected' : ''; ?>>زن</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">تماس اضطراری</label>
                    <input type="text" name="emergency_contact" class="form-input" value="<?php echo e($isEdit ? $member['emergency_contact'] : old('emergency_contact') ?? ''); ?>">
                </div>
            </div>

            <!-- Row 5: address -->
            <div class="form-group">
                <label class="form-label">آدرس</label>
                <textarea name="address" class="form-textarea" rows="3"><?php echo e($isEdit ? $member['address'] : old('address') ?? ''); ?></textarea>
            </div>

            <!-- Medical Information Section -->
            <div class="section-divider" style="border-top:2px solid #e2e8f0;margin:24px 0 16px;padding-top:16px;">
                <h3 style="font-size:1rem;font-weight:600;margin-bottom:12px;color:#334155;">
                    <i class="fas fa-heartbeat" style="color:#ef4444;margin-left:6px;"></i>
                    اطلاعات پزشکی
                </h3>
            </div>

            <!-- Row: blood_type, referral_number -->
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">گروه خونی</label>
                    <select name="blood_type" class="form-select">
                        <option value="">نامشخص</option>
                        <?php
                        $bloodTypes = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];
                        foreach ($bloodTypes as $bt):
                        ?>
                        <option value="<?php echo e($bt); ?>" <?php echo ($isEdit && ($member['blood_type'] ?? '') === $bt) || (!$isEdit && old('blood_type') === $bt) ? 'selected' : ''; ?>>
                            <?php echo e($bt); ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">شماره معرف</label>
                    <input type="text" name="referral_number" class="form-input" placeholder="شماره معرف" value="<?php echo e($isEdit ? ($member['referral_number'] ?? '') : old('referral_number') ?? ''); ?>">
                </div>
            </div>

            <!-- Row: allergies -->
            <div class="form-group">
                <label class="form-label">آلرژی‌ها و حساسیت‌ها</label>
                <textarea name="allergies" class="form-textarea" rows="2" placeholder="آلرژی‌ها و حساسیت‌ها..."><?php echo e($isEdit ? ($member['allergies'] ?? '') : old('allergies') ?? ''); ?></textarea>
            </div>

            <!-- Row: medical_history -->
            <div class="form-group">
                <label class="form-label">سوابق پزشکی</label>
                <textarea name="medical_history" class="form-textarea" rows="2" placeholder="سوابق پزشکی، جراحی‌ها، داروهای مصرفی..."><?php echo e($isEdit ? ($member['medical_history'] ?? '') : old('medical_history') ?? ''); ?></textarea>
            </div>

            <!-- Row 6: membership_id, status -->
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">اشتراک</label>
                    <select name="membership_id" class="form-input">
                        <option value="">بدون اشتراک</option>
                        <?php if (!empty($memberships)): ?>
                        <?php foreach ($memberships as $m): ?>
                        <option value="<?php echo e($m['id']); ?>" <?php echo ($isEdit && isset($member['membership_id']) && $member['membership_id'] == $m['id']) || (!$isEdit && old('membership_id') == $m['id']) ? 'selected' : ''; ?>>
                            <?php echo e($m['name']); ?>
                        </option>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">وضعیت</label>
                    <select name="status" class="form-input">
                        <option value="active" <?php echo ($isEdit && $member['status'] === 'active') || (!$isEdit && old('status') === 'active') ? 'selected' : ''; ?>>فعال</option>
                        <option value="inactive" <?php echo ($isEdit && $member['status'] === 'inactive') || (!$isEdit && old('status') === 'inactive') ? 'selected' : ''; ?>>غیرفعال</option>
                    </select>
                </div>
            </div>

            <!-- Actions -->
            <div class="form-actions" style="display:flex;gap:8px;margin-top:24px;">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i>
                    <?php echo $isEdit ? 'ذخیره تغییرات' : 'ثبت عضو'; ?>
                </button>
                <a href="<?php echo url('admin/members'); ?>" class="btn btn-secondary">انصراف</a>
            </div>
        </form>
    </div>
</div>