<?php
$statusMap = [
    'active' => '<span class="badge badge-success">فعال</span>',
    'inactive' => '<span class="badge badge-warning">غیرفعال</span>',
    'expired' => '<span class="badge badge-danger">منقضی</span>',
    'suspended' => '<span class="badge badge-danger">معلق</span>',
];
$paymentStatusMap = [
    'paid' => '<span class="badge badge-success">پرداخت شده</span>',
    'pending' => '<span class="badge badge-warning">در انتظار</span>',
    'refunded' => '<span class="badge badge-danger">بازپرداخت</span>',
    'cancelled' => '<span class="badge badge-danger">لغو شده</span>',
];
$methodMap = [
    'cash' => 'نقدی',
    'card' => 'کارت',
    'transfer' => 'انتقال بانکی',
    'online' => 'آنلاین',
];
?>

<ul class="nav nav-tabs" dir="rtl" style="margin-bottom:20px;">
    <li class="nav-item">
        <a class="nav-link active" data-bs-toggle="tab" href="#tab-info">
            <i class="fas fa-user"></i> اطلاعات
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" data-bs-toggle="tab" href="#tab-medical">
            <i class="fas fa-heartbeat"></i> پزشکی
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" data-bs-toggle="tab" href="#tab-insurance">
            <i class="fas fa-shield-alt"></i> بیمه
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" data-bs-toggle="tab" href="#tab-transactions">
            <i class="fas fa-receipt"></i> تراکنش‌ها
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" data-bs-toggle="tab" href="#tab-classes">
            <i class="fas fa-dumbbell"></i> کلاس‌ها
        </a>
    </li>
</ul>

<div class="tab-content">

    <!-- ===== Tab: Info ===== -->
    <div class="tab-pane fade show active" id="tab-info">
        <div class="card" style="margin-bottom:0;border:none;">
            <div class="card-body">
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                    <div style="padding:10px;background:#f8fafc;border-radius:8px;">
                        <small style="color:#64748b;"><i class="fas fa-user" style="margin-left:4px;"></i> نام</small>
                        <p style="margin:4px 0 0;font-weight:600;"><?php echo e($member['first_name'] . ' ' . $member['last_name']); ?></p>
                    </div>
                    <div style="padding:10px;background:#f8fafc;border-radius:8px;">
                        <small style="color:#64748b;"><i class="fas fa-phone" style="margin-left:4px;"></i> تلفن</small>
                        <p style="margin:4px 0 0;font-weight:600;direction:ltr;text-align:right;"><?php echo e($member['phone']); ?></p>
                    </div>
                    <div style="padding:10px;background:#f8fafc;border-radius:8px;">
                        <small style="color:#64748b;"><i class="fas fa-envelope" style="margin-left:4px;"></i> ایمیل</small>
                        <p style="margin:4px 0 0;font-weight:600;"><?php echo e($member['email'] ?? '-'); ?></p>
                    </div>
                    <div style="padding:10px;background:#f8fafc;border-radius:8px;">
                        <small style="color:#64748b;"><i class="fas fa-id-card" style="margin-left:4px;"></i> کد ملی</small>
                        <p style="margin:4px 0 0;font-weight:600;direction:ltr;text-align:right;"><?php echo e($member['national_code'] ?? '-'); ?></p>
                    </div>
                    <div style="padding:10px;background:#f8fafc;border-radius:8px;">
                        <small style="color:#64748b;"><i class="fas fa-calendar-alt" style="margin-left:4px;"></i> تاریخ تولد</small>
                        <p style="margin:4px 0 0;font-weight:600;"><?php echo $member['birth_date'] ? e(formatDate($member['birth_date'])) : '-'; ?></p>
                    </div>
                    <div style="padding:10px;background:#f8fafc;border-radius:8px;">
                        <small style="color:#64748b;"><i class="fas fa-circle-check" style="margin-left:4px;"></i> وضعیت</small>
                        <p style="margin:4px 0 0;font-weight:600;"><?php echo $statusMap[$member['status']] ?? e($member['status']); ?></p>
                    </div>
                    <div style="padding:10px;background:#f8fafc;border-radius:8px;">
                        <small style="color:#64748b;"><i class="fas fa-award" style="margin-left:4px;"></i> اشتراک</small>
                        <p style="margin:4px 0 0;font-weight:600;"><?php echo e($activeMembership['plan_name'] ?? 'بدون اشتراک'); ?></p>
                    </div>
                    <div style="padding:10px;background:#f8fafc;border-radius:8px;">
                        <small style="color:#64748b;"><i class="fas fa-hashtag" style="margin-left:4px;"></i> شماره معرف</small>
                        <p style="margin:4px 0 0;font-weight:600;"><?php echo e($member['referral_number'] ?? '-'); ?></p>
                    </div>
                </div>
                <div style="margin-top:12px;padding:10px;background:#f8fafc;border-radius:8px;">
                    <small style="color:#64748b;"><i class="fas fa-location-dot" style="margin-left:4px;"></i> آدرس</small>
                    <p style="margin:4px 0 0;font-weight:600;"><?php echo e($member['address'] ?? '-'); ?></p>
                </div>
                <div style="margin-top:12px;display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                    <div style="padding:10px;background:#f8fafc;border-radius:8px;">
                        <small style="color:#64748b;"><i class="fas fa-phone-volume" style="margin-left:4px;"></i> تماس اضطراری</small>
                        <p style="margin:4px 0 0;font-weight:600;"><?php echo e($member['emergency_contact'] ?? '-'); ?></p>
                    </div>
                    <div style="padding:10px;background:#f8fafc;border-radius:8px;">
                        <small style="color:#64748b;"><i class="fas fa-phone-flip" style="margin-left:4px;"></i> تلفن اضطراری</small>
                        <p style="margin:4px 0 0;font-weight:600;direction:ltr;text-align:right;"><?php echo e($member['emergency_phone'] ?? '-'); ?></p>
                    </div>
                </div>
                <?php if (!empty($member['notes'])): ?>
                <div style="margin-top:12px;padding:10px;background:#fffbeb;border-radius:8px;border:1px solid #fbbf24;">
                    <small style="color:#92400e;"><i class="fas fa-sticky-note" style="margin-left:4px;"></i> یادداشت‌ها</small>
                    <p style="margin:4px 0 0;color:#78350f;"><?php echo e($member['notes']); ?></p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- ===== Tab: Medical ===== -->
    <div class="tab-pane fade" id="tab-medical">
        <div class="card" style="margin-bottom:0;border:none;">
            <div class="card-body">
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                    <div style="padding:14px;background:#fef2f2;border-radius:8px;border:1px solid #fecaca;">
                        <small style="color:#991b1b;"><i class="fas fa-droplet" style="margin-left:4px;"></i> گروه خونی</small>
                        <p style="margin:6px 0 0;font-size:1.25rem;font-weight:700;color:#dc2626;"><?php echo e($member['blood_type'] ?? 'نامشخص'); ?></p>
                    </div>
                </div>
                <div style="margin-top:12px;padding:14px;background:#f8fafc;border-radius:8px;">
                    <small style="color:#64748b;"><i class="fas fa-allergies" style="margin-left:4px;"></i> آلرژی‌ها و حساسیت‌ها</small>
                    <p style="margin:6px 0 0;font-weight:600;"><?php echo e($member['allergies'] ?? 'ثبت نشده'); ?></p>
                </div>
                <div style="margin-top:12px;padding:14px;background:#f8fafc;border-radius:8px;">
                    <small style="color:#64748b;"><i class="fas fa-file-medical" style="margin-left:4px;"></i> سوابق پزشکی</small>
                    <p style="margin:6px 0 0;font-weight:600;"><?php echo e($member['medical_history'] ?? 'ثبت نشده'); ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- ===== Tab: Insurance ===== -->
    <div class="tab-pane fade" id="tab-insurance">
        <div class="card" style="margin-bottom:0;border:none;">
            <div class="card-body">
                <?php if ($insurance): ?>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                    <div style="padding:10px;background:#f0fdf4;border-radius:8px;border:1px solid #bbf7d0;">
                        <small style="color:#166534;"><i class="fas fa-shield-halved" style="margin-left:4px;"></i> نوع بیمه</small>
                        <p style="margin:4px 0 0;font-weight:700;color:#15803d;"><?php echo e($insurance['insurance_type'] ?? 'بیمه ورزشی'); ?></p>
                    </div>
                    <div style="padding:10px;background:#f8fafc;border-radius:8px;">
                        <small style="color:#64748b;"><i class="fas fa-circle-check" style="margin-left:4px;"></i> وضعیت</small>
                        <p style="margin:4px 0 0;font-weight:600;">
                            <?php if ($insurance['status'] === 'active' && $insurance['end_date'] >= date('Y-m-d')): ?>
                            <span class="badge badge-success">فعال</span>
                            <?php else: ?>
                            <span class="badge badge-danger">غیرفعال / منقضی</span>
                            <?php endif; ?>
                        </p>
                    </div>
                    <div style="padding:10px;background:#f8fafc;border-radius:8px;">
                        <small style="color:#64748b;"><i class="fas fa-hashtag" style="margin-left:4px;"></i> شماره بیمه‌نامه</small>
                        <p style="margin:4px 0 0;font-weight:600;"><?php echo e($insurance['policy_number'] ?? '-'); ?></p>
                    </div>
                    <div style="padding:10px;background:#f8fafc;border-radius:8px;">
                        <small style="color:#64748b;"><i class="fas fa-coins" style="margin-left:4px;"></i> حق بیمه</small>
                        <p style="margin:4px 0 0;font-weight:600;"><?php echo formatCurrency($insurance['premium_amount'] ?? 0); ?></p>
                    </div>
                    <div style="padding:10px;background:#f8fafc;border-radius:8px;">
                        <small style="color:#64748b;"><i class="fas fa-calendar-check" style="margin-left:4px;"></i> تاریخ شروع</small>
                        <p style="margin:4px 0 0;font-weight:600;"><?php echo formatDate($insurance['start_date']); ?></p>
                    </div>
                    <div style="padding:10px;background:#f8fafc;border-radius:8px;">
                        <small style="color:#64748b;"><i class="fas fa-calendar-xmark" style="margin-left:4px;"></i> تاریخ پایان</small>
                        <p style="margin:4px 0 0;font-weight:600;"><?php echo formatDate($insurance['end_date']); ?></p>
                    </div>
                    <?php if (!empty($insurance['document_path'])): ?>
                    <div style="padding:10px;background:#eff6ff;border-radius:8px;border:1px solid #bfdbfe;grid-column:1/-1;">
                        <small style="color:#1e40af;"><i class="fas fa-file-alt" style="margin-left:4px;"></i> فایل بیمه‌نامه</small>
                        <p style="margin:4px 0 0;font-weight:600;">
                            <a href="<?php echo asset($insurance['document_path']); ?>" target="_blank" class="btn btn-primary btn-xs" style="margin-top:4px;">
                                <i class="fas fa-download"></i> دانلود فایل
                            </a>
                        </p>
                    </div>
                    <?php endif; ?>
                </div>
                <?php else: ?>
                <div class="text-center py-5">
                    <i class="fas fa-shield-halved" style="font-size:48px;color:#cbd5e1;"></i>
                    <h5 style="margin-top:12px;color:#64748b;">بیمه فعالی یافت نشد</h5>
                    <p style="color:#94a3b8;">این عضو در حال حاضر بیمه فعال ندارد.</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- ===== Tab: Transactions ===== -->
    <div class="tab-pane fade" id="tab-transactions">
        <div class="card" style="margin-bottom:0;border:none;">
            <div class="card-body" style="padding:0;">
                <!-- Balance Summary -->
                <div style="padding:14px 16px;border-bottom:1px solid #e2e8f0;display:flex;align-items:center;justify-content:space-between;">
                    <span style="font-weight:600;color:#334155;">
                        <i class="fas fa-wallet" style="margin-left:4px;"></i> مانده حساب
                    </span>
                    <span style="font-size:1.1rem;font-weight:700;color:<?php echo $balance > 0 ? '#16a34a' : ($balance < 0 ? '#dc2626' : '#64748b'); ?>;">
                        <?php echo formatCurrency(abs($balance)); ?>
                        <?php if ($balance > 0): ?>
                            <small style="font-size:0.75rem;">(بستانکار)</small>
                        <?php elseif ($balance < 0): ?>
                            <small style="font-size:0.75rem;">(بدهکار)</small>
                        <?php else: ?>
                            <small style="font-size:0.75rem;">(مطابق)</small>
                        <?php endif; ?>
                    </span>
                </div>
                <?php if (!empty($payments)): ?>
                <table style="width:100%;border-collapse:collapse;font-size:0.85rem;">
                    <thead>
                        <tr style="background:#f1f5f9;">
                            <th style="padding:10px 12px;text-align:right;font-weight:600;color:#475569;">تاریخ</th>
                            <th style="padding:10px 12px;text-align:right;font-weight:600;color:#475569;">مبلغ</th>
                            <th style="padding:10px 12px;text-align:right;font-weight:600;color:#475569;">روش</th>
                            <th style="padding:10px 12px;text-align:right;font-weight:600;color:#475569;">وضعیت</th>
                            <th style="padding:10px 12px;text-align:right;font-weight:600;color:#475569;">توضیحات</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $runningBalance = 0;
                        foreach ($payments as $p):
                            if ($p['type'] === 'credit' || $p['payment_type'] === 'income') {
                                $runningBalance += (float)$p['amount'];
                            } else {
                                $runningBalance -= (float)$p['amount'];
                            }
                        ?>
                        <tr style="border-bottom:1px solid #f1f5f9;">
                            <td style="padding:10px 12px;"><?php echo formatDateTime($p['payment_date']); ?></td>
                            <td style="padding:10px 12px;font-weight:600;direction:ltr;text-align:right;">
                                <?php echo formatCurrency($p['amount']); ?>
                            </td>
                            <td style="padding:10px 12px;"><?php echo e($methodMap[$p['payment_method']] ?? $p['payment_method']); ?></td>
                            <td style="padding:10px 12px;"><?php echo $paymentStatusMap[$p['status']] ?? e($p['status']); ?></td>
                            <td style="padding:10px 12px;color:#64748b;max-width:180px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="<?php echo e($p['description'] ?? ''); ?>">
                                <?php echo e($p['description'] ?? '-'); ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php else: ?>
                <div class="text-center py-5">
                    <i class="fas fa-receipt" style="font-size:48px;color:#cbd5e1;"></i>
                    <h5 style="margin-top:12px;color:#64748b;">تراکنشی یافت نشد</h5>
                    <p style="color:#94a3b8;">هنوز تراکنشی برای این عضو ثبت نشده است.</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- ===== Tab: Classes ===== -->
    <div class="tab-pane fade" id="tab-classes">
        <div class="card" style="margin-bottom:0;border:none;">
            <div class="card-body" style="padding:0;">
                <?php if (!empty($classRegistrations)): ?>
                <table style="width:100%;border-collapse:collapse;font-size:0.85rem;">
                    <thead>
                        <tr style="background:#f1f5f9;">
                            <th style="padding:10px 12px;text-align:right;font-weight:600;color:#475569;">نام کلاس</th>
                            <th style="padding:10px 12px;text-align:right;font-weight:600;color:#475569;">روز</th>
                            <th style="padding:10px 12px;text-align:right;font-weight:600;color:#475569;">ساعت</th>
                            <th style="padding:10px 12px;text-align:right;font-weight:600;color:#475569;">مربی</th>
                            <th style="padding:10px 12px;text-align:right;font-weight:600;color:#475569;">تاریخ ثبت‌نام</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($classRegistrations as $cr): ?>
                        <tr style="border-bottom:1px solid #f1f5f9;">
                            <td style="padding:10px 12px;font-weight:600;">
                                <i class="fas fa-dumbbell" style="color:#3b82f6;margin-left:4px;"></i>
                                <?php echo e($cr['class_name']); ?>
                            </td>
                            <td style="padding:10px 12px;"><?php echo e($cr['schedule_day']); ?></td>
                            <td style="padding:10px 12px;direction:ltr;text-align:right;"><?php echo e($cr['schedule_time']); ?></td>
                            <td style="padding:10px 12px;">
                                <?php echo e(($cr['coach_first_name'] ?? '') . ' ' . ($cr['coach_last_name'] ?? '')) ?: '-'; ?>
                            </td>
                            <td style="padding:10px 12px;"><?php echo formatDateTime($cr['registration_date']); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php else: ?>
                <div class="text-center py-5">
                    <i class="fas fa-dumbbell" style="font-size:48px;color:#cbd5e1;"></i>
                    <h5 style="margin-top:12px;color:#64748b;">کلاسی ثبت نشده</h5>
                    <p style="color:#94a3b8;">این عضو در حال حاضر در هیچ کلاسی ثبت‌نام نکرده است.</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

</div>