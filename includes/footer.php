<?php
// includes/footer.php
?>
    </div>
</main>

<?php if (empty($hideFooter)): ?>
<footer class="border-t border-slate-200 bg-white mt-8">
    <div class="max-w-6xl mx-auto px-4 py-8 grid gap-6 md:grid-cols-4 text-xs text-slate-600">
        <!-- Cột 1: Logo + giới thiệu -->
        <div class="md:col-span-1">
            <div class="flex items-center gap-2 mb-3">
                <span class="w-3 h-3 rounded-full bg-[#7AE582] shadow-[0_0_10px_#7AE582]"></span>
                <span class="font-semibold tracking-wide text-sm text-slate-800">
                    English Learning
                </span>
            </div>
            <p class="text-[0.75rem] leading-relaxed">
                Hệ thống học tiếng Anh miễn phí với từ vựng, flashcard, bài luyện tập nghe – nhìn – điền từ.
                Phù hợp cho người mới bắt đầu đến luyện thi.
            </p>
        </div>

        <!-- Cột 2: Liên kết nhanh -->
        <div>
            <h3 class="text-[0.8rem] font-semibold text-slate-800 mb-2">Liên kết nhanh</h3>
            <ul class="space-y-1">
                <li><a href="/index.php" class="hover:text-[#16a34a]">Trang chủ</a></li>
                <li><a href="/user/learn.php" class="hover:text-[#16a34a]">Lộ trình Level</a></li>
                <li><a href="/user/flashcard.php?level_id=1" class="hover:text-[#16a34a]">Flashcard (demo)</a></li>
                <li><a href="/user/practice.php?level_id=1" class="hover:text-[#16a34a]">Practice (demo)</a></li>
                <li><a href="/auth/login.php" class="hover:text-[#16a34a]">Đăng nhập</a></li>
                <li><a href="/auth/register.php" class="hover:text-[#16a34a]">Đăng ký</a></li>
            </ul>
        </div>

        <!-- Cột 3: Thông tin liên hệ -->
        <div>
            <h3 class="text-[0.8rem] font-semibold text-slate-800 mb-2">Liên hệ</h3>
            <ul class="space-y-1">
                <li class="flex items-start gap-2">
                    <i class="fa-solid fa-envelope mt-[2px] text-slate-400"></i>
                    <span>zayluon@gmail.com</span>
                </li>
                <li class="flex items-start gap-2">
                    <i class="fa-solid fa-phone mt-[2px] text-slate-400"></i>
                    <span>+84 364 132 169</span>
                </li>
                <li class="flex items-start gap-2">
                    <i class="fa-solid fa-location-dot mt-[2px] text-slate-400"></i>
                    <span>Hồ Chí Minh, Việt Nam</span>
                </li>
            </ul>

            <div class="flex gap-3 mt-3 text-slate-400">
                <a href="#" class="hover:text-[#16a34a]"><i class="fa-brands fa-facebook-f"></i></a>
                <a href="#" class="hover:text-[#16a34a]"><i class="fa-brands fa-youtube"></i></a>
                <a href="#" class="hover:text-[#16a34a]"><i class="fa-brands fa-tiktok"></i></a>
            </div>
        </div>

        <!-- Cột 4: Bản đồ -->
        <div>
            <h3 class="text-[0.8rem] font-semibold text-slate-800 mb-2">Bản đồ</h3>
            <div class="h-32 sm:h-40 rounded-lg overflow-hidden border border-slate-200">
                <iframe
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3919.9616486333266!2d106.67510897589445!3d10.737439289408972!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31752f62a90e5dbd%3A0x674d5126513db295!2zVHLGsOG7nW5nIMSQ4bqhaSBo4buNYyBDw7RuZyBuZ2jhu4cgU8OgaSBHw7Ju!5e0!3m2!1svi!2s!4v1765099328419!5m2!1svi!2s"
                    width="100%"
                    height="100%"
                    style="border:0;"
                    allowfullscreen=""
                    loading="lazy"
                    referrerpolicy="no-referrer-when-downgrade">
                </iframe>
            </div>
        </div>
    </div>

    <div class="border-t border-slate-100">
        <div class="max-w-6xl mx-auto px-4 py-3 flex flex-col sm:flex-row items-center justify-between gap-2">
            <p class="text-[0.7rem] text-slate-400">
                &copy; <?= date('Y') ?> English Learning System. Built with Ludwig.
            </p>
            <p class="text-[0.7rem] text-slate-400">
                <i class="fa-solid fa-heart text-[#7AE582] mr-1"></i>
                Học mỗi ngày một chút là tiến bộ.
            </p>
        </div>
    </div>
</footer>
<?php endif; ?>

<script src="/assets/js/app.js"></script>
</body>
</html>
