<!-- Welcome Card -->
<div class="card" style="margin-bottom:24px;background:linear-gradient(135deg,#3B82F6 0%,#8B5CF6 100%);border:none;">
    <div class="card-body" style="padding:24px 28px;">
        <div style="display:flex;align-items:center;gap:20px;">
            <div style="width:56px;height:56px;border-radius:50%;background:rgba(255,255,255,0.2);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <i class="fas fa-user" style="color:#fff;font-size:1.5rem;"></i>
            </div>
            <div>
                <h2 style="margin:0 0 4px 0;color:#fff;font-size:1.25rem;font-weight:700;">
                    <?php echo e($member['first_name'] ?? '') . ' ' . e($member['last_name'] ?? ''); ?> عزیز، خوش آمدید
                </h2>
                <p style="margin:0;color:rgba(255,255,255,0.8);font-size:0.9rem;">خلاصه وضعیت حساب شما را در اینجا مشاهده کنید.</p>
            </div>
        </div>
    </div>
</div>

<!-- Slider Section (Active Sliders for Members) -->
<?php
$sliderController = new SliderController();
ob_start();
$sliderController->getActiveSliders();
$sliderJson = ob_get_clean();
$sliderData = json_decode($sliderJson, true);
$activeSliders = $sliderData['sliders'] ?? [];
?>
<?php if (!empty($activeSliders)): ?>
<div class="card" style="margin-bottom:24px;overflow:hidden;">
    <div class="card-body" style="padding:0;">
        <div id="portal-slider" class="slider-container" style="position:relative;width:100%;height:280px;overflow:hidden;border-radius:12px;">
            <?php foreach ($activeSliders as $idx => $slider): ?>
            <div class="slider-slide" style="position:absolute;top:0;left:0;width:100%;height:100%;opacity:<?php echo $idx === 0 ? '1' : '0'; ?>;transition:opacity 0.6s ease-in-out;display:flex;align-items:flex-end;">
                <img src="<?php echo asset($slider['image_path'] ?? '/public/uploads/sliders/default.svg'); ?>" alt="<?php echo e($slider['title']); ?>" style="width:100%;height:100%;object-fit:cover;position:absolute;top:0;left:0;z-index:1;">
                <div style="position:relative;z-index:2;width:100%;padding:24px;background:linear-gradient(to top, rgba(0,0,0,0.85), transparent);">
                    <h3 style="color:#fff;font-size:1.3rem;font-weight:700;margin:0 0 8px 0;"><?php echo e($slider['title']); ?></h3>
                    <?php if (!empty($slider['description'])): ?>
                    <p style="color:rgba(255,255,255,0.9);font-size:0.9rem;margin:0;"><?php echo e(mb_substr($slider['description'], 0, 120)); ?><?php echo mb_strlen($slider['description']) > 120 ? '...' : ''; ?></p>
                    <?php endif; ?>
                    <?php if (!empty($slider['link_url'])): ?>
                    <a href="<?php echo e($slider['link_url']); ?>" target="_blank" rel="noopener" style="display:inline-block;margin-top:12px;padding:8px 16px;background:#1877F2;color:#fff;text-decoration:none;border-radius:8px;font-size:0.85rem;font-weight:600;transition:background 0.2s;" onmouseover="this.style.background='#4293FF'" onmouseout="this.style.background='#1877F2'">بیشتر بخوانید</a>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
            <!-- Navigation Arrows -->
            <button type="button" id="slider-prev" style="position:absolute;left:16px;top:50%;transform:translateY(-50%);z-index:3;width:42px;height:42px;border-radius:50%;background:rgba(255,255,255,0.9);border:none;cursor:pointer;display:flex;align-items:center;justify-content:center;box-shadow:0 4px 12px rgba(0,0,0,0.2);transition:all 0.2s;" onmouseover="this.style.transform='translateY(-50%) scale(1.08)'" onmouseout="this.style.transform='translateY(-50%) scale(1)'">
                <i class="fas fa-chevron-right" style="color:#1f2937;font-size:1rem;"></i>
            </button>
            <button type="button" id="slider-next" style="position:absolute;right:16px;top:50%;transform:translateY(-50%);z-index:3;width:42px;height:42px;border-radius:50%;background:rgba(255,255,255,0.9);border:none;cursor:pointer;display:flex;align-items:center;justify-content:center;box-shadow:0 4px 12px rgba(0,0,0,0.2);transition:all 0.2s;" onmouseover="this.style.transform='translateY(-50%) scale(1.08)'" onmouseout="this.style.transform='translateY(-50%) scale(1)'">
                <i class="fas fa-chevron-left" style="color:#1f2937;font-size:1rem;"></i>
            </button>
            <!-- Dots Indicator -->
            <div id="slider-dots" style="position:absolute;bottom:16px;left:50%;transform:translateX(-50%);z-index:3;display:flex;gap:8px;">
                <?php foreach ($activeSliders as $idx => $slider): ?>
                <span class="slider-dot" data-index="<?php echo $idx; ?>" style="width:10px;height:10px;border-radius:50%;background:<?php echo $idx === 0 ? '#1877F2' : 'rgba(255,255,255,0.5)'; ?>;cursor:pointer;transition:all 0.3s;"></span>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>
<script>
(function(){
    var slides = document.querySelectorAll('#portal-slider .slider-slide');
    var dots = document.querySelectorAll('.slider-dot');
    var prevBtn = document.getElementById('slider-prev');
    var nextBtn = document.getElementById('slider-next');
    var current = 0;
    var total = slides.length;
    var autoPlayInterval;

    function showSlide(idx) {
        slides.forEach(function(slide, i) {
            slide.style.opacity = i === idx ? '1' : '0';
        });
        dots.forEach(function(dot, i) {
            dot.style.background = i === idx ? '#1877F2' : 'rgba(255,255,255,0.5)';
        });
        current = idx;
    }

    function nextSlide() {
        showSlide((current + 1) % total);
    }

    function prevSlide() {
        showSlide((current - 1 + total) % total);
    }

    // Auto-play every 5 seconds
    function startAutoPlay() {
        autoPlayInterval = setInterval(nextSlide, 5000);
    }

    function stopAutoPlay() {
        if (autoPlayInterval) clearInterval(autoPlayInterval);
    }

    // Event listeners
    if (nextBtn) nextBtn.addEventListener('click', function() { stopAutoPlay(); nextSlide(); startAutoPlay(); });
    if (prevBtn) prevBtn.addEventListener('click', function() { stopAutoPlay(); prevSlide(); startAutoPlay(); });
    
    dots.forEach(function(dot) {
        dot.addEventListener('click', function() {
            stopAutoPlay();
            showSlide(parseInt(this.getAttribute('data-index')));
            startAutoPlay();
        });
    });

    // Pause on hover
    var sliderContainer = document.getElementById('portal-slider');
    if (sliderContainer) {
        sliderContainer.addEventListener('mouseenter', stopAutoPlay);
        sliderContainer.addEventListener('mouseleave', startAutoPlay);
    }

    // Initialize
    startAutoPlay();
})();
</script>
<?php endif; ?>

<!-- Summary Cards -->
<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:16px;margin-bottom:24px;">
    <!-- اشتراک فعال -->
    <div class="card" style="margin-bottom:0;">
        <div class="card-body" style="padding:20px;">
            <div style="display:flex;align-items:center;justify-content:space-between;">
                <div>
                    <?php if (!empty($membership)): ?>
                        <div style="font-size:1.1rem;font-weight:700;color:#1f2937;margin-bottom:4px;"><?php echo e($membership['plan_name'] ?? ''); ?></div>
                        <div style="font-size:0.8rem;color:#6B7A8D;">تا <?php echo formatDate($membership['end_date'] ?? ''); ?></div>
                    <?php else: ?>
                        <div style="font-size:1.1rem;font-weight:700;color:#F59E0B;margin-bottom:4px;">بدون اشتراک</div>
                        <div style="font-size:0.8rem;color:#6B7A8D;">اشتراکی فعال ندارید</div>
                    <?php endif; ?>
                    <div style="font-size:0.8rem;color:#9CA3AF;margin-top:8px;">
                        <i class="fas fa-id-card" style="margin-left:4px;"></i>
                        اشتراک فعال
                    </div>
                </div>
                <div style="width:48px;height:48px;border-radius:12px;background:<?php echo !empty($membership) ? '#10B98115' : '#F59E0B15'; ?>;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <i class="fas fa-credit-card" style="font-size:1.2rem;color:<?php echo !empty($membership) ? '#10B981' : '#F59E0B'; ?>;"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- کلاس‌های ثبت‌نام شده -->
    <div class="card" style="margin-bottom:0;">
        <div class="card-body" style="padding:20px;">
            <div style="display:flex;align-items:center;justify-content:space-between;">
                <div>
                    <div style="font-size:1.1rem;font-weight:700;color:#1f2937;margin-bottom:4px;">
                        <?php echo $totalClassesRegistered ?? count($myClasses ?? []); ?>
                    </div>
                    <div style="font-size:0.8rem;color:#6B7A8D;">کلاس فعال</div>
                    <div style="font-size:0.8rem;color:#9CA3AF;margin-top:8px;">
                        <i class="fas fa-calendar-check" style="margin-left:4px;"></i>
                        کلاس‌های ثبت‌نام شده
                    </div>
                </div>
                <div style="width:48px;height:48px;border-radius:12px;background:#3B82F615;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <i class="fas fa-chalkboard-teacher" style="font-size:1.2rem;color:#3B82F6;"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- حضور این ماه -->
    <div class="card" style="margin-bottom:0;">
        <div class="card-body" style="padding:20px;">
            <div style="display:flex;align-items:center;justify-content:space-between;">
                <div>
                    <div style="font-size:1.1rem;font-weight:700;color:#1f2937;margin-bottom:4px;">
                        <?php echo $monthlyAttendanceCount ?? 0; ?>
                    </div>
                    <div style="font-size:0.8rem;color:#6B7A8D;">حضور در ماه جاری</div>
                    <div style="font-size:0.8rem;color:#9CA3AF;margin-top:8px;">
                        <i class="fas fa-clipboard-check" style="margin-left:4px;"></i>
                        آمار حضور
                    </div>
                </div>
                <div style="width:48px;height:48px;border-radius:12px;background:#8B5CF615;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <i class="fas fa-clipboard-check" style="font-size:1.2rem;color:#8B5CF6;"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- آخرین پرداخت -->
    <div class="card" style="margin-bottom:0;">
        <div class="card-body" style="padding:20px;">
            <div style="display:flex;align-items:center;justify-content:space-between;">
                <div>
                    <?php if (!empty($recentPayments)): ?>
                        <div style="font-size:1.1rem;font-weight:700;color:#1f2937;margin-bottom:4px;">
                            <?php echo formatCurrency($recentPayments[0]['amount'] ?? 0); ?>
                        </div>
                        <div style="font-size:0.8rem;color:#6B7A8D;">
                            <?php echo formatDate($recentPayments[0]['payment_date'] ?? $recentPayments[0]['created_at'] ?? ''); ?>
                        </div>
                    <?php else: ?>
                        <div style="font-size:1.1rem;font-weight:700;color:#9CA3AF;margin-bottom:4px;">-</div>
                        <div style="font-size:0.8rem;color:#6B7A8D;">پرداختی ثبت نشده</div>
                    <?php endif; ?>
                    <div style="font-size:0.8rem;color:#9CA3AF;margin-top:8px;">
                        <i class="fas fa-money-bill-wave" style="margin-left:4px;"></i>
                        آخرین پرداخت
                    </div>
                </div>
                <div style="width:48px;height:48px;border-radius:12px;background:#10B98115;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <i class="fas fa-wallet" style="font-size:1.2rem;color:#10B981;"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- وضعیت بیمه -->
    <div class="card" style="margin-bottom:0;">
        <div class="card-body" style="padding:20px;">
            <div style="display:flex;align-items:center;justify-content:space-between;">
                <div>
                    <?php if (!empty($insuranceStatus) && $insuranceStatus === 'active'): ?>
                        <div style="font-size:1.1rem;font-weight:700;color:#10B981;margin-bottom:4px;">فعال</div>
                        <div style="font-size:0.8rem;color:#6B7A8D;">بیمه معتبر</div>
                    <?php else: ?>
                        <div style="font-size:1.1rem;font-weight:700;color:#F59E0B;margin-bottom:4px;">بدون بیمه</div>
                        <div style="font-size:0.8rem;color:#6B7A8D;">بیمه‌ای ثبت نشده</div>
                    <?php endif; ?>
                    <div style="font-size:0.8rem;color:#9CA3AF;margin-top:8px;">
                        <i class="fas fa-shield-alt" style="margin-left:4px;"></i>
                        وضعیت بیمه
                    </div>
                </div>
                <div style="width:48px;height:48px;border-radius:12px;background:<?php echo (!empty($insuranceStatus) && $insuranceStatus === 'active') ? '#10B98115' : '#EF444415'; ?>;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <i class="fas fa-shield-alt" style="font-size:1.2rem;color:<?php echo (!empty($insuranceStatus) && $insuranceStatus === 'active') ? '#10B981' : '#EF4444'; ?>;"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Upcoming Classes -->
<div class="card" style="margin-bottom:16px;">
    <div class="card-header" style="display:flex;justify-content:space-between;align-items:center;">
        <h3 style="margin:0;font-size:0.95rem;font-weight:600;">
            <i class="fas fa-calendar-alt" style="margin-left:8px;color:#3B82F6;"></i>
            کلاس‌های آینده
        </h3>
        <a href="<?php echo url('portal/classes'); ?>" style="font-size:0.8rem;color:#3B82F6;text-decoration:none;">
            مشاهده همه <i class="fas fa-arrow-left" style="margin-right:4px;font-size:0.7rem;"></i>
        </a>
    </div>
    <div class="card-body">
        <?php if (!empty($upcomingClasses)): ?>
        <?php $shown = 0; ?>
        <div class="table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>نام کلاس</th>
                        <th>مربی</th>
                        <th>روز</th>
                        <th>ساعت</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($upcomingClasses as $cls): ?>
                    <?php if ($shown++ >= 3) break; ?>
                    <?php
                    $coachName = trim(($cls['coach_first_name'] ?? '') . ' ' . ($cls['coach_last_name'] ?? ''));
                    ?>
                    <tr>
                        <td style="font-weight:500;"><?php echo e($cls['name']); ?></td>
                        <td><?php echo e($coachName); ?></td>
                        <td><?php echo e($cls['schedule_day'] ?? $cls['day_of_week'] ?? ''); ?></td>
                        <td style="direction:ltr;text-align:right;"><?php echo e($cls['start_time']); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <div class="table-empty">
            <div class="empty-state">
                <i class="fas fa-calendar-times" style="font-size:48px;color:#D1D5DB;margin-bottom:16px;display:block;"></i>
                <h3>کلاسی یافت نشد</h3>
                <p>در حال حاضر کلاس ثبت‌نامی آینده‌ای ندارید.</p>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Recent Attendance History -->
<div class="card" style="margin-bottom:16px;">
    <div class="card-header" style="display:flex;justify-content:space-between;align-items:center;">
        <h3 style="margin:0;font-size:0.95rem;font-weight:600;">
            <i class="fas fa-clipboard-check" style="margin-left:8px;color:#8B5CF6;"></i>
            آخرین حضور و غیاب
        </h3>
        <a href="<?php echo url('portal/attendance'); ?>" style="font-size:0.8rem;color:#3B82F6;text-decoration:none;">
            مشاهده همه <i class="fas fa-arrow-left" style="margin-right:4px;font-size:0.7rem;"></i>
        </a>
    </div>
    <div class="card-body">
        <?php if (!empty($recentAttendance)): ?>
        <?php $shown = 0; ?>
        <div class="table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>تاریخ</th>
                        <th>نام کلاس</th>
                        <th class="hide-mobile">وضعیت</th>
                        <th class="hide-mobile">توضیحات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recentAttendance as $att): ?>
                    <?php if ($shown++ >= 5) break; ?>
                    <?php
                    $status = $att['attendance_status'] ?? $att['status'] ?? '';
                    $badgeClass = 'badge-secondary';
                    $statusLabel = 'نامشخص';
                    if ($status === 'present') { $badgeClass = 'badge-success'; $statusLabel = 'حاضر'; }
                    elseif ($status === 'absent') { $badgeClass = 'badge-danger'; $statusLabel = 'غایب'; }
                    elseif ($status === 'late') { $badgeClass = 'badge-warning'; $statusLabel = 'تأخیر'; }
                    elseif ($status === 'excused') { $badgeClass = 'badge-info'; $statusLabel = 'موجه'; }
                    ?>
                    <tr>
                        <td><?php echo formatDate($att['attendance_date'] ?? ''); ?></td>
                        <td style="font-weight:500;"><?php echo e($att['class_name'] ?? ''); ?></td>
                        <td class="hide-mobile"><span class="badge <?php echo $badgeClass; ?>"><?php echo $statusLabel; ?></span></td>
                        <td class="hide-mobile" style="color:#6B7A8D;font-size:0.85rem;"><?php echo e($att['notes'] ?? '-'); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <div class="table-empty">
            <div class="empty-state">
                <i class="fas fa-clipboard-check" style="font-size:48px;color:#D1D5DB;margin-bottom:16px;display:block;"></i>
                <h3>رکوردی یافت نشد</h3>
                <p>هنوز سابقه حضور و غیابی ثبت نشده است.</p>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Recent Payments -->
<div class="card">
    <div class="card-header" style="display:flex;justify-content:space-between;align-items:center;">
        <h3 style="margin:0;font-size:0.95rem;font-weight:600;">
            <i class="fas fa-receipt" style="margin-left:8px;color:#10B981;"></i>
            آخرین پرداخت‌ها
        </h3>
        <a href="<?php echo url('portal/payments'); ?>" style="font-size:0.8rem;color:#3B82F6;text-decoration:none;">
            مشاهده همه <i class="fas fa-arrow-left" style="margin-right:4px;font-size:0.7rem;"></i>
        </a>
    </div>
    <div class="card-body">
        <?php if (!empty($recentPayments)): ?>
        <?php $shown = 0; ?>
        <div class="table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>مبلغ</th>
                        <th>روش</th>
                        <th>تاریخ</th>
                        <th>وضعیت</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recentPayments as $payment): ?>
                    <?php if ($shown++ >= 3) break; ?>
                    <?php
                    $method = $payment['method'] ?? '';
                    $methodLabel = e($method);
                    if ($method === 'cash') $methodLabel = 'نقدی';
                    elseif ($method === 'card') $methodLabel = 'کارت به کارت';
                    elseif ($method === 'online') $methodLabel = 'آنلاین';
                    ?>
                    <tr>
                        <td style="font-weight:600;"><?php echo formatCurrency($payment['amount']); ?></td>
                        <td><?php echo $methodLabel; ?></td>
                        <td><?php echo formatDate($payment['payment_date'] ?? $payment['created_at'] ?? ''); ?></td>
                        <td>
                            <?php
                            $status = $payment['status'] ?? '';
                            if ($status === 'paid') echo '<span class="badge badge-success">پرداخت شده</span>';
                            elseif ($status === 'pending') echo '<span class="badge badge-warning">در انتظار</span>';
                            elseif ($status === 'refunded') echo '<span class="badge badge-info">بازپرداخت شده</span>';
                            elseif ($status === 'cancelled') echo '<span class="badge badge-danger">لغو شده</span>';
                            else echo '<span class="badge badge-secondary">' . e($status) . '</span>';
                            ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <div class="table-empty">
            <div class="empty-state">
                <i class="fas fa-money-bill-wave" style="font-size:48px;color:#D1D5DB;margin-bottom:16px;display:block;"></i>
                <h3>پرداختی یافت نشد</h3>
                <p>هنوز پرداختی ثبت نشده است.</p>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>